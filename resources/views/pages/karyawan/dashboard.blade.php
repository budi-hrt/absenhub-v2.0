<?php

use App\Models\Absen;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Dashboard')] class extends Component {
    public function mount()
    {
        if (!auth()->user()->hasRole('karyawan')) {
            $this->redirect('/');
        }
    }

    public function with(): array
    {
        $user = auth()->user();
        $karyawan = $user->karyawan;

        $bulanIni = now();
        $absens = collect();
        $todayAbsen = null;

        if ($karyawan) {
            $absens = Absen::where('karyawan_id', $karyawan->id)
                ->whereMonth('tanggal_absen', $bulanIni->month)
                ->whereYear('tanggal_absen', $bulanIni->year)
                ->orderByDesc('tanggal_absen')
                ->get();

            $todayAbsen = $absens->firstWhere('tanggal_absen', today()->toDateString());
        }

        $alpa = $absens->where('keterangan', 'Alpa')->count();
        $izin = $absens->where('keterangan', 'Izin')->count();
        $sakit = $absens->where('keterangan', 'Sakit')->count();
        $cuti = $absens->where('keterangan', 'Cuti')->count();
        $hadir = $absens->where('keterangan', 'Hadir')->count();
        $dinasLuar = $absens->where('keterangan', 'Dinas Luar')->count();

        $recentAbsens = $absens->take(5);
        $totalHariKerja = $absens->count();

        $persen = $totalHariKerja > 0
            ? max(0, round(100 - ($alpa * 3) - ($izin * 2) - ($sakit * 1), 1))
            : 100;

        return [
            'karyawan' => $karyawan,
            'todayAbsen' => $todayAbsen,
            'alpa' => $alpa,
            'izin' => $izin,
            'sakit' => $sakit,
            'cuti' => $cuti,
            'hadir' => $hadir,
            'dinasLuar' => $dinasLuar,
            'recentAbsens' => $recentAbsens,
            'persen' => $persen,
            'totalHariKerja' => $totalHariKerja,
            'namaBulan' => $bulanIni->translatedFormat('F Y'),
        ];
    }
}; ?>

