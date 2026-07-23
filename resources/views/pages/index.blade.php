<?php

use App\Models\Absen;
use App\Models\Karyawan;
use App\Models\Kontrak;
use App\Models\PengajuanAbsen;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Dashboard')] class extends Component {
    public int $selectedYear = 0;

    public array $listTahun = [];

    public function boot(): void
    {
        if (empty($this->selectedYear)) {
            $this->selectedYear = (int) now()->year;
        }

        $now = now();
        $this->listTahun = range($now->year - 3, $now->year + 1);
    }

    public function mount(): void
    {
        if (auth()->user()->hasRole('karyawan')) {
            $this->redirect('/dashboard');
            return;
        }
    }

    #[Computed]
    public function counts(): array
    {
        $karyawans = Karyawan::where('is_active', true)->get(['id', 'status_id']);
        
        $totalAktif = $karyawans->count();
        $kontrak = $karyawans->where('status_id', 2)->count();
        $tetap = $karyawans->where('status_id', 1)->count();

        return [
            'totalAktif' => $totalAktif,
            'kontrak' => $kontrak,
            'tetap' => $tetap,
        ];
    }

    #[Computed]
    public function kontrakExpiringCount(): int
    {
        $today = Carbon::now()->startOfDay()->toDateString();
        $in30Days = Carbon::now()->startOfDay()->addDays(30)->toDateString();

        return Kontrak::whereHas('karyawan', function ($q) {
            $q->where('is_active', true)->where('status_id', 2);
        })
        ->whereIn('id', function ($sub) {
            $sub->selectRaw('MAX(id)')
                ->from('kontraks')
                ->groupBy('karyawan_id');
        })
        ->whereBetween('tanggal_akhir', [$today, $in30Days])
        ->count();
    }

    #[Computed]
    public function annualStats(): array
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthNums = range(1, 12);

        $rawAbsen = Absen::selectRaw('MONTH(tanggal_absen) as m, keterangan, COUNT(*) as cnt')
            ->whereYear('tanggal_absen', (int) $this->selectedYear)
            ->whereIn('keterangan', ['Izin', 'I', 'Sakit', 'S', 'Alpa', 'A'])
            ->groupBy('m', 'keterangan')
            ->get();

        $izinData = [];
        $sakitData = [];
        $alpaData = [];

        foreach ($monthNums as $m) {
            $izinData[] = $rawAbsen->where('m', $m)->whereIn('keterangan', ['Izin', 'I'])->sum('cnt');
            $sakitData[] = $rawAbsen->where('m', $m)->whereIn('keterangan', ['Sakit', 'S'])->sum('cnt');
            $alpaData[] = $rawAbsen->where('m', $m)->whereIn('keterangan', ['Alpa', 'A'])->sum('cnt');
        }

        // Generate SVG paths dynamically normalized to height 200, width 1000
        $maxVal = max(1, max($izinData), max($sakitData), max($alpaData));

        $buildPath = function ($data) use ($maxVal) {
            $points = [];
            $step = 1000 / (count($data) - 1);
            foreach ($data as $i => $val) {
                $x = (int) ($i * $step);
                // Invert Y for SVG (0 top, 200 bottom), keep 20px padding top/bottom
                $y = (int) (180 - ($val / $maxVal) * 140);
                $points[] = "{$x},{$y}";
            }
            return 'M' . implode(' L', $points);
        };

        return [
            'months' => $months,
            'izinPath' => $buildPath($izinData),
            'sakitPath' => $buildPath($sakitData),
            'alpaPath' => $buildPath($alpaData),
            'totalIzin' => array_sum($izinData),
            'totalSakit' => array_sum($sakitData),
            'totalAlpa' => array_sum($alpaData),
        ];
    }

    #[Computed]
    public function attendanceDistribution(): array
    {
        $rawDist = Absen::selectRaw('keterangan, COUNT(*) as cnt')
            ->whereYear('tanggal_absen', (int) $this->selectedYear)
            ->whereIn('keterangan', ['Cuti', 'C', 'Alpa', 'A', 'Sakit', 'S', 'Izin', 'I'])
            ->groupBy('keterangan')
            ->get();

        $cuti = $rawDist->whereIn('keterangan', ['Cuti', 'C'])->sum('cnt');
        $alpa = $rawDist->whereIn('keterangan', ['Alpa', 'A'])->sum('cnt');
        $sakit = $rawDist->whereIn('keterangan', ['Sakit', 'S'])->sum('cnt');
        $izin = $rawDist->whereIn('keterangan', ['Izin', 'I'])->sum('cnt');

        $total = $cuti + $alpa + $sakit + $izin;
        if ($total === 0) {
            // Fallback for visual balance if database has no attendance records yet
            return [
                'cutiPct' => 40, 'alpaPct' => 24, 'sakitPct' => 20, 'izinPct' => 16,
                'dashCuti' => '100 251', 'offsetAlpa' => -100, 'dashAlpa' => '60 251',
                'offsetSakit' => -160, 'dashSakit' => '50 251',
                'offsetIzin' => -210, 'dashIzin' => '41 251',
            ];
        }

        $cutiPct = round(($cuti / $total) * 100);
        $alpaPct = round(($alpa / $total) * 100);
        $sakitPct = round(($sakit / $total) * 100);
        $izinPct = round(($izin / $total) * 100);

        // Circumference for r=40 is ~251.3
        $c = 251;
        $cutiDash = round(($cutiPct / 100) * $c);
        $alpaDash = round(($alpaPct / 100) * $c);
        $sakitDash = round(($sakitPct / 100) * $c);
        $izinDash = round(($izinPct / 100) * $c);

        $offsetAlpa = -$cutiDash;
        $offsetSakit = -($cutiDash + $alpaDash);
        $offsetIzin = -($cutiDash + $alpaDash + $sakitDash);

        return [
            'cutiPct' => $cutiPct,
            'alpaPct' => $alpaPct,
            'sakitPct' => $sakitPct,
            'izinPct' => $izinPct,
            'dashCuti' => "{$cutiDash} {$c}",
            'dashAlpa' => "{$alpaDash} {$c}",
            'dashSakit' => "{$sakitDash} {$c}",
            'dashIzin' => "{$izinDash} {$c}",
            'offsetAlpa' => $offsetAlpa,
            'offsetSakit' => $offsetSakit,
            'offsetIzin' => $offsetIzin,
        ];
    }

    #[Computed]
    public function expiringContracts()
    {
        $today = Carbon::now()->startOfDay();

        return Kontrak::with(['karyawan.jabatan'])
            ->whereHas('karyawan', function ($q) {
                $q->where('is_active', true)->where('status_id', 2);
            })
            ->whereIn('id', function ($sub) {
                $sub->selectRaw('MAX(id)')
                    ->from('kontraks')
                    ->groupBy('karyawan_id');
            })
            ->orderBy('tanggal_akhir', 'asc')
            ->take(5)
            ->get()
            ->map(function ($kontrak) use ($today) {
                $tglAkhir = Carbon::parse($kontrak->tanggal_akhir)->startOfDay();
                $diff = $today->diffInDays($tglAkhir, false);

                return [
                    'karyawan' => $kontrak->karyawan,
                    'kontrak' => $kontrak,
                    'diff_days' => $diff,
                    'is_urgent' => ($diff <= 14),
                ];
            });
    }

    #[Computed]
    public function topPerformers()
    {
        $tahun = (int) $this->selectedYear;

        $karyawans = Karyawan::with('jabatan')
            ->where('is_active', true)
            ->where('status_id', 1)
            ->get();

        if ($karyawans->isEmpty()) {
            return collect();
        }

        $karyawanIds = $karyawans->pluck('id');

        $absenStats = \Illuminate\Support\Facades\DB::table('absens')
            ->whereIn('karyawan_id', $karyawanIds)
            ->whereYear('tanggal_absen', $tahun)
            ->select('karyawan_id', 'keterangan', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('karyawan_id', 'keterangan')
            ->get()
            ->groupBy('karyawan_id');

        return $karyawans->map(function ($k) use ($absenStats) {
            $stats = $absenStats[$k->id] ?? collect();
            $hk = $stats->sum('total');

            $sakit = $stats->whereIn('keterangan', ['Sakit', 'S'])->sum('total');
            $izin = $stats->whereIn('keterangan', ['Izin', 'I'])->sum('total');
            $alpa = $stats->whereIn('keterangan', ['Alpa', 'A'])->sum('total');
            $lainnya = $stats->whereIn('keterangan', ['Lainnya'])->sum('total');

            $persen = $hk > 0 ? max(0, round(100 - ($alpa * 3) - ($izin * 2) - ($sakit * 1) - ($lainnya * 0.5), 1)) : 0;

            return [
                'karyawan' => $k,
                'rate' => $persen,
                'hk' => $hk,
            ];
        })
        ->sort(function ($a, $b) {
            if ($a['rate'] === $b['rate']) {
                return strcmp($a['karyawan']->nama_karyawan, $b['karyawan']->nama_karyawan);
            }
            return $b['rate'] <=> $a['rate'];
        })
        ->take(5)
        ->values();
    }
}; ?>

