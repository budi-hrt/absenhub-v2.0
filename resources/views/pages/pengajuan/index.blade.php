<?php

use App\Models\PengajuanAbsen;
use App\Models\JatahCuti;
use App\Models\Karyawan;
use App\Models\Absen;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Layout('layouts.app')] #[Title('Kelola Pengajuan')] class extends Component {
    use Toast, WithPagination, WithFileUploads;

    // Filter properties
    public string $filterStatus = '';
    public string $search = '';

    // Detail modal
    public bool $showModal = false;
    public ?PengajuanAbsen $selectedPengajuan = null;
    public string $alasan_tolak = '';

    // Buat pengajuan modal
    public bool $showCreateModal = false;
    public ?int $karyawan_id = null;
    public string $jenis = '';
    public array $tanggal = [];
    public string $keterangan = '';
    public $lampiran = null;
    public ?int $selectedKaryawanSisaCuti = null;

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedKaryawanId($value)
    {
        if ($value) {
            $this->selectedKaryawanSisaCuti = JatahCuti::sisaByKaryawan((int) $value);
        } else {
            $this->selectedKaryawanSisaCuti = null;
        }
    }

    public function updatedJenis()
    {
        // Refresh sisa cuti saat jenis berubah
        if ($this->karyawan_id) {
            $this->selectedKaryawanSisaCuti = JatahCuti::sisaByKaryawan((int) $this->karyawan_id);
        }
    }

    public function tambahTanggal()
    {
        $this->tanggal[] = '';
    }

    public function hapusTanggal(int $index)
    {
        unset($this->tanggal[$index]);
        $this->tanggal = array_values($this->tanggal);
    }

    // --- Buat Pengajuan ---
    public function openCreateModal()
    {
        $this->reset(['karyawan_id', 'jenis', 'tanggal', 'keterangan', 'lampiran', 'selectedKaryawanSisaCuti']);
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function submitPengajuan()
    {
        $this->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'jenis' => 'required|in:Cuti,Izin,Sakit',
            'tanggal' => 'required|array|min:1',
            'tanggal.*' => 'date',
            'keterangan' => 'nullable|string|max:500',
            'lampiran' => 'nullable|image|max:2048',
        ], [
            'karyawan_id.required' => 'Pilih karyawan terlebih dahulu.',
            'jenis.required' => 'Pilih jenis pengajuan.',
            'tanggal.required' => 'Pilih setidaknya satu tanggal.',
            'tanggal.min' => 'Pilih setidaknya satu tanggal.',
            'tanggal.*.date' => 'Format tanggal tidak valid.',
        ]);

        $jumlahHari = count($this->tanggal);

        // Cek sisa cuti jika jenis = Cuti
        if ($this->jenis === 'Cuti') {
            $sisaCuti = JatahCuti::sisaByKaryawan((int) $this->karyawan_id);
            if ($jumlahHari > $sisaCuti) {
                $this->error("Sisa cuti karyawan tidak mencukupi. Sisa: {$sisaCuti} hari, diajukan: {$jumlahHari} hari.");
                return;
            }
        }

        // Cek tumpang tindih
        $existingDates = PengajuanAbsen::where('karyawan_id', $this->karyawan_id)
            ->where('status', '!=', 'Ditolak')
            ->get()
            ->pluck('tanggal')
            ->flatten()
            ->toArray();
            
        $bentrok = collect($this->tanggal)->intersect($existingDates)->isNotEmpty();

        if ($bentrok) {
            $this->error('Sebagian atau seluruh tanggal yang dipilih sudah ada pengajuan.');
            return;
        }

        // Upload lampiran
        $lampiranPath = null;
        if ($this->lampiran) {
            $lampiranPath = $this->lampiran->store('lampiran-pengajuan', 'public');
        }

        PengajuanAbsen::create([
            'karyawan_id' => $this->karyawan_id,
            'jenis' => $this->jenis,
            'tanggal' => $this->tanggal,
            'keterangan' => $this->keterangan,
            'lampiran' => $lampiranPath,
            'status' => 'Menunggu',
            'admin_id' => auth()->id(),
        ]);

        $this->showCreateModal = false;
        $this->reset(['karyawan_id', 'jenis', 'tanggal', 'keterangan', 'lampiran', 'selectedKaryawanSisaCuti']);
        $this->success('Pengajuan berhasil dibuat atas nama karyawan.');
    }

    // --- Kelola Pengajuan ---
    public function lihatDetail(int $id)
    {
        $this->selectedPengajuan = PengajuanAbsen::with(['karyawan.jabatan'])->findOrFail($id);
        $this->alasan_tolak = '';
        $this->showModal = true;
    }

    public function setujui(int $id)
    {
        $pengajuan = PengajuanAbsen::findOrFail($id);

        if ($pengajuan->status !== 'Menunggu') {
            $this->error('Pengajuan ini sudah diproses.');
            return;
        }

        // Cek sisa cuti jika jenis Cuti (tanpa hitung menunggu, karena pengajuan ini sendiri berstatus menunggu)
        if ($pengajuan->jenis === 'Cuti') {
            $sisaCuti = JatahCuti::sisaByKaryawan($pengajuan->karyawan_id, includeMenunggu: false);
            if ($pengajuan->jumlah_hari > $sisaCuti) {
                $this->error("Sisa cuti karyawan tidak mencukupi. Sisa: {$sisaCuti} hari.");
                return;
            }
        }

        $pengajuan->update([
            'status' => 'Disetujui',
            'admin_id' => auth()->id(),
        ]);

        // Otomatis buat record absen untuk setiap hari pengajuan
        $tanggalArray = $pengajuan->tanggal ?? [];

        foreach ($tanggalArray as $tgl) {
            Absen::updateOrCreate(
                [
                    'karyawan_id' => $pengajuan->karyawan_id,
                    'tanggal_absen' => Carbon::parse($tgl)->toDateString(),
                ],
                [
                    'keterangan' => $pengajuan->jenis,
                    'scan_in' => null,
                    'scan_out' => null,
                ]
            );
        }

        $this->showModal = false;
        $this->success('Pengajuan disetujui dan data absen berhasil dicatat.');
    }

    public function tolak(int $id)
    {
        $pengajuan = PengajuanAbsen::findOrFail($id);

        if ($pengajuan->status !== 'Menunggu') {
            $this->error('Pengajuan ini sudah diproses.');
            return;
        }

        $pengajuan->update([
            'status' => 'Ditolak',
            'admin_id' => auth()->id(),
            'alasan_tolak' => $this->alasan_tolak ?: null,
        ]);

        $this->showModal = false;
        $this->success('Pengajuan telah ditolak.');
    }

    public function hapus(int $id)
    {
        $pengajuan = PengajuanAbsen::findOrFail($id);

        if ($pengajuan->status === 'Disetujui') {
            $this->error('Tidak dapat menghapus pengajuan yang sudah disetujui.');
            return;
        }

        $pengajuan->delete();
        $this->success('Pengajuan berhasil dihapus.');
    }

    public function with(): array
    {
        $query = PengajuanAbsen::with(['karyawan.jabatan', 'admin'])
            ->orderByRaw("FIELD(status, 'Menunggu', 'Disetujui', 'Ditolak')")
            ->orderByDesc('created_at');

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->search) {
            $query->whereHas('karyawan', function ($q) {
                $q->where('nama_karyawan', 'like', "%{$this->search}%");
            });
        }

        $statusOptions = [
            ['id' => '', 'name' => 'Semua Status'],
            ['id' => 'Menunggu', 'name' => 'Menunggu'],
            ['id' => 'Disetujui', 'name' => 'Disetujui'],
            ['id' => 'Ditolak', 'name' => 'Ditolak'],
        ];

        $jenisOptions = [
            ['id' => 'Cuti', 'name' => 'Cuti'],
            ['id' => 'Izin', 'name' => 'Izin'],
            ['id' => 'Sakit', 'name' => 'Sakit'],
        ];

        // Untuk statistik
        $menungguCount = PengajuanAbsen::where('status', 'Menunggu')->count();
        $disetujuiCount = PengajuanAbsen::where('status', 'Disetujui')->count();
        $ditolakCount = PengajuanAbsen::where('status', 'Ditolak')->count();

        // List karyawan aktif untuk form buat pengajuan
        $karyawanList = Karyawan::where('is_active', true)
            ->orderBy('nama_karyawan')
            ->get()
            ->map(fn($k) => [
                'id' => $k->id,
                'name' => $k->nama_karyawan . ' — ' . ($k->jabatan?->nama_jabatan ?? 'Tanpa Jabatan'),
            ]);

        return [
            'pengajuans' => $query->paginate(15),
            'statusOptions' => $statusOptions,
            'jenisOptions' => $jenisOptions,
            'menungguCount' => $menungguCount,
            'disetujuiCount' => $disetujuiCount,
            'ditolakCount' => $ditolakCount,
            'karyawanList' => $karyawanList,
        ];
    }
}; ?>

