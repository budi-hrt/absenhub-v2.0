<?php

use App\Models\JatahCuti;
use App\Models\PengajuanAbsen;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new #[Layout('layouts.app')] #[Title('Pengajuan Izin/Cuti')] class extends Component {
    use Toast, WithFileUploads;

    #[Validate('required|in:Cuti,Izin,Sakit')]
    public string $jenis = '';

    #[Validate([
        'tanggal' => 'required|array|min:1',
        'tanggal.*' => 'date',
    ], message: [
        'tanggal.required' => 'Pilih setidaknya satu tanggal.',
        'tanggal.min' => 'Pilih setidaknya satu tanggal.',
    ])]
    public array $tanggal = [];

    #[Validate('nullable|string|max:500')]
    public string $keterangan = '';

    #[Validate('nullable|image|max:2048')]
    public $lampiran = null;

    public bool $showForm = false;

    public function mount()
    {
        if (!auth()->user()->hasRole('karyawan')) {
            $this->redirect('/');
        }
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        $this->reset(['jenis', 'tanggal', 'keterangan', 'lampiran']);
        $this->resetValidation();
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

    public function submit()
    {
        $this->validate();

        $karyawan = auth()->user()->karyawan;

        if (!$karyawan) {
            $this->error('Data karyawan tidak ditemukan.');
            return;
        }

        $jumlahHari = count($this->tanggal);

        // Cek sisa cuti jika jenis = Cuti
        if ($this->jenis === 'Cuti') {
            $sisaCuti = JatahCuti::sisaByKaryawan($karyawan->id);

            if ($jumlahHari > $sisaCuti) {
                $this->error("Sisa cuti Anda tidak mencukupi. Sisa: {$sisaCuti} hari, diajukan: {$jumlahHari} hari.");
                return;
            }
        }

        // Cek apakah ada pengajuan yang tumpang tindih
        $existingDates = PengajuanAbsen::where('karyawan_id', $karyawan->id)
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

        // Upload lampiran jika ada
        $lampiranPath = null;
        if ($this->lampiran) {
            $lampiranPath = $this->lampiran->store('lampiran-pengajuan', 'public');
        }

        PengajuanAbsen::create([
            'karyawan_id' => $karyawan->id,
            'jenis' => $this->jenis,
            'tanggal' => $this->tanggal,
            'keterangan' => $this->keterangan,
            'lampiran' => $lampiranPath,
            'status' => 'Menunggu',
        ]);

        $this->showForm = false;
        $this->reset(['jenis', 'tanggal', 'keterangan', 'lampiran']);
        $this->success('Pengajuan berhasil dikirim! Menunggu persetujuan admin.');
    }

    public function with(): array
    {
        $karyawan = auth()->user()->karyawan;

        $jatahCuti = null;
        $terpakai = 0;
        $sisaCuti = 0;
        $pengajuans = collect();

        if ($karyawan) {
            $jatahCuti = JatahCuti::getTahun(now()->year);
            $terpakai = JatahCuti::terpakaiByKaryawan($karyawan->id);
            $sisaCuti = JatahCuti::sisaByKaryawan($karyawan->id);

            $pengajuans = PengajuanAbsen::where('karyawan_id', $karyawan->id)
                ->orderByDesc('created_at')
                ->get();
        }

        $jenisOptions = [
            ['id' => 'Cuti', 'name' => 'Cuti'],
            ['id' => 'Izin', 'name' => 'Izin'],
            ['id' => 'Sakit', 'name' => 'Sakit'],
        ];

        return [
            'jatahCuti' => $jatahCuti,
            'terpakai' => $terpakai,
            'sisaCuti' => $sisaCuti,
            'pengajuans' => $pengajuans,
            'jenisOptions' => $jenisOptions,
        ];
    }
}; ?>