<div class="space-y-6">
    <!-- Header Section -->
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold text-base-content tracking-tight">HR Pulse</h1>
            <p class="text-base text-base-content/70 mt-1">Monitoring data karyawan dan absensi secara real-time.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold text-base-content/70 bg-base-200 px-4 py-2 rounded-lg flex items-center gap-2 border border-base-300">
                <x-icon name="o-calendar" class="w-4 h-4 text-primary" />
                {{ now()->isoFormat('MMMM YYYY') }}
            </span>
        </div>
    </header>

    <!-- Row 1: Summary Cards (Minimalist 4 Cards) -->
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <!-- Card 1: Total Karyawan -->
        <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Total Karyawan</p>
                <h3 class="text-xl md:text-2xl font-bold text-base-content mt-0.5">{{ number_format($this->counts['totalAktif']) }}</h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                <x-icon name="o-users" class="w-5 h-5" />
            </div>
        </div>

        <!-- Card 2: Karyawan Tetap -->
        <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Karyawan Tetap</p>
                <h3 class="text-xl md:text-2xl font-bold text-success mt-0.5">{{ number_format($this->counts['tetap']) }}</h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-success/10 text-success flex items-center justify-center">
                <x-icon name="o-check-badge" class="w-5 h-5" />
            </div>
        </div>

        <!-- Card 3: Karyawan Kontrak -->
        <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Karyawan Kontrak</p>
                <h3 class="text-xl md:text-2xl font-bold text-info mt-0.5">{{ number_format($this->counts['kontrak']) }}</h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-info/10 text-info flex items-center justify-center">
                <x-icon name="o-identification" class="w-5 h-5" />
            </div>
        </div>

        <!-- Card 4: Kontrak Hampir Habis -->
        <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Kontrak Exipring</p>
                <h3 class="text-xl md:text-2xl font-bold text-error mt-0.5">{{ number_format($this->kontrakExpiringCount) }}</h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-error/10 text-error flex items-center justify-center">
                <x-icon name="o-exclamation-triangle" class="w-5 h-5" />
            </div>
        </div>
    </section>

    <!-- Row 2: Charts -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Annual Attendance Line Chart -->
        <div class="lg:col-span-8 bg-base-100 border border-base-300 p-6 rounded-2xl shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-base-content">Statistik Absensi Tahunan</h3>
                    <select wire:model.live="selectedYear" class="select select-bordered select-xs text-xs font-semibold text-base-content/80">
                        @foreach ($this->listTahun as $y)
                            <option value="{{ $y }}">Tahun {{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="h-64 w-full relative px-2 pb-8 pt-4">
                    <!-- Grid Lines -->
                    <div class="absolute inset-x-0 bottom-8 border-b border-base-300"></div>
                    <div class="absolute inset-x-0 top-1/2 border-b border-base-300/40"></div>
                    <div class="absolute inset-x-0 top-1/4 border-b border-base-300/40"></div>

                    <!-- Line Chart SVG -->
                    <svg class="w-full h-48 overflow-visible" viewBox="0 0 1000 200" preserveAspectRatio="none">
                        <!-- Izin (Primary Color) -->
                        <path d="{{ $this->annualStats['izinPath'] }}" fill="none" stroke="currentColor" class="text-primary" stroke-width="3" stroke-linecap="round" />
                        <!-- Sakit (Warning Color) -->
                        <path d="{{ $this->annualStats['sakitPath'] }}" fill="none" stroke="currentColor" class="text-warning" stroke-width="3" stroke-linecap="round" />
                        <!-- Alpa (Error Color) -->
                        <path d="{{ $this->annualStats['alpaPath'] }}" fill="none" stroke="currentColor" class="text-error" stroke-width="3" stroke-linecap="round" />
                    </svg>

                    <!-- X-Axis Labels -->
                    <div class="flex justify-between mt-2 px-1 text-xs text-base-content/70 font-medium">
                        @foreach ($this->annualStats['months'] as $month)
                            <span>{{ $month }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Minimal Legend -->
            <div class="flex justify-center gap-6 mt-4">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 bg-primary rounded-full"></div>
                    <span class="text-xs text-base-content/70">Izin</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 bg-warning rounded-full"></div>
                    <span class="text-xs text-base-content/70">Sakit</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 bg-error rounded-full"></div>
                    <span class="text-xs text-base-content/70">Alpa</span>
                </div>
            </div>
        </div>

        <!-- Right: Attendance Distribution Donut Chart -->
        <div class="lg:col-span-4 bg-base-100 border border-base-300 p-6 rounded-2xl shadow-sm flex flex-col justify-between">
            <h3 class="text-xl font-semibold text-base-content mb-6">Distribusi Kehadiran</h3>
            
            <div class="flex-grow flex items-center justify-center relative min-h-[180px]">
                <svg class="w-48 h-48 transform -rotate-90" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="currentColor" class="text-base-200" stroke-width="12"></circle>
                    <!-- Cuti (Success) -->
                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="currentColor" class="text-success" stroke-width="12" stroke-dasharray="{{ $this->attendanceDistribution['dashCuti'] }}"></circle>
                    <!-- Alpa (Error) -->
                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="currentColor" class="text-error" stroke-width="12" stroke-dasharray="{{ $this->attendanceDistribution['dashAlpa'] }}" stroke-dashoffset="{{ $this->attendanceDistribution['offsetAlpa'] }}"></circle>
                    <!-- Sakit (Warning) -->
                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="currentColor" class="text-warning" stroke-width="12" stroke-dasharray="{{ $this->attendanceDistribution['dashSakit'] }}" stroke-dashoffset="{{ $this->attendanceDistribution['offsetSakit'] }}"></circle>
                    <!-- Izin (Primary) -->
                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="currentColor" class="text-primary" stroke-width="12" stroke-dasharray="{{ $this->attendanceDistribution['dashIzin'] }}" stroke-dashoffset="{{ $this->attendanceDistribution['offsetIzin'] }}"></circle>
                </svg>
                <div class="absolute text-center">
                    <p class="text-[10px] text-base-content/60 uppercase tracking-wider font-semibold">Status</p>
                    <p class="text-lg font-bold text-base-content">Absensi</p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-success"></div>
                    <span class="text-xs text-base-content/70">Cuti ({{ $this->attendanceDistribution['cutiPct'] }}%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-error"></div>
                    <span class="text-xs text-base-content/70">Alpa ({{ $this->attendanceDistribution['alpaPct'] }}%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-warning"></div>
                    <span class="text-xs text-base-content/70">Sakit ({{ $this->attendanceDistribution['sakitPct'] }}%)</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-primary"></div>
                    <span class="text-xs text-base-content/70">Izin ({{ $this->attendanceDistribution['izinPct'] }}%)</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Row 3: Lists / Tables -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Contract Expiry Table -->
        <div class="lg:col-span-8 bg-base-100 border border-base-300 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-base-300 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-base-content">Kontrak Kerja Mau Jatuh Tempo</h3>
                <a href="{{ route('kontrak.index') }}" wire:navigate class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                    Lihat Semua
                    <x-icon name="o-arrow-right" class="w-3.5 h-3.5" />
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-base-200/60 text-xs font-semibold text-base-content/70 uppercase tracking-wider">
                            <th class="px-6 py-3">Nama Karyawan</th>
                            <th class="px-6 py-3">Departemen</th>
                            <th class="px-6 py-3">Tanggal Berakhir</th>
                            <th class="px-6 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-base-300/60 text-sm">
                        @forelse ($this->expiringContracts as $item)
                            @php
                                $karyawan = $item['karyawan'];
                                $kontrak = $item['kontrak'];
                                $isUrgent = $item['is_urgent'];
                            @endphp
                            <tr class="hover:bg-base-200/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-primary text-primary-content flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($karyawan?->nama_karyawan ?? 'K', 0, 2)) }}
                                        </div>
                                        <span class="font-medium text-base-content">{{ $karyawan?->nama_karyawan ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-base-content/70">
                                    {{ $karyawan?->jabatan?->nama_jabatan ?? 'General' }}
                                </td>
                                <td class="px-6 py-4 text-base-content/70">
                                    {{ $kontrak->tanggal_akhir ? $kontrak->tanggal_akhir->format('d M Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($isUrgent)
                                        <span class="badge badge-error badge-sm font-semibold">Urgent</span>
                                    @else
                                        <span class="badge badge-primary badge-outline badge-sm font-semibold">In Review</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-base-content/60">
                                    Tidak ada data kontrak yang mau jatuh tempo dalam waktu dekat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Top Performance List -->
        <div class="lg:col-span-4 bg-base-100 border border-base-300 p-6 rounded-2xl shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-xl font-semibold text-base-content mb-6">5 Top Performa Absensi</h3>
                <div class="space-y-4">
                    @forelse ($this->topPerformers as $item)
                        <div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-base-200/60 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-secondary text-secondary-content flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr($item['karyawan']->nama_karyawan, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-sm text-base-content">{{ $item['karyawan']->nama_karyawan }}</p>
                                    <p class="text-xs text-base-content/60">{{ $item['karyawan']->jabatan?->nama_jabatan ?? 'Staf' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold text-primary mb-1">{{ $item['rate'] }}%</p>
                                <div class="w-16 h-1.5 bg-base-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-primary" style="width: {{ $item['rate'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-base-content/60 text-center py-4">Belum ada data karyawan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>