<div>
    <x-header title="Kelola Pengajuan" separator progress-indicator>
        <x-slot:actions>
            <x-button wire:click="openCreateModal" label="Buat Pengajuan" icon="o-plus" class="btn-primary shadow-sm" />
        </x-slot:actions>
    </x-header>

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-base-100 p-5 rounded-2xl border border-base-300 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-warning/10 flex items-center justify-center shrink-0">
                    <x-icon name="o-clock" class="w-6 h-6 text-warning" />
                </div>
                <div>
                    <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Menunggu</p>
                    <p class="text-2xl font-bold text-warning">{{ $menungguCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-base-100 p-5 rounded-2xl border border-base-300 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-success/10 flex items-center justify-center shrink-0">
                    <x-icon name="o-check-circle" class="w-6 h-6 text-success" />
                </div>
                <div>
                    <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Disetujui</p>
                    <p class="text-2xl font-bold text-success">{{ $disetujuiCount }}</p>
                </div>
            </div>
        </div>

        <div class="bg-base-100 p-5 rounded-2xl border border-base-300 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-error/10 flex items-center justify-center shrink-0">
                    <x-icon name="o-x-circle" class="w-6 h-6 text-error" />
                </div>
                <div>
                    <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Ditolak</p>
                    <p class="text-2xl font-bold text-error">{{ $ditolakCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <x-input label="Cari Nama Karyawan" wire:model.live.debounce.300ms="search" icon="o-magnifying-glass" clearable />
        <x-select label="Filter Status" wire:model.live="filterStatus" :options="$statusOptions" option-value="id" option-label="name" />
    </div>

    {{-- Tabel Pengajuan --}}
    <x-card class="border border-base-300 shadow-sm">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Durasi</th>
                        <th>Status</th>
                        <th>Diajukan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengajuans as $p)
                        @php
                            $statusBadge = match($p->status) {
                                'Menunggu' => 'badge-warning',
                                'Disetujui' => 'badge-success',
                                'Ditolak' => 'badge-error',
                                default => 'badge-ghost',
                            };
                            $jenisBadge = match($p->jenis) {
                                'Cuti' => 'badge-primary',
                                'Izin' => 'badge-info',
                                'Sakit' => 'badge-error badge-outline',
                                default => 'badge-ghost',
                            };
                        @endphp
                        <tr class="hover {{ $p->status === 'Menunggu' ? 'bg-warning/5' : '' }}">
                            <td>
                                <div class="font-bold">{{ $p->karyawan?->nama_karyawan ?? '-' }}</div>
                                <div class="text-xs text-base-content/50">{{ $p->karyawan?->jabatan?->nama_jabatan ?? '-' }}</div>
                            </td>
                            <td><span class="badge {{ $jenisBadge }} badge-sm">{{ $p->jenis }}</span></td>
                            <td class="text-sm max-w-xs truncate">
                                @if(is_array($p->tanggal) && count($p->tanggal) > 0)
                                    @foreach($p->tanggal as $idx => $tgl)
                                        {{ Carbon::parse($tgl)->locale('id')->isoFormat('D MMM Y') }}@if(!$loop->last), @endif
                                        @if($idx == 2 && count($p->tanggal) > 3) <span class="text-xs opacity-50">(+{{ count($p->tanggal) - 3 }} hari)</span> @break @endif
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                            <td class="font-bold">{{ $p->jumlah_hari }} hari</td>
                            <td><span class="badge {{ $statusBadge }} badge-sm">{{ $p->status }}</span></td>
                            <td class="text-xs text-base-content/50">
                                {{ Carbon::parse($p->created_at)->locale('id')->isoFormat('D MMM Y') }}
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <x-button wire:click="lihatDetail({{ $p->id }})" icon="o-eye" class="btn-sm btn-ghost text-primary" spinner tooltip="Detail" />
                                    @if($p->status !== 'Disetujui')
                                        <x-button wire:click="hapus({{ $p->id }})" wire:confirm="Yakin ingin menghapus pengajuan ini?" icon="o-trash" class="btn-sm btn-ghost text-error" spinner tooltip="Hapus" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-base-content/50">
                                <x-icon name="o-inbox" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                                Belum ada pengajuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $pengajuans->links() }}
        </div>
    </x-card>

    {{-- Modal Buat Pengajuan --}}
    <x-modal wire:model="showCreateModal" title="Buat Pengajuan Baru" subtitle="Ajukan cuti/izin/sakit atas nama karyawan" box-class="max-w-xl">
        <form wire:submit="submitPengajuan" class="space-y-4">
            {{-- Pilih Karyawan --}}
            <x-select
                label="Karyawan"
                wire:model.live="karyawan_id"
                :options="$karyawanList"
                option-value="id"
                option-label="name"
                placeholder="Pilih karyawan..."
                icon="o-user"
                searchable
            />

            {{-- Info Sisa Cuti --}}
            @if($karyawan_id && $selectedKaryawanSisaCuti !== null)
                <div class="bg-info/10 text-info p-3 rounded-xl text-sm flex items-center gap-2">
                    <x-icon name="o-information-circle" class="w-5 h-5 shrink-0" />
                    <span>Sisa cuti karyawan ini: <strong>{{ $selectedKaryawanSisaCuti }} hari</strong> (tahun {{ now()->year }})</span>
                </div>
            @endif

            {{-- Jenis Pengajuan --}}
            <x-select
                label="Jenis Pengajuan"
                wire:model.live="jenis"
                :options="$jenisOptions"
                option-value="id"
                option-label="name"
                placeholder="Pilih jenis..."
                icon="o-document-text"
            />

            {{-- Tanggal --}}
            <div>
                <label class="label"><span class="label-text font-semibold">Daftar Tanggal <span class="text-error">*</span></span></label>
                <div class="space-y-2">
                    @forelse($tanggal as $index => $tgl)
                        <div class="flex gap-2 items-start">
                            <div class="flex-1">
                                <x-input wire:model="tanggal.{{ $index }}" type="date" icon="o-calendar" />
                            </div>
                            <x-button wire:click="hapusTanggal({{ $index }})" icon="o-trash" class="btn-error btn-outline" tooltip="Hapus" />
                        </div>
                    @empty
                        <div class="text-sm text-base-content/50 italic mb-2">Belum ada tanggal dipilih.</div>
                    @endforelse
                </div>
                <x-button wire:click="tambahTanggal" label="Tambah Tanggal" icon="o-plus" class="btn-sm btn-ghost mt-2" />
                @error('tanggal') <span class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Keterangan --}}
            <x-textarea
                label="Keterangan / Alasan"
                wire:model="keterangan"
                placeholder="Tulis alasan pengajuan..."
                rows="3"
            />

            {{-- Lampiran --}}
            <x-file
                label="Lampiran (Opsional)"
                wire:model="lampiran"
                accept="image/*"
                hint="Maks 2MB. Format: JPG, PNG."
            />

            {{-- Actions --}}
            <x-slot:actions>
                <x-button label="Batal" @click="$wire.showCreateModal = false" />
                <x-button type="submit" label="Buat Pengajuan" icon="o-paper-airplane" class="btn-primary" spinner="submitPengajuan" />
            </x-slot:actions>
        </form>
    </x-modal>

    {{-- Modal Detail --}}
    <x-modal wire:model="showModal" title="Detail Pengajuan" box-class="max-w-xl">
        @if($selectedPengajuan)
            <div class="space-y-4">
                {{-- Info Karyawan --}}
                <div class="bg-base-200/50 p-4 rounded-xl">
                    <p class="text-sm font-bold text-base-content">{{ $selectedPengajuan->karyawan?->nama_karyawan }}</p>
                    <p class="text-xs text-base-content/60">{{ $selectedPengajuan->karyawan?->jabatan?->nama_jabatan ?? '-' }}</p>
                </div>

                {{-- Info Pengajuan --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-base-200/50 p-3 rounded-xl">
                        <p class="text-[10px] uppercase text-base-content/50 font-semibold mb-0.5">Jenis</p>
                        <p class="font-bold">{{ $selectedPengajuan->jenis }}</p>
                    </div>
                    <div class="bg-base-200/50 p-3 rounded-xl">
                        <p class="text-[10px] uppercase text-base-content/50 font-semibold mb-0.5">Durasi</p>
                        <p class="font-bold">{{ $selectedPengajuan->jumlah_hari }} hari</p>
                    </div>
                    <div class="bg-base-200/50 p-3 rounded-xl col-span-2">
                        <p class="text-[10px] uppercase text-base-content/50 font-semibold mb-0.5">Daftar Tanggal</p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @if(is_array($selectedPengajuan->tanggal))
                                @foreach($selectedPengajuan->tanggal as $tgl)
                                    <span class="badge badge-neutral">{{ Carbon::parse($tgl)->locale('id')->isoFormat('D MMM Y') }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                @if($selectedPengajuan->keterangan)
                    <div class="bg-base-200/50 p-3 rounded-xl">
                        <p class="text-[10px] uppercase text-base-content/50 font-semibold mb-1">Alasan</p>
                        <p class="text-sm">{{ $selectedPengajuan->keterangan }}</p>
                    </div>
                @endif

                @if($selectedPengajuan->lampiran)
                    <div class="bg-base-200/50 p-3 rounded-xl">
                        <p class="text-[10px] uppercase text-base-content/50 font-semibold mb-2">Lampiran</p>
                        <img src="{{ Storage::url($selectedPengajuan->lampiran) }}" alt="Lampiran" class="rounded-lg max-h-64 object-contain" />
                    </div>
                @endif

                {{-- Sisa cuti info jika jenis Cuti --}}
                @if($selectedPengajuan->jenis === 'Cuti')
                    @php
                        $sisaCuti = \App\Models\JatahCuti::sisaByKaryawan($selectedPengajuan->karyawan_id);
                    @endphp
                    <div class="bg-info/10 text-info p-3 rounded-xl text-sm">
                        <x-icon name="o-information-circle" class="w-4 h-4 inline" />
                        Sisa cuti karyawan ini: <strong>{{ $sisaCuti }} hari</strong>
                    </div>
                @endif

                {{-- Tombol aksi jika masih Menunggu --}}
                @if($selectedPengajuan->status === 'Menunggu')
                    <x-textarea label="Alasan Penolakan (opsional)" wire:model="alasan_tolak" placeholder="Isi jika ingin menolak..." rows="2" />

                    <div class="flex gap-3 pt-2">
                        <x-button wire:click="setujui({{ $selectedPengajuan->id }})" wire:confirm="Yakin menyetujui pengajuan ini?" label="Setujui" icon="o-check" class="btn-success flex-1" spinner />
                        <x-button wire:click="tolak({{ $selectedPengajuan->id }})" wire:confirm="Yakin menolak pengajuan ini?" label="Tolak" icon="o-x-mark" class="btn-error btn-outline flex-1" spinner />
                    </div>
                @else
                    <div class="bg-base-200/50 p-3 rounded-xl">
                        <p class="text-[10px] uppercase text-base-content/50 font-semibold mb-1">Status</p>
                        @php
                            $sBadge = match($selectedPengajuan->status) {
                                'Disetujui' => 'badge-success',
                                'Ditolak' => 'badge-error',
                                default => 'badge-ghost',
                            };
                        @endphp
                        <span class="badge {{ $sBadge }}">{{ $selectedPengajuan->status }}</span>
                        @if($selectedPengajuan->admin)
                            <span class="text-xs text-base-content/50 ml-2">oleh {{ $selectedPengajuan->admin->name }}</span>
                        @endif
                    </div>

                    @if($selectedPengajuan->status === 'Ditolak' && $selectedPengajuan->alasan_tolak)
                        <div class="bg-error/10 text-error p-3 rounded-xl text-sm">
                            <span class="font-semibold">Alasan ditolak:</span> {{ $selectedPengajuan->alasan_tolak }}
                        </div>
                    @endif
                @endif
            </div>
        @endif
    </x-modal>
</div>