<div>
    <section class="mb-6 md:mb-8">
        <div class="relative rounded-2xl md:rounded-[32px] overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-primary/90 to-primary-container/80"></div>
            <div class="absolute inset-0 opacity-20">
                <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white/30 blur-3xl"></div>
                <div class="absolute -left-5 -bottom-5 w-36 h-36 rounded-full bg-white/20 blur-2xl"></div>
            </div>
            <div class="relative p-6 md:p-8 lg:p-10 flex items-center gap-5 md:gap-6">
                <x-avatar
                    :image="auth()->user()->face_photo
                        ? Storage::url(auth()->user()->face_photo)
                        : 'https://i.pravatar.cc/150?img=9'"
                    :title="$karyawan?->nama_karyawan ?? auth()->user()->name"
                    :subtitle="$karyawan?->jabatan?->nama_jabatan ?? '-'"
                    class="!w-24"
                />
            </div>
        </div>
    </section>

    {{-- Statistics Grid --}}
    <section class="mb-6 md:mb-8">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm md:text-base font-semibold text-base-content">Statistik Bulan Ini</h3>
            <span class="text-[10px] md:text-xs font-semibold text-primary bg-primary/10 px-2.5 py-1 rounded-full">{{ $namaBulan }}</span>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
            {{-- Izin --}}
            <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Izin</p>
                    <h3 class="text-xl md:text-2xl font-bold text-warning mt-0.5">{{ $izin }} <span class="text-xs font-normal text-base-content/60">hari</span></h3>
                </div>
                <div class="w-9 h-9 rounded-lg bg-warning/10 text-warning flex items-center justify-center">
                    <x-icon name="o-document-text" class="w-5 h-5" />
                </div>
            </div>
            {{-- Sakit --}}
            <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Sakit</p>
                    <h3 class="text-xl md:text-2xl font-bold text-amber-600 mt-0.5">{{ $sakit }} <span class="text-xs font-normal text-base-content/60">hari</span></h3>
                </div>
                <div class="w-9 h-9 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
                    <x-icon name="o-heart" class="w-5 h-5" />
                </div>
            </div>
            {{-- Cuti --}}
            <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Cuti</p>
                    <h3 class="text-xl md:text-2xl font-bold text-primary mt-0.5">{{ $cuti }} <span class="text-xs font-normal text-base-content/60">hari</span></h3>
                </div>
                <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                    <x-icon name="o-calendar" class="w-5 h-5" />
                </div>
            </div>
            {{-- Alpa --}}
            <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Alpa</p>
                    <h3 class="text-xl md:text-2xl font-bold text-error mt-0.5">{{ $alpa }} <span class="text-xs font-normal text-base-content/60">hari</span></h3>
                </div>
                <div class="w-9 h-9 rounded-lg bg-error/10 text-error flex items-center justify-center">
                    <x-icon name="o-x-circle" class="w-5 h-5" />
                </div>
            </div>
        </div>
    </section>

    {{-- Activity Log & Insight --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
        {{-- Aktivitas Terakhir --}}
        <div class="lg:col-span-2 bg-base-100 border border-base-300 rounded-2xl md:rounded-[32px] p-5 md:p-6 lg:p-8 space-y-4 md:space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-base md:text-lg font-semibold text-base-content">Aktivitas Terakhir</h3>
                <span class="text-xs font-semibold text-primary bg-primary/10 px-3 py-1.5 rounded-full">{{ $namaBulan }}</span>
            </div>
            <div class="space-y-2 md:space-y-3">
                @forelse ($recentAbsens as $absen)
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
                    <div class="flex items-center gap-3 md:gap-4 lg:gap-6 p-3 md:p-4 rounded-xl md:rounded-2xl border-l-4 {{ $borderColor }} bg-base-200/30 hover:bg-base-200/60 transition-colors">
                        <div class="flex flex-col items-center min-w-[40px] md:min-w-[55px]">
                            <span class="text-base md:text-lg font-bold text-base-content">{{ $absen->tanggal_absen->format('d') }}</span>
                            <span class="text-[9px] md:text-[10px] text-base-content/50 uppercase font-semibold">{{ $absen->tanggal_absen->translatedFormat('M') }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm md:text-base font-semibold text-base-content truncate">{{ $absen->keterangan }}</p>
                            <p class="text-[11px] md:text-xs text-base-content/50">
                                @if ($absen->jam_masuk)
                                    Masuk: {{ Carbon::parse($absen->jam_masuk)->format('H:i') }}
                                @endif
                                @if ($absen->jam_keluar)
                                    • Keluar: {{ Carbon::parse($absen->jam_keluar)->format('H:i') }}
                                @endif
                                @if (!$absen->jam_masuk && !$absen->jam_keluar)
                                    {{ $absen->tanggal_absen->translatedFormat('l') }}
                                @endif
                            </p>
                        </div>
                        <div class="badge {{ $badgeClass }} badge-sm md:badge-md text-[10px] md:text-xs shrink-0">
                            {{ $absen->keterangan }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 md:py-12 text-base-content/40">
                        <x-icon name="o-inbox" class="w-10 h-10 md:w-12 md:h-12 mx-auto mb-3" />
                        <p class="text-sm">Belum ada data absen bulan ini</p>
                    </div>
                @endforelse
            </div>
            @if ($recentAbsens->isNotEmpty())
                <button class="w-full py-3 md:py-3.5 rounded-xl md:rounded-2xl bg-base-200 text-base-content/70 font-semibold text-sm hover:bg-base-300 transition-colors">
                    Lihat Riwayat Lengkap
                </button>
            @endif
        </div>

        {{-- Insight Panel --}}
        <div class="bg-neutral text-neutral-content rounded-2xl md:rounded-[32px] p-5 md:p-6 lg:p-8 flex flex-col justify-between relative overflow-hidden min-h-[300px] md:min-h-[320px]">
            <div class="absolute -right-10 -top-10 w-48 h-48 bg-primary/20 rounded-full blur-3xl"></div>
            <div class="absolute -left-5 bottom-10 w-28 h-28 bg-secondary/15 rounded-full blur-2xl"></div>

            <div class="relative z-10 space-y-3 md:space-y-4">
                <div class="w-12 h-12 md:w-14 md:h-14 rounded-xl md:rounded-2xl bg-white/10 backdrop-blur-xl flex items-center justify-center">
                    <x-icon name="o-chart-bar" class="w-6 h-6 md:w-7 md:h-7" />
                </div>
                <h4 class="text-base md:text-lg font-bold">Performa Bulan Ini</h4>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl md:text-[36px] font-bold tracking-tight">{{ $persen }}%</span>
                    <span class="text-xs opacity-60">persentase</span>
                </div>
                <p class="text-xs md:text-sm opacity-80 leading-relaxed">
                    @if ($persen >= 95)
                        Performa Anda sangat baik! Tetap pertahankan kehadiran yang konsisten.
                    @elseif ($persen >= 80)
                        Performa cukup baik. Tingkatkan lagi kehadiran Anda.
                    @else
                        Perlu ditingkatkan. Pastikan untuk hadir tepat waktu setiap hari.
                    @endif
                </p>
            </div>

            <div class="relative z-10 mt-4 md:mt-6 space-y-3">
                <div class="grid grid-cols-2 gap-2 md:gap-3">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-2.5 md:p-3">
                        <p class="text-[10px] uppercase tracking-wider opacity-60 font-semibold">Kehadiran</p>
                        <p class="text-lg md:text-xl font-bold">{{ $hadir + $dinasLuar }} <span class="text-xs font-normal opacity-70">hari</span></p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-2.5 md:p-3">
                        <p class="text-[10px] uppercase tracking-wider opacity-60 font-semibold">Tdk Hadir</p>
                        <p class="text-lg md:text-xl font-bold">{{ $alpa + $sakit + $izin + $cuti }} <span class="text-xs font-normal opacity-70">hari</span></p>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-success animate-pulse"></span>
                        <span class="text-[10px] md:text-xs opacity-80 font-semibold">Total Hari Aktif: {{ $totalHariKerja }} hari</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
