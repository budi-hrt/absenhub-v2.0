<?php

use App\Models\Karyawan;
use App\Models\Kontrak;
use App\Models\MasaKontrak;
use App\Models\Penandatangan;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Layout('layouts.app')] #[Title('Kontrak Kerja')] class extends Component {
    use Toast, WithPagination, WithFileUploads;

    public string $search = '';
    public string $filterMasaKontrak = '';
    public string $filterJabatan = '';
    public string $filterStatus = '';

    // Modal state
    public bool $showModal = false;
    public ?Karyawan $selectedKaryawan = null;

    // Form state
    public bool $showForm = false;
    public ?int $editingId = null;

    // Form properties
    #[Validate('required|string|max:255')]
    public string $nomor = '';
    #[Validate('required|date')]
    public string $tanggal_surat = '';
    #[Validate('required|exists:penandatangans,id')]
    public ?int $penandatangan_id = null;
    #[Validate('required|exists:masa_kontraks,id')]
    public ?int $masa_kontrak_id = null;
    #[Validate('required|date')]
    public string $tanggal_mulai = '';
    #[Validate('required|date|after_or_equal:tanggal_mulai')]
    public string $tanggal_akhir = '';
    #[Validate('required|numeric|min:0')]
    public int $gaji = 0;
    #[Validate('required|numeric|min:0')]
    public int $tunjangan = 0;
    #[Validate('required|numeric|min:0')]
    public int $um_dalamkota = 0;
    #[Validate('required|numeric|min:0')]
    public int $um_luarkota = 0;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterMasaKontrak()
    {
        $this->resetPage();
    }

    public function updatingFilterJabatan()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterMasaKontrak', 'filterJabatan', 'filterStatus']);
        $this->resetPage();
    }

    public function kelolaKontrak(int $karyawanId)
    {
        $this->selectedKaryawan = Karyawan::with(['jabatan', 'kontraks' => function($q) {
            $q->orderBy('tanggal_akhir', 'desc');
        }, 'kontraks.masaKontrak', 'kontraks.penandatangan'])->findOrFail($karyawanId);
        
        $this->showModal = true;
        $this->showForm = false;
        $this->resetForm();
    }

    private function generateNomorKontrak(): string
    {
        $bulan = date('n');
        $tahun2Digit = date('y');
        
        $romawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $bulanRomawi = $romawi[$bulan];

        $lastKontrak = Kontrak::whereYear('created_at', date('Y'))
            ->orderBy('id', 'desc')
            ->first();

        $nextUrutan = 1;
        if ($lastKontrak && preg_match('/^(\d+)\/SDPA\/SP\//', $lastKontrak->nomor, $matches)) {
            $nextUrutan = (int)$matches[1] + 1;
        } else {
            $nextUrutan = Kontrak::whereYear('created_at', date('Y'))->count() + 1;
        }

        return sprintf("%03d/SDPA/SP/%s-%s", $nextUrutan, $bulanRomawi, $tahun2Digit);
    }

    public function tambahBaru()
    {
        $this->resetForm();
        $this->nomor = $this->generateNomorKontrak();
        $this->showForm = true;
    }

    public function edit(int $id)
    {
        $kontrak = Kontrak::findOrFail($id);
        $this->editingId = $id;
        
        $this->nomor = $kontrak->nomor;
        $this->tanggal_surat = Carbon::parse($kontrak->tanggal_surat)->format('Y-m-d');
        $this->penandatangan_id = $kontrak->penandatangan_id;
        $this->masa_kontrak_id = $kontrak->masa_kontrak_id;
        $this->tanggal_mulai = Carbon::parse($kontrak->tanggal_mulai)->format('Y-m-d');
        $this->tanggal_akhir = Carbon::parse($kontrak->tanggal_akhir)->format('Y-m-d');
        $this->gaji = $kontrak->gaji;
        $this->tunjangan = $kontrak->tunjangan;
        $this->um_dalamkota = $kontrak->um_dalamkota;
        $this->um_luarkota = $kontrak->um_luarkota;
        
        $this->showForm = true;
    }

    public function simpan()
    {
        $this->validate();

        $data = [
            'karyawan_id' => $this->selectedKaryawan->id,
            'nomor' => $this->nomor,
            'tanggal_surat' => $this->tanggal_surat,
            'penandatangan_id' => $this->penandatangan_id,
            'masa_kontrak_id' => $this->masa_kontrak_id,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_akhir' => $this->tanggal_akhir,
            'gaji' => $this->gaji,
            'tunjangan' => $this->tunjangan,
            'um_dalamkota' => $this->um_dalamkota,
            'um_luarkota' => $this->um_luarkota,
        ];

        if ($this->editingId) {
            $kontrak = Kontrak::findOrFail($this->editingId);
            $kontrak->update($data);
            $this->success('Kontrak berhasil diperbarui.');
        } else {
            Kontrak::create($data);
            $this->success('Kontrak baru berhasil ditambahkan.');
        }

        // Reload data karyawan
        $this->kelolaKontrak($this->selectedKaryawan->id);
    }

    public function hapus(int $id)
    {
        $kontrak = Kontrak::findOrFail($id);
        
        $kontrak->delete();
        $this->success('Kontrak berhasil dihapus.');
        
        // Reload data
        $this->kelolaKontrak($this->selectedKaryawan->id);
    }

    public function batalForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'editingId', 'nomor', 'tanggal_surat', 'penandatangan_id', 
            'masa_kontrak_id', 'tanggal_mulai', 'tanggal_akhir', 
            'gaji', 'tunjangan', 'um_dalamkota', 'um_luarkota'
        ]);
        $this->resetValidation();
    }

    public function with(): array
    {
        // Get Kontrak status ID
        $statusKontrak = Status::where('nama_status', 'like', '%Kontrak%')->first();
        $statusId = $statusKontrak ? $statusKontrak->id : 0;

        $query = Karyawan::with(['jabatan', 'kontraks' => function($q) {
            $q->orderBy('tanggal_akhir', 'desc');
        }, 'kontraks.masaKontrak'])
        ->where('is_active', true)
        ->where('status_id', $statusId);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama_karyawan', 'like', "%{$this->search}%")
                  ->orWhere('nik', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterJabatan) {
            $query->where('jabatan_id', $this->filterJabatan);
        }

        if ($this->filterMasaKontrak) {
            $query->whereHas('kontraks', function($q) {
                $q->whereIn('id', function($sub) {
                    $sub->selectRaw('MAX(id)')
                        ->from('kontraks')
                        ->groupBy('karyawan_id');
                })->where('masa_kontrak_id', $this->filterMasaKontrak);
            });
        }

        $today = Carbon::now()->startOfDay()->toDateString();
        $in30Days = Carbon::now()->startOfDay()->addDays(30)->toDateString();

        if ($this->filterStatus) {
            $query->whereHas('kontraks', function($q) use ($today, $in30Days) {
                $q->whereIn('id', function($sub) {
                    $sub->selectRaw('MAX(id)')
                        ->from('kontraks')
                        ->groupBy('karyawan_id');
                });

                if ($this->filterStatus === 'aktif') {
                    $q->where('tanggal_akhir', '>', $in30Days);
                } elseif ($this->filterStatus === 'hampir_habis') {
                    $q->whereBetween('tanggal_akhir', [$today, $in30Days]);
                } elseif ($this->filterStatus === 'kadarluarsa') {
                    $q->where('tanggal_akhir', '<', $today);
                }
            });
        }

        $statusOptions = [
            ['id' => 'aktif', 'name' => 'Aktif'],
            ['id' => 'hampir_habis', 'name' => 'Hampir Habis'],
            ['id' => 'kadarluarsa', 'name' => 'Kadarluarsa'],
        ];

        return [
            'karyawans' => $query->paginate(15),
            'penandatangans' => Penandatangan::where('is_active', true)->get(),
            'masaKontraks' => MasaKontrak::where('is_active', true)->get(),
            'jabatans' => \App\Models\Jabatan::all(),
            'statusOptions' => $statusOptions,
        ];
    }
}; ?>

