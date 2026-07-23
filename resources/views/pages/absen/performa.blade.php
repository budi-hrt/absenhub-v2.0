<?php

use App\Models\Absen;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Layout('layouts.app')] #[Title('Performa Absensi Karyawan')] class extends Component {
    use Toast, WithPagination;

    public string $search = '';
    public string $filterStatus = 'tetap'; // Default tab 'tetap' (Karyawan Tetap)
    public string $tahun = '';
    public string $perPage = '10';

    public array $listTahun = [];

    // Modal state for detail rekapan
    public bool $detailModal = false;
    public ?Karyawan $selectedKaryawan = null;
    public array $selectedKaryawanRekap = [];

    public function boot(): void
    {
        $now = now();
        $this->listTahun = range($now->year - 3, $now->year + 1);

        if (empty($this->tahun)) {
            $this->tahun = (string) $now->year;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingTahun(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function showDetail(int $karyawanId): void
    {
        $tahun = (int) $this->tahun;
        $this->selectedKaryawan = Karyawan::with(['jabatan', 'status'])->find($karyawanId);

        if (! $this->selectedKaryawan) {
            return;
        }

        $allRecords = Absen::where('karyawan_id', $karyawanId)
            ->whereYear('tanggal_absen', $tahun)
            ->get();

        $hk = $allRecords->count();
        $hadir = $allRecords->where('keterangan', 'Hadir')->count();
        $dn = $allRecords->where('keterangan', 'Dinas Luar')->count();
        $cuti = $allRecords->where('keterangan', 'Cuti')->count();
        $sakit = $allRecords->where('keterangan', 'Sakit')->count();
        $izin = $allRecords->where('keterangan', 'Izin')->count();
        $alpa = $allRecords->where('keterangan', 'Alpa')->count();
        $off = $allRecords->where('keterangan', 'Off')->count();
        $libur = $allRecords->where('keterangan', 'Libur')->count();
        $lainnya = $allRecords->where('keterangan', 'Lainnya')->count();

        // Rumus persentase persis rekap-tahunan: 100 - (alpa*3) - (izin*2) - (sakit*1) - (lainnya*0.5)
        $persen = $hk > 0 ? max(0, round(100 - ($alpa * 3) - ($izin * 2) - ($sakit * 1) - ($lainnya * 0.5), 1)) : 0;

        $this->selectedKaryawanRekap = [
            'hk' => $hk,
            'hadir' => $hadir,
            'dn' => $dn,
            'cuti' => $cuti,
            'sakit' => $sakit,
            'izin' => $izin,
            'alpa' => $alpa,
            'off' => $off,
            'libur' => $libur,
            'lainnya' => $lainnya,
            'persen' => $persen,
        ];

        $this->detailModal = true;
    }

    #[Computed]
    public function performanceData()
    {
        $tahun = (int) $this->tahun;

        $karyawans = Karyawan::with('jabatan')
            ->where('is_active', true)
            ->when($this->filterStatus === 'tetap', function ($q) {
                $q->where('status_id', 1);
            })
            ->when($this->filterStatus === 'kontrak', function ($q) {
                $q->where('status_id', 2);
            })
            ->when($this->search, function ($q) {
                $term = trim($this->search);
                $q->where(function ($sub) use ($term) {
                    $sub->where('nama_karyawan', 'like', "%{$term}%")
                        ->orWhere('nik', 'like', "%{$term}%");
                });
            })
            ->get();

        if ($karyawans->isEmpty()) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, (int) $this->perPage, $this->getPage());
        }

        $karyawanIds = $karyawans->pluck('id');

        $absenStats = DB::table('absens')
            ->whereIn('karyawan_id', $karyawanIds)
            ->whereYear('tanggal_absen', $tahun)
            ->select('karyawan_id', 'keterangan', DB::raw('count(*) as total'))
            ->groupBy('karyawan_id', 'keterangan')
            ->get()
            ->groupBy('karyawan_id');

        $allCalculated = $karyawans->map(function ($k) use ($absenStats) {
            $stats = $absenStats[$k->id] ?? collect();
            
            $hk = $stats->sum('total');
            $hadir = $stats->where('keterangan', 'Hadir')->sum('total');
            $dn = $stats->where('keterangan', 'Dinas Luar')->sum('total');
            $cuti = $stats->where('keterangan', 'Cuti')->sum('total');
            $sakit = $stats->where('keterangan', 'Sakit')->sum('total');
            $izin = $stats->where('keterangan', 'Izin')->sum('total');
            $alpa = $stats->where('keterangan', 'Alpa')->sum('total');
            $off = $stats->where('keterangan', 'Off')->sum('total');
            $libur = $stats->where('keterangan', 'Libur')->sum('total');
            $lainnya = $stats->where('keterangan', 'Lainnya')->sum('total');

            // Rumus persentase persis rekap-tahunan: 100 - (alpa*3) - (izin*2) - (sakit*1) - (lainnya*0.5)
            $persen = $hk > 0 ? max(0, round(100 - ($alpa * 3) - ($izin * 2) - ($sakit * 1) - ($lainnya * 0.5), 1)) : 0;

            return [
                'karyawan' => $k,
                'hk' => $hk,
                'hadir' => $hadir,
                'dn' => $dn,
                'cuti' => $cuti,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpa' => $alpa,
                'off' => $off,
                'libur' => $libur,
                'lainnya' => $lainnya,
                'rate' => $persen,
            ];
        })
        ->sort(function ($a, $b) {
            if ($a['rate'] === $b['rate']) {
                return strcmp($a['karyawan']->nama_karyawan, $b['karyawan']->nama_karyawan);
            }
            return $b['rate'] <=> $a['rate'];
        })
        ->values();

        $currentPage = (int) $this->getPage();
        $perPageInt = (int) $this->perPage;
        $sliced = $allCalculated->slice(($currentPage - 1) * $perPageInt, $perPageInt)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $sliced,
            $allCalculated->count(),
            $perPageInt,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
    }

    #[Computed]
    public function keyMetrics(): array
    {
        $tahun = (int) $this->tahun;
        $karyawans = Karyawan::where('is_active', true)->get(['id']);
        $totalAktif = $karyawans->count();

        $absenStats = DB::table('absens')
            ->whereIn('karyawan_id', $karyawans->pluck('id'))
            ->whereYear('tanggal_absen', $tahun)
            ->select('karyawan_id', 'keterangan', DB::raw('count(*) as total'))
            ->groupBy('karyawan_id', 'keterangan')
            ->get()
            ->groupBy('karyawan_id');

        $rates = [];

        foreach ($karyawans as $k) {
            $stats = $absenStats[$k->id] ?? collect();
            $hk = $stats->sum('total');
            if ($hk > 0) {
                $sakit = $stats->where('keterangan', 'Sakit')->sum('total');
                $izin = $stats->where('keterangan', 'Izin')->sum('total');
                $alpa = $stats->where('keterangan', 'Alpa')->sum('total');
                $lainnya = $stats->where('keterangan', 'Lainnya')->sum('total');
                $rates[] = max(0, round(100 - ($alpa * 3) - ($izin * 2) - ($sakit * 1) - ($lainnya * 0.5), 1));
            } else {
                $rates[] = 0;
            }
        }

        $avgRate = count($rates) > 0 ? round(array_sum($rates) / count($rates), 1) : 0;
        $excellentCount = collect($rates)->filter(fn($r) => $r >= 95)->count();
        $needAttentionCount = collect($rates)->filter(fn($r) => $r < 85 && $r > 0)->count();

        return [
            'totalAktif' => $totalAktif,
            'avgRate' => $avgRate,
            'excellentCount' => $excellentCount,
            'needAttentionCount' => $needAttentionCount,
        ];
    }
}; ?>