<div>
    {{-- Header --}}
    <x-header title="Pengajuan Izin / Cuti" separator progress-indicator />

    {{-- Sisa Cuti Card --}}
    @if($jatahCuti)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-base-100 p-5 rounded-2xl border border-base-300 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                    <x-icon name="o-calendar-days" class="w-6 h-6 text-primary" />
                </div>
                <div>
                    <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Jatah Cuti {{ now()->year }}</p>
                    <p class="text-2xl font-bold text-base-content">{{ $jatahCuti->jatah_cuti }} <span class="text-sm font-normal text-base-content/60">hari</span></p>
                </div>
            </div>
        </div>

        <div class="bg-base-100 p-5 rounded-2xl border border-base-300 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-warning/10 flex items-center justify-center shrink-0">
                    <x-icon name="o-clock" class="w-6 h-6 text-warning" />
                </div>
                <div>
                    <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Terpakai</p>
                    <p class="text-2xl font-bold text-base-content">{{ $terpakai }} <span class="text-sm font-normal text-base-content/60">hari</span></p>
                </div>
            </div>
        </div>

        <div class="bg-base-100 p-5 rounded-2xl border border-base-300 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-success/10 flex items-center justify-center shrink-0">
                    <x-icon name="o-check-circle" class="w-6 h-6 text-success" />
                </div>
                <div>
                    <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Sisa Cuti</p>
                    <p class="text-2xl font-bold text-success">{{ $sisaCuti }} <span class="text-sm font-normal text-base-content/60">hari</span></p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Tombol Buat Pengajuan --}}
    <div class="mb-6">
        <x-button 
            wire:click="toggleForm" 
            :label="$showForm ? 'Batal' : 'Buat Pengajuan Baru'" 
            :icon="$showForm ? 'o-x-mark' : 'o-plus'" 
            :class="$showForm ? 'btn-ghost' : 'btn-primary'" 
            class="shadow-sm" 
        />
    </div>

    {{-- Form Pengajuan --}}
    @if($showForm)
    <x-card title="Form Pengajuan" class="mb-6 border border-base-300 shadow-sm">
        <form wire:submit="submit" class="space-y-4">
            <x-select 
                label="Jenis Pengajuan" 
                wire:model="jenis" 
                :options="$jenisOptions" 
                option-value="id" 
                option-label="name" 
                placeholder="Pilih jenis..." 
            />
            
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
                <x-button wire:click="tambahTanggal" label="Tambah Tanggal" icon="o-plus" class="btn-sm btn-ghost mt-2 border border-base-300" />
                @error('tanggal') <span class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <x-textarea label="Keterangan / Alasan" wire:model="keterangan" placeholder="Tulis alasan pengajuan Anda..." rows="3" />

            <x-file label="Lampiran (Surat Dokter / Bukti)" wire:model="lampiran" accept="image/*" hint="Opsional. Maks 2MB. Format: JPG, PNG." />

            <div class="flex justify-end pt-2">
                <x-button type="submit" label="Kirim Pengajuan" icon="o-paper-airplane" class="btn-primary shadow-sm" spinner="submit" />
            </div>
        </form>
    </x-card>
    @endif

    {{-- Riwayat Pengajuan --}}
    <div class="space-y-3 mb-24">
        <h3 class="text-sm md:text-base font-semibold text-base-content mb-3">Riwayat Pengajuan</h3>
        
        @forelse ($pengajuans as $p)
            @php
                $statusBadge = match($p->status) {
                    'Menunggu' => 'badge-warning',
                    'Disetujui' => 'badge-success',
                    'Ditolak' => 'badge-error',
                    default => 'badge-ghost',
                };
                $borderColor = match($p->status) {
                    'Menunggu' => 'border-warning',
                    'Disetujui' => 'border-success',
                    'Ditolak' => 'border-error',
                    default => 'border-base-300',
                };
                $jenisBadge = match($p->jenis) {
                    'Cuti' => 'badge-primary',
                    'Izin' => 'badge-info',
                    'Sakit' => 'badge-error badge-outline',
                    default => 'badge-ghost',
                };
            @endphp
            <div class="bg-base-100 rounded-2xl border-l-4 {{ $borderColor }} border-y border-r border-base-200 p-4 hover:shadow-md transition-shadow">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                    <div class="flex items-center gap-2">
                        <div class="badge {{ $jenisBadge }} badge-sm md:badge-md font-semibold">{{ $p->jenis }}</div>
                        <span class="text-xs text-base-content/50">{{ $p->jumlah_hari }} hari</span>
                    </div>
                    <div class="badge {{ $statusBadge }} badge-sm md:badge-md font-semibold">{{ $p->status }}</div>
                </div>

                <div class="bg-base-200/50 p-3 rounded-xl mb-3">
                    <p class="text-[10px] md:text-xs text-base-content/60 uppercase font-semibold mb-1">Daftar Tanggal</p>
                    <div class="flex flex-wrap gap-2">
                        @if(is_array($p->tanggal))
                            @foreach($p->tanggal as $tgl)
                                <span class="badge badge-neutral">{{ Carbon::parse($tgl)->locale('id')->isoFormat('D MMM Y') }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>

                @if($p->keterangan)
                <p class="text-xs md:text-sm text-base-content/70 mb-2">
                    <span class="font-semibold">Alasan:</span> {{ $p->keterangan }}
                </p>
                @endif

                @if($p->status === 'Ditolak' && $p->alasan_tolak)
                <div class="bg-error/10 text-error text-xs md:text-sm p-3 rounded-xl mt-2">
                    <span class="font-semibold">Alasan ditolak:</span> {{ $p->alasan_tolak }}
                </div>
                @endif

                <p class="text-[10px] text-base-content/40 mt-2">Diajukan: {{ Carbon::parse($p->created_at)->locale('id')->isoFormat('D MMMM Y, HH:mm') }}</p>
            </div>
        @empty
            <div class="text-center py-12 bg-base-100 rounded-2xl border border-base-200 shadow-sm">
                <x-icon name="o-document-plus" class="w-12 h-12 md:w-16 md:h-16 mx-auto mb-4 text-base-content/30" />
                <p class="text-base-content/60 font-medium text-sm md:text-base">Belum ada riwayat pengajuan.</p>
                <p class="text-base-content/40 text-xs mt-1">Klik tombol "Buat Pengajuan Baru" untuk memulai.</p>
            </div>
        @endforelse
    </div>
</div>