<div>
    <x-header title="Data Kontrak Kerja" separator progress-indicator />

    {{-- Filters above table --}}
    <x-card class="border border-base-300 shadow-sm mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <x-input label="CARI KARYAWAN" placeholder="Nama / NIK..." wire:model.live.debounce.300ms="search" icon="o-magnifying-glass" clearable />
            
            <x-select label="MASA KONTRAK" wire:model.live="filterMasaKontrak" :options="$masaKontraks" option-value="id" option-label="status_kontrak" placeholder="Semua Masa Kontrak" clearable />
            
            <x-select label="JABATAN" wire:model.live="filterJabatan" :options="$jabatans" option-value="id" option-label="nama_jabatan" placeholder="Semua Jabatan" clearable />
            
            <x-select label="STATUS KONTRAK" wire:model.live="filterStatus" :options="$statusOptions" option-value="id" option-label="name" placeholder="Semua Status" clearable />
        </div>
    </x-card>

    {{-- Main Table --}}
    <x-card class="border border-base-300 shadow-sm mb-6 p-0">
        <div class="relative min-h-[30rem]">
            {{-- Loading Overlay for Search/Filters --}}
            <div wire:loading wire:target="search, filterMasaKontrak, filterJabatan, filterStatus, resetFilters, gotoPage, previousPage, nextPage" class="absolute inset-0 bg-base-100/30 backdrop-blur-[1px] z-50 rounded-xl transition-all duration-150">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center gap-2">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                    <span class="text-xs font-bold text-primary tracking-wider uppercase animate-pulse">Memuat...</span>
                </div>
            </div>

            <div wire:loading.class="opacity-25 pointer-events-none" wire:target="search, filterMasaKontrak, filterJabatan, filterStatus, resetFilters, gotoPage, previousPage, nextPage" class="transition-opacity duration-150 flex flex-col justify-between h-full">
                <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr class="uppercase text-xs tracking-wider">
                            <th>KARYAWAN</th>
                            <th>NO. KONTRAK TERAKHIR</th>
                            <th>MASA KONTRAK</th>
                            <th>PERIODE</th>
                            <th>STATUS / SISA</th>
                            <th class="text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($karyawans as $k)
                            @php
                                $latestContract = $k->kontraks->first();
                                $statusBadge = 'badge-ghost';
                                $sisaText = '-';
                                $sisaHari = '';
                                
                                if ($latestContract) {
                                    $tglAkhir = Carbon::parse($latestContract->tanggal_akhir)->startOfDay();
                                    $hariIni = Carbon::now()->startOfDay();
                                    $diff = $hariIni->diffInDays($tglAkhir, false);
                                    
                                    if ($hariIni->gt($tglAkhir)) {
                                        $statusBadge = 'badge-error';
                                        $sisaText = 'Kadarluarsa';
                                        $sisaHari = abs($diff) . ' hari lalu';
                                    } elseif ($diff <= 30) {
                                        $statusBadge = 'badge-warning';
                                        $sisaText = 'Hampir Habis';
                                        $sisaHari = $diff . ' hari lagi';
                                    } else {
                                        $statusBadge = 'badge-success';
                                        $sisaText = 'Aktif';
                                        $sisaHari = $diff . ' hari lagi';
                                    }
                                }
                            @endphp
                            <tr class="hover">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar">
                                            <div class="mask mask-squircle w-10 h-10">
                                                <img src="{{ $k->foto_karyawan ? Storage::url($k->foto_karyawan) : 'https://i.pravatar.cc/150?u=' . $k->nik }}" alt="{{ $k->nama_karyawan }}" />
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold text-sm">{{ $k->nama_karyawan }}</div>
                                            <div class="text-xs text-base-content/50">{{ $k->jabatan?->nama_jabatan ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($latestContract)
                                        <span class="font-medium text-sm">{{ $latestContract->nomor }}</span>
                                    @else
                                        <span class="text-xs text-base-content/40 italic">Belum ada kontrak</span>
                                    @endif
                                </td>
                                <td>
                                    @if($latestContract && $latestContract->masaKontrak)
                                        <span class="text-sm">{{ $latestContract->masaKontrak->status_kontrak }}</span>
                                    @else
                                        <span class="text-xs text-base-content/40">-</span>
                                    @endif
                                </td>
                                <td class="text-sm">
                                    @if($latestContract)
                                        {{ Carbon::parse($latestContract->tanggal_mulai)->format('d/m/Y') }} - {{ Carbon::parse($latestContract->tanggal_akhir)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($latestContract)
                                        <div class="flex flex-col gap-0.5">
                                            <span class="badge {{ $statusBadge }} badge-sm font-semibold whitespace-nowrap">{{ $sisaText }}</span>
                                            <span class="text-xs text-base-content/50">{{ $sisaHari }}</span>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-right">
                                    <x-button wire:click="kelolaKontrak({{ $k->id }})" label="Kelola" icon="o-document-text" class="btn-sm btn-primary text-white shadow-sm" spinner="kelolaKontrak({{ $k->id }})" tooltip="Kelola Kontrak" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-base-content/50">
                                    <x-icon name="o-users" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                                    Tidak ada karyawan yang sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 px-4 pb-4">
                {{ $karyawans->links() }}
            </div>
        </div>
    </div>
</x-card>

    {{-- Modal Kelola Kontrak --}}
    <x-modal wire:model="showModal" title="Kelola Kontrak: {{ $selectedKaryawan?->nama_karyawan ?? '' }}" box-class="max-w-4xl">
        @if($selectedKaryawan)
            <div class="flex flex-col gap-4">
                
                {{-- Header Info --}}
                <div class="bg-base-200/50 p-4 rounded-xl flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <p class="text-sm text-base-content/60">NIK: {{ $selectedKaryawan->nik }}</p>
                        <p class="font-bold text-lg">{{ $selectedKaryawan->nama_karyawan }}</p>
                        <p class="text-sm">{{ $selectedKaryawan->jabatan?->nama_jabatan ?? 'Tanpa Jabatan' }}</p>
                    </div>
                    @if(!$showForm)
                        <x-button wire:click="tambahBaru" label="Tambah Kontrak Baru" icon="o-plus" class="btn-primary btn-sm text-white shadow-sm" />
                    @endif
                </div>

                {{-- Form Tambah/Edit --}}
                @if($showForm)
                    <div class="bg-base-100 p-5 rounded-xl border border-primary/20 shadow-sm relative">
                        <h3 class="font-bold text-lg mb-4 text-primary">{{ $editingId ? 'Edit Kontrak (Kontrak Terbaru)' : 'Buat Kontrak Baru' }}</h3>
                        
                        <form wire:submit="simpan" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-input label="No. Kontrak" wire:model="nomor" placeholder="Contoh: 001/HRD/KONTRAK/2026" required />
                                <x-input type="date" label="Tanggal Surat" wire:model="tanggal_surat" required />
                                
                                <x-select label="Masa Kontrak" wire:model="masa_kontrak_id" :options="$masaKontraks" option-value="id" option-label="status_kontrak" placeholder="Pilih masa kontrak..." required />
                                <x-select label="Penandatangan" wire:model="penandatangan_id" :options="$penandatangans" option-value="id" option-label="nama_penandatangan" placeholder="Pilih penandatangan..." required />
                                
                                <x-input type="date" label="Tanggal Mulai" wire:model="tanggal_mulai" required />
                                <x-input type="date" label="Tanggal Akhir" wire:model="tanggal_akhir" required />
                                
                                <x-input type="number" prefix="Rp" label="Gaji Pokok" wire:model="gaji" required />
                                <x-input type="number" prefix="Rp" label="Tunjangan" wire:model="tunjangan" required />
                                
                                <x-input type="number" prefix="Rp" label="Uang Makan (Dalam Kota)" wire:model="um_dalamkota" required />
                                <x-input type="number" prefix="Rp" label="Uang Makan (Luar Kota)" wire:model="um_luarkota" required />
                            </div>

                            <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-base-200">
                                <x-button wire:click="batalForm" label="Batal" class="btn-ghost" />
                                <x-button type="submit" label="Simpan Kontrak" icon="o-check" class="btn-primary text-white" spinner="simpan" />
                            </div>
                        </form>
                    </div>
                @endif

                {{-- Riwayat Kontrak --}}
                @if(!$showForm)
                    <div class="border border-base-300 rounded-xl overflow-hidden shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="table w-full text-sm">
                                <thead class="bg-base-200">
                                    <tr>
                                        <th>No. Kontrak</th>
                                        <th>Periode</th>
                                        <th>Masa Kontrak</th>
                                        <th>Gaji Pokok</th>
                                        <th>Status Record</th>
                                        <th class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($selectedKaryawan->kontraks as $index => $knt)
                                        @php
                                            $isLatest = ($index === 0);
                                        @endphp
                                        <tr class="hover">
                                            <td class="font-semibold">{{ $knt->nomor }}</td>
                                            <td>{{ Carbon::parse($knt->tanggal_mulai)->format('d/m/y') }} - {{ Carbon::parse($knt->tanggal_akhir)->format('d/m/y') }}</td>
                                            <td>{{ $knt->masaKontrak?->status_kontrak ?? '-' }}</td>
                                            <td>Rp {{ number_format($knt->gaji, 0, ',', '.') }}</td>
                                            <td>
                                                @if($isLatest)
                                                    <span class="badge badge-success badge-sm font-semibold">Aktif</span>
                                                @else
                                                    <span class="badge badge-ghost badge-sm opacity-70">Arsip</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <div class="flex justify-end gap-1 items-center">
                                                    <x-button link="{{ route('kontrak.pdf', $knt->id) }}" external icon="o-printer" class="btn-xs btn-ghost text-primary" tooltip="Cetak / Preview PDF" />
                                                    @if($isLatest)
                                                        <x-button wire:click="edit({{ $knt->id }})" icon="o-pencil" class="btn-xs btn-ghost text-info" tooltip="Edit Kontrak Terbaru" spinner="edit({{ $knt->id }})" />
                                                    @else
                                                        <span class="text-xs text-base-content/40 italic px-1">Terkunci</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-6 text-base-content/50">
                                                Belum ada riwayat kontrak.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </x-modal>
</div>
