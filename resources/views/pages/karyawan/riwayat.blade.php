<?php

use App\Models\Absen;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Riwayat Absensi')] class extends Component {
    public $bulan;
    public $tahun;
    
    public $riwayatAbsen = [];
    public $isSearched = false;

    public function mount()
    {
        if (!auth()->user()->hasRole('karyawan')) {
            $this->redirect('/');
        }
        
        $this->bulan = now()->format('m');
        $this->tahun = now()->format('Y');
        
        $this->cari();
    }

    public function cari()
    {
        $user = auth()->user();
        $karyawan = $user->karyawan;

        if ($karyawan) {
            $this->riwayatAbsen = Absen::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal_absen', $this->bulan)
                ->whereYear('tanggal_absen', $this->tahun)
                ->orderByDesc('tanggal_absen')
                ->get();
        } else {
            $this->riwayatAbsen = collect();
        }
        
        $this->isSearched = true;
    }
    
    public function with(): array
    {
        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];
        
        $years = range(now()->year - 5, now()->year);
        
        return [
            'months' => $months,
            'years' => $years,
        ];
    }
}; ?>

<div>
    {{-- Header --}}
    <x-header title="Riwayat Absensi" separator progress-indicator />

    {{-- Filter Section --}}
    <x-card class="mb-6 border border-base-300 shadow-sm">
        <form wire:submit="cari" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <x-select label="Bulan" wire:model="bulan" :options="collect($months)->map(fn($name, $id) => ['id' => $id, 'name' => $name])" option-value="id" option-label="name" />
            <x-select label="Tahun" wire:model="tahun" :options="collect($years)->map(fn($year) => ['id' => $year, 'name' => $year])" option-value="id" option-label="name" />
            
            <div class="pt-2">
                <x-button type="submit" label="Lihat Data" icon="o-magnifying-glass" class="btn-primary w-full shadow-sm" spinner="cari" />
            </div>
        </form>
    </x-card>

    {{-- Data List --}}
    @if($isSearched)
        <div class="space-y-3 mb-24">
            @forelse ($riwayatAbsen as $absen)
                @php
                    $borderColor = match($absen->keterangan) {
                        'Hadir' => 'border-success',
                        'Dinas Luar' => 'border-info',
                        'Cuti' => 'border-success',
                        'Sakit' => 'border-warning',
                        'Izin' => 'border-warning',
                        'Alpa' => 'border-error',
                        'Off', 'Libur' => 'border-base-300',
                        default => 'border-base-300',
                    };
                    $badgeClass = match($absen->keterangan) {
                        'Hadir' => 'badge-success',
                        'Dinas Luar' => 'badge-info',
                        'Cuti' => 'badge-success badge-outline',
                        'Sakit' => 'badge-warning',
                        'Izin' => 'badge-warning badge-outline',
                        'Alpa' => 'badge-error',
                        'Off', 'Libur' => 'badge-ghost',
                        default => 'badge-ghost',
                    };
                @endphp
                <div class="flex items-center gap-4 p-4 bg-base-100 rounded-2xl border-l-4 {{ $borderColor }} border-y border-r border-base-200 hover:shadow-md transition-shadow">
                    <div class="flex flex-col items-center justify-center min-w-[50px] md:min-w-[60px]">
                        <span class="text-2xl md:text-3xl font-bold text-base-content">{{ Carbon::parse($absen->tanggal_absen)->format('d') }}</span>
                        <span class="text-[10px] md:text-xs text-base-content/50 uppercase font-semibold">{{ Carbon::parse($absen->tanggal_absen)->locale('id')->isoFormat('MMM') }}</span>
                    </div>
                    
                    <div class="flex-1 min-w-0 border-l border-base-200 pl-4 md:pl-5">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <span class="text-xs md:text-sm text-base-content/70 font-semibold">{{ Carbon::parse($absen->tanggal_absen)->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                            <div class="badge {{ $badgeClass }} badge-sm md:badge-md text-[10px] md:text-xs">{{ $absen->keterangan }}</div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mt-2 bg-base-200/50 p-2.5 rounded-xl">
                            <div>
                                <div class="flex items-center gap-1.5 mb-0.5">
                                    <x-icon name="o-arrow-right-on-rectangle" class="w-3.5 h-3.5 text-base-content/50" />
                                    <p class="text-[10px] md:text-xs text-base-content/60 uppercase font-semibold">Jam Masuk</p>
                                </div>
                                <p class="text-sm md:text-base font-bold text-base-content">{{ $absen->jam_masuk ? Carbon::parse($absen->jam_masuk)->format('H:i') : '--:--' }}</p>
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5 mb-0.5">
                                    <x-icon name="o-arrow-left-on-rectangle" class="w-3.5 h-3.5 text-base-content/50" />
                                    <p class="text-[10px] md:text-xs text-base-content/60 uppercase font-semibold">Jam Keluar</p>
                                </div>
                                <p class="text-sm md:text-base font-bold text-base-content">{{ $absen->jam_keluar ? Carbon::parse($absen->jam_keluar)->format('H:i') : '--:--' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-base-100 rounded-2xl border border-base-200 shadow-sm">
                    <x-icon name="o-calendar-days" class="w-12 h-12 md:w-16 md:h-16 mx-auto mb-4 text-base-content/30" />
                    <p class="text-base-content/60 font-medium text-sm md:text-base">Belum ada riwayat absensi pada bulan ini.</p>
                </div>
            @endforelse
        </div>
    @endif
</div>
