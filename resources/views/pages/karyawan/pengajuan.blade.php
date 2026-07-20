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

    #[Validate('required|date|after_or_equal:today')]
    public string $tanggal_mulai = '';

    #[Validate('required|date|after_or_equal:tanggal_mulai')]
    public string $tanggal_selesai = '';

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
        $this->reset(['jenis', 'tanggal_mulai', 'tanggal_selesai', 'keterangan', 'lampiran']);
        $this->resetValidation();
    }

    public function submit()
    {
        $this->validate();

        $karyawan = auth()->user()->karyawan;

        if (!$karyawan) {
            $this->error('Data karyawan tidak ditemukan.');
            return;
        }

        $jumlahHari = Carbon::parse($this->tanggal_mulai)->diffInDays(Carbon::parse($this->tanggal_selesai)) + 1;

        // Cek sisa cuti jika jenis = Cuti
        if ($this->jenis === 'Cuti') {
            $sisaCuti = JatahCuti::sisaByKaryawan($karyawan->id);

            if ($jumlahHari > $sisaCuti) {
                $this->error("Sisa cuti Anda tidak mencukupi. Sisa: {$sisaCuti} hari, diajukan: {$jumlahHari} hari.");
                return;
            }
        }

        // Cek apakah ada pengajuan yang tumpang tindih
        $bentrok = PengajuanAbsen::where('karyawan_id', $karyawan->id)
            ->where('status', '!=', 'Ditolak')
            ->where(function ($q) {
                $q->whereBetween('tanggal_mulai', [$this->tanggal_mulai, $this->tanggal_selesai])
                    ->orWhereBetween('tanggal_selesai', [$this->tanggal_mulai, $this->tanggal_selesai]);
            })
            ->exists();

        if ($bentrok) {
            $this->error('Sudah ada pengajuan pada rentang tanggal tersebut.');
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
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'keterangan' => $this->keterangan,
            'lampiran' => $lampiranPath,
            'status' => 'Menunggu',
        ]);

        $this->showForm = false;
        $this->reset(['jenis', 'tanggal_mulai', 'tanggal_selesai', 'keterangan', 'lampiran']);
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
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input label="Tanggal Mulai" wire:model="tanggal_mulai" type="date" />
                <x-input label="Tanggal Selesai" wire:model="tanggal_selesai" type="date" />
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

                <div class="grid grid-cols-2 gap-4 bg-base-200/50 p-3 rounded-xl mb-3">
                    <div>
                        <p class="text-[10px] md:text-xs text-base-content/60 uppercase font-semibold mb-0.5">Dari</p>
                        <p class="text-sm font-bold text-base-content">{{ Carbon::parse($p->tanggal_mulai)->locale('id')->isoFormat('D MMM Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] md:text-xs text-base-content/60 uppercase font-semibold mb-0.5">Sampai</p>
                        <p class="text-sm font-bold text-base-content">{{ Carbon::parse($p->tanggal_selesai)->locale('id')->isoFormat('D MMM Y') }}</p>
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