<div class="space-y-6">
    <!-- Header Section -->
    <x-header title="Performa Absensi Karyawan" subtitle="Manajemen Absensi > Performa Karyawan" separator progress-indicator>
        <x-slot:actions>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-base-content/70">Tahun:</span>
                <select wire:model.live="tahun" class="select select-bordered select-sm text-xs font-semibold">
                    @foreach ($this->listTahun as $y)
                        <option value="{{ $y }}">Tahun {{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </x-slot:actions>
    </x-header>

    <!-- Filter Tabs Karyawan Tetap / Kontrak -->
    <div class="flex items-center justify-between gap-4">
        <div class="inline-flex bg-base-200 p-1 rounded-xl border border-base-300 gap-1">
            <button wire:click="$set('filterStatus', 'tetap')" class="px-4 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer {{ $filterStatus === 'tetap' ? 'bg-primary text-primary-content shadow-xs' : 'text-base-content/70 hover:bg-base-300/50' }}">
                <span>Karyawan Tetap</span>
            </button>
            <button wire:click="$set('filterStatus', 'kontrak')" class="px-4 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer {{ $filterStatus === 'kontrak' ? 'bg-primary text-primary-content shadow-xs' : 'text-base-content/70 hover:bg-base-300/50' }}">
                <span>Karyawan Kontrak</span>
            </button>
        </div>
    </div>

    <!-- Key Metrics Cards (Minimalist 4 Cards) -->
    <section wire:loading.class="opacity-50 pointer-events-none" wire:target="tahun" class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 transition-opacity duration-150">

        <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Total Karyawan</p>
                <h3 class="text-xl md:text-2xl font-bold text-base-content mt-0.5">{{ number_format($this->keyMetrics['totalAktif']) }}</h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                <x-icon name="o-users" class="w-5 h-5" />
            </div>
        </div>

        <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Rata-rata Performa</p>
                <h3 class="text-xl md:text-2xl font-bold text-primary mt-0.5">{{ $this->keyMetrics['avgRate'] }}%</h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-success/10 text-success flex items-center justify-center">
                <x-icon name="o-chart-bar" class="w-5 h-5" />
            </div>
        </div>

        <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Performa Baik (≥95%)</p>
                <h3 class="text-xl md:text-2xl font-bold text-success mt-0.5">{{ number_format($this->keyMetrics['excellentCount']) }}</h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-info/10 text-info flex items-center justify-center">
                <x-icon name="o-check-badge" class="w-5 h-5" />
            </div>
        </div>

        <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Perhatian (&lt;85%)</p>
                <h3 class="text-xl md:text-2xl font-bold text-error mt-0.5">{{ number_format($this->keyMetrics['needAttentionCount']) }}</h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-error/10 text-error flex items-center justify-center">
                <x-icon name="o-exclamation-triangle" class="w-5 h-5" />
            </div>
        </div>
    </section>

    <!-- Performance Table Section -->
    <section class="bg-base-100 border border-base-300 rounded-2xl overflow-hidden shadow-sm relative min-h-[20rem]">
        <div wire:loading wire:target="tahun, filterStatus, search, perPage, gotoPage, nextPage, previousPage" class="absolute inset-0 bg-base-100/30 backdrop-blur-[1px] z-50 transition-all duration-150">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center gap-2">
                <span class="loading loading-spinner loading-lg text-primary"></span>
                <span class="text-xs font-bold text-primary/70 tracking-wider uppercase animate-pulse">Memuat...</span>
            </div>
        </div>
        <div wire:loading.class="opacity-25 pointer-events-none" wire:target="tahun, filterStatus, search, perPage, gotoPage, nextPage, previousPage" class="transition-opacity duration-150">
            <div class="p-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-base-300 bg-base-200/40">
            <h3 class="text-base font-bold text-base-content">Daftar Performa Absensi ({{ $filterStatus === 'tetap' ? 'Karyawan Tetap' : 'Karyawan Kontrak' }})</h3>
            
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <x-icon name="o-magnifying-glass" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-base-content/50" />
                    <input wire:model.live.debounce.300ms="search" type="text" class="input input-bordered input-sm w-full pl-9 text-xs" placeholder="Cari nama atau NIK..." />
                </div>
                
                <a href="{{ route('absen.performa.pdf', ['tahun' => $tahun, 'search' => $search, 'filterStatus' => $filterStatus]) }}" target="_blank" class="btn btn-primary btn-sm text-xs gap-1.5">
                    <x-icon name="o-arrow-down-tray" class="w-4 h-4" />
                    Export PDF
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-base-200/80 border-b border-base-300 text-xs font-semibold text-base-content/70 uppercase tracking-wider">
                        <th class="px-5 py-3">Nama Karyawan</th>
                        <th class="px-5 py-3">Lama Kerja</th>
                        <th class="px-5 py-3 text-center">HK</th>
                        <th class="px-5 py-3 text-center">Izin/Sakit</th>
                        <th class="px-5 py-3 text-center">Alpa</th>
                        <th class="px-5 py-3">Performa %</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-300/60 text-sm">
                    @forelse ($this->performanceData as $item)
                        @php
                            $k = $item['karyawan'];
                            $rate = $item['rate'];
                            $lamaKerja = $k->tanggal_masuk 
                                ? \Carbon\Carbon::parse($k->tanggal_masuk)->locale('id')->diffForHumans(now(), ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) 
                                : '-';
                        @endphp
                        <tr class="hover:bg-base-200/40 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="avatar {{ !$k->foto_karyawan ? 'placeholder' : '' }}">
                                        <div class="mask mask-squircle w-10 h-10 {{ !$k->foto_karyawan ? 'bg-primary/10 text-primary border border-primary/20 flex items-center justify-center font-bold text-xs' : '' }}">
                                            @if ($k->foto_karyawan)
                                                <img src="{{ Storage::url($k->foto_karyawan) }}" alt="{{ $k->nama_karyawan }}" />
                                            @else
                                                <span>{{ strtoupper(substr($k->nama_karyawan, 0, 2)) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-base-content text-sm">{{ $k->nama_karyawan }}</p>
                                        <p class="text-xs text-base-content/60">{{ $k->jabatan?->nama_jabatan ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-base-content/70 text-xs font-medium">
                                {{ $lamaKerja }}
                            </td>
                            <td class="px-5 py-3 text-center text-xs font-medium">{{ $item['hk'] ?: '-' }}</td>
                            <td class="px-5 py-3 text-center text-xs font-semibold text-amber-700">{{ ($item['izin'] + $item['sakit']) ?: '-' }}</td>
                            <td class="px-5 py-3 text-center text-xs font-semibold text-rose-700">{{ $item['alpa'] ?: '-' }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-20 h-1.5 bg-base-200 rounded-full overflow-hidden">
                                        <div class="h-full {{ $rate >= 90 ? 'bg-success' : ($rate >= 80 ? 'bg-warning' : 'bg-error') }}" style="width: {{ $rate }}%"></div>
                                    </div>
                                    <span class="font-bold text-xs text-base-content">{{ $rate }}%</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <button wire:click="showDetail({{ $k->id }})" class="btn btn-ghost btn-xs text-primary gap-1" title="Lihat Rekapan">
                                    <x-icon name="o-eye" class="w-4 h-4" />
                                    <span>Rekap</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-base-content/50">
                                Tidak ada data performa karyawan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-base-300 flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-base-content/60">
                <span>Tampilkan</span>
                <select wire:model.live="perPage" class="select select-bordered select-xs">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
                <span>baris</span>
            </div>
            @if ($this->performanceData->hasPages())
                {{ $this->performanceData->links('livewire::tailwind') }}
            @endif
        </div>
        </div>
    </section>

    <!-- Modal Rekapan Performa Detail -->
    <x-modal wire:model="detailModal" title="Detail Rekap Performa Absensi" class="backdrop-blur-xs">
        @if ($selectedKaryawan)
            <div class="space-y-4">
                <div class="flex items-center gap-4 p-3 bg-base-200/50 rounded-xl border border-base-300">
                    <div class="avatar {{ !$selectedKaryawan->foto_karyawan ? 'placeholder' : '' }}">
                        <div class="mask mask-squircle w-12 h-12 {{ !$selectedKaryawan->foto_karyawan ? 'bg-primary/10 text-primary border border-primary/20 flex items-center justify-center font-bold text-sm' : '' }}">
                            @if ($selectedKaryawan->foto_karyawan)
                                <img src="{{ Storage::url($selectedKaryawan->foto_karyawan) }}" alt="{{ $selectedKaryawan->nama_karyawan }}" />
                            @else
                                <span>{{ strtoupper(substr($selectedKaryawan->nama_karyawan, 0, 2)) }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-base text-base-content">{{ $selectedKaryawan->nama_karyawan }}</h4>
                        <p class="text-xs text-base-content/60">NIK: {{ $selectedKaryawan->nik }} | Jabatan: {{ $selectedKaryawan->jabatan?->nama_jabatan ?? '-' }}</p>
                        <p class="text-xs text-primary font-medium mt-0.5">Tahun Periode: {{ $tahun }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 text-center text-xs">
                    <div class="p-2.5 bg-base-200/60 rounded-lg border border-base-300">
                        <span class="text-base-content/60 block text-[10px] uppercase font-semibold">HK (Hari Kerja)</span>
                        <span class="font-bold text-sm text-base-content">{{ $selectedKaryawanRekap['hk'] ?? 0 }}</span>
                    </div>
                    <div class="p-2.5 bg-emerald-50 rounded-lg border border-emerald-200">
                        <span class="text-emerald-700 block text-[10px] uppercase font-semibold">Hadir</span>
                        <span class="font-bold text-sm text-emerald-800">{{ $selectedKaryawanRekap['hadir'] ?? 0 }}</span>
                    </div>
                    <div class="p-2.5 bg-sky-50 rounded-lg border border-sky-200">
                        <span class="text-sky-700 block text-[10px] uppercase font-semibold">Dinas Luar</span>
                        <span class="font-bold text-sm text-sky-800">{{ $selectedKaryawanRekap['dn'] ?? 0 }}</span>
                    </div>
                    <div class="p-2.5 bg-green-50 rounded-lg border border-green-200">
                        <span class="text-green-700 block text-[10px] uppercase font-semibold">Cuti</span>
                        <span class="font-bold text-sm text-green-800">{{ $selectedKaryawanRekap['cuti'] ?? 0 }}</span>
                    </div>
                    <div class="p-2.5 bg-amber-50 rounded-lg border border-amber-200">
                        <span class="text-amber-700 block text-[10px] uppercase font-semibold">Sakit</span>
                        <span class="font-bold text-sm text-amber-800">{{ $selectedKaryawanRekap['sakit'] ?? 0 }}</span>
                    </div>
                    <div class="p-2.5 bg-orange-50 rounded-lg border border-orange-200">
                        <span class="text-orange-700 block text-[10px] uppercase font-semibold">Izin</span>
                        <span class="font-bold text-sm text-orange-800">{{ $selectedKaryawanRekap['izin'] ?? 0 }}</span>
                    </div>
                    <div class="p-2.5 bg-rose-50 rounded-lg border border-rose-200">
                        <span class="text-rose-700 block text-[10px] uppercase font-semibold">Alpa</span>
                        <span class="font-bold text-sm text-rose-800">{{ $selectedKaryawanRekap['alpa'] ?? 0 }}</span>
                    </div>
                    <div class="p-2.5 bg-purple-50 rounded-lg border border-purple-200">
                        <span class="text-purple-700 block text-[10px] uppercase font-semibold">Performa %</span>
                        <span class="font-bold text-sm text-purple-900">{{ $selectedKaryawanRekap['persen'] ?? 0 }}%</span>
                    </div>
                </div>
            </div>
        @endif

        <x-slot:actions>
            <x-button label="Tutup" @click="$wire.detailModal = false" class="btn-ghost btn-sm" />
            @if ($selectedKaryawan)
                <a href="{{ route('absen.detail-harian', ['karyawan_id' => $selectedKaryawan->id]) }}" wire:navigate class="btn btn-primary btn-sm">
                    Lihat Detail Harian
                </a>
            @endif
        </x-slot:actions>
    </x-modal>
</div>
