<?php

use App\Models\Absen;
use App\Models\Karyawan;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use Toast, WithPagination;

    public string $search = '';
    public string $bulan = '';
    public string $tahun = '';
    public string $perPage = '10';

    public array $listBulan = [];
    public array $listTahun = [];

    public function boot(): void
    {
        $this->listBulan = [
            ['id' => '01', 'name' => 'Januari'], ['id' => '02', 'name' => 'Februari'],
            ['id' => '03', 'name' => 'Maret'], ['id' => '04', 'name' => 'April'],
            ['id' => '05', 'name' => 'Mei'], ['id' => '06', 'name' => 'Juni'],
            ['id' => '07', 'name' => 'Juli'], ['id' => '08', 'name' => 'Agustus'],
            ['id' => '09', 'name' => 'September'], ['id' => '10', 'name' => 'Oktober'],
            ['id' => '11', 'name' => 'November'], ['id' => '12', 'name' => 'Desember'],
        ];

        $now = now()->setTimezone('Asia/Makassar');
        $this->listTahun = collect(range($now->year - 2, $now->year + 1))
            ->map(fn($y) => ['id' => (string) $y, 'name' => (string) $y])
            ->toArray();

        if (empty($this->bulan)) $this->bulan = $now->format('m');
        if (empty($this->tahun)) $this->tahun = (string) $now->year;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingBulan(): void
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

    #[Computed]
    public function karyawans()
    {
        $bulan = (int) $this->bulan;
        $tahun = (int) $this->tahun;
        $lastDay = Carbon::create($tahun, $bulan)->endOfMonth();

        return Karyawan::with('jabatan')
            ->where('tanggal_masuk', '<=', $lastDay)
            ->where(function ($q) use ($tahun, $bulan) {
                $q->where('is_active', true)
                    ->orWhereHas('absens', function ($aq) use ($tahun, $bulan) {
                        $aq->whereYear('tanggal_absen', $tahun)
                           ->whereMonth('tanggal_absen', $bulan);
                    });
            })
            ->when($this->search, function ($q) {
                $term = trim($this->search);
                $q->where(function ($sub) use ($term) {
                    $sub->where('nama_karyawan', 'like', "%{$term}%")
                        ->orWhere('nik', 'like', "%{$term}%");
                });
            })
            ->orderBy('nama_karyawan')
            ->paginate((int) $this->perPage);
    }

    #[Computed]
    public function absenRecords()
    {
        $karyawanIds = $this->karyawans->pluck('id');
        if ($karyawanIds->isEmpty()) {
            return collect();
        }

        return Absen::whereIn('karyawan_id', $karyawanIds)
            ->whereYear('tanggal_absen', (int) $this->tahun)
            ->whereMonth('tanggal_absen', (int) $this->bulan)
            ->get()
            ->groupBy('karyawan_id');
    }

    public function with(): array
    {
        return [
            'karyawans' => $this->karyawans,
            'absenRecords' => $this->absenRecords,
            'namaBulan' => collect($this->listBulan)->firstWhere('id', $this->bulan)['name'] ?? '',
            'totalHari' => Carbon::create((int) $this->tahun, (int) $this->bulan)->daysInMonth,
        ];
    }

    public static function cellInfo($absen, int $day): array
    {
        $altBg = $day % 2 === 0 ? 'bg-base-200' : 'bg-base-100';

        if (!$absen) {
            return ['letter' => '', 'class' => $altBg];
        }

        return match ($absen->keterangan) {
            'Hadir' => ['letter' => '', 'class' => 'bg-base-100'],
            'Dinas Luar' => ['letter' => '}', 'class' => 'bg-base-100'],
            'Cuti' => ['letter' => 'C', 'class' => 'bg-green-600 text-white font-bold'],
            'Sakit' => ['letter' => 'S', 'class' => 'bg-yellow-400 font-bold'],
            'Izin' => ['letter' => 'I', 'class' => 'bg-orange-400 text-white font-bold'],
            'Alpa' => ['letter' => 'A', 'class' => 'bg-red-600 text-white font-bold'],
            'Libur' => ['letter' => 'L', 'class' => 'bg-gray-200'],
            'Off' => ['letter' => 'O', 'class' => 'bg-gray-400 font-bold'],
            'Lainnya' => ['letter' => 'X', 'class' => 'bg-blue-200 font-bold'],
            default => ['letter' => '', 'class' => $altBg],
        };
    }
};
?>

<div>
    <x-header title="Laporan Absensi Karyawan" separator progress-indicator />

    {{-- Filters --}}
    <section class="flex flex-wrap items-end gap-3 mb-2 p-3 bg-base-200 rounded-xl">
        <fieldset class="fieldset flex-1 min-w-[200px]">
            <legend class="fieldset-legend text-xs">Cari Karyawan</legend>
            <input type="text" class="input input-bordered input-sm w-full" placeholder="Cari nama/NIK..."
                wire:model.live.debounce="search" />
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Bulan</legend>
            <select class="select select-bordered select-sm w-36" wire:model.live="bulan">
                @foreach ($listBulan as $b)
                    <option value="{{ $b['id'] }}">{{ $b['name'] }}</option>
                @endforeach
            </select>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Tahun</legend>
            <select class="select select-bordered select-sm w-28" wire:model.live="tahun">
                @foreach ($listTahun as $t)
                    <option value="{{ $t['id'] }}">{{ $t['name'] }}</option>
                @endforeach
            </select>
        </fieldset>
        <a href="{{ route('absen.laporan-bulanan.pdf', ['bulan' => $bulan, 'tahun' => $tahun, 'search' => $search]) }}"
            class="btn btn-outline btn-sm btn-error">
            <x-icon name="o-document-arrow-down" class="w-4 h-4" />
            Export PDF
        </a>
    </section>

    {{-- Legend --}}
    <div class="flex flex-wrap gap-4 px-4 py-2 mb-2 bg-base-200/50 rounded-lg text-xs">
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-base-100 border border-base-300"></span> Hadir
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-base-100 border border-base-300 font-bold text-center text-[10px] leading-3">}</span> Dinas Luar
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-green-600"></span> Cuti
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-yellow-400"></span> Sakit
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-orange-400"></span> Izin
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-red-600"></span> Alpa
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-gray-200"></span> Libur
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-gray-400 font-bold text-center text-[10px] leading-3">O</span> Off
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-blue-200 font-bold text-center text-[10px] leading-3">X</span> Lainnya
        </span>
    </div>

    {{-- Table --}}
    <div class="bg-base-100 border rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-xs" wire:key="laporan-{{ $bulan }}-{{ $tahun }}">
                <thead>
                    {{-- Header Row 1 --}}
                    <tr class="bg-base-200 border-b">
                        <th class="px-3 py-2 text-left font-semibold text-base-content/70 border-r min-w-[140px]" rowspan="2">NAMA</th>
                        <th class="px-3 py-2 text-left font-semibold text-base-content/70 border-r min-w-[90px]" rowspan="2">JABATAN</th>
                        <th class="px-3 py-2 text-center font-semibold text-base-content/70 border-r" colspan="{{ $totalHari }}">
                            Hari Kerja (HK) di Bulan {{ $namaBulan }} {{ $tahun }}
                        </th>
                        <th class="px-2 py-2 text-center font-semibold text-base-content/70 border-r" rowspan="2">HK</th>
                        <th class="px-2 py-2 text-center font-semibold text-white bg-green-600 border-r" rowspan="2">C</th>
                        <th class="px-2 py-2 text-center font-semibold text-black bg-yellow-400 border-r" rowspan="2">S</th>
                        <th class="px-2 py-2 text-center font-semibold text-white bg-orange-400 border-r" rowspan="2">I</th>
                        <th class="px-2 py-2 text-center font-semibold text-white bg-red-600 border-r" rowspan="2">A</th>
                        <th class="px-2 py-2 text-center font-semibold text-base-content/70" rowspan="2">%</th>
                    </tr>
                    {{-- Header Row 2 --}}
                    <tr class="bg-base-200 border-b">
                        @for ($d = 1; $d <= $totalHari; $d++)
                            <th class="w-7 py-1.5 text-center font-medium text-[10px] text-base-content/50 border-r {{ $d % 2 === 0 ? 'bg-base-200' : 'bg-base-100' }}">
                                {{ $d }}
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($karyawans as $k)
                        @php
                            $records = ($absenRecords[$k->id] ?? collect())->keyBy(fn($a) => (int) $a->tanggal_absen->format('d'));
                            $allRecords = $absenRecords[$k->id] ?? collect();
                            $hk = $allRecords->count();
                            $hadir = $allRecords->where('keterangan', 'Hadir')->count();
                            $dn = $allRecords->where('keterangan', 'Dinas Luar')->count();
                            $cuti = $allRecords->where('keterangan', 'Cuti')->count();
                            $sakit = $allRecords->where('keterangan', 'Sakit')->count();
                            $izin = $allRecords->where('keterangan', 'Izin')->count();
                            $alpa = $allRecords->where('keterangan', 'Alpa')->count();
                            $off = $allRecords->where('keterangan', 'Off')->count();
                            $libur = $allRecords->where('keterangan', 'Libur')->count();
                            $persen = $totalHari > 0 ? round((($hadir + $dn + $cuti + $off + $libur) / $totalHari) * 100) : 0;
                            $isAlumni = !$k->is_active;
                        @endphp
                        <tr class="hover:bg-base-200/50 transition-colors">
                            <td class="px-3 py-1.5 font-medium border-r whitespace-nowrap">
                                {{ $k->nama_karyawan }}
                                @if ($isAlumni)
                                    <span class="badge badge-xs badge-ghost ml-1">Alumni</span>
                                @endif
                            </td>
                            <td class="px-3 py-1.5 text-base-content/60 border-r whitespace-nowrap">{{ $k->jabatan?->nama_jabatan ?? '-' }}</td>
                            @for ($d = 1; $d <= $totalHari; $d++)
                                @php $info = $this->cellInfo($records->get($d), $d); @endphp
                                <td class="w-7 py-1.5 text-center border-r {{ $info['class'] }}">
                                    <span class="text-[11px] {{ $info['letter'] ? 'font-bold' : '' }}">{{ $info['letter'] }}</span>
                                </td>
                            @endfor
                            <td class="px-2 py-1.5 text-center font-medium border-r">{{ $hk ?: '-' }}</td>
                            <td class="px-2 py-1.5 text-center font-bold text-white bg-green-600 border-r">{{ $cuti ?: '-' }}</td>
                            <td class="px-2 py-1.5 text-center font-bold bg-yellow-400 border-r">{{ $sakit ?: '-' }}</td>
                            <td class="px-2 py-1.5 text-center font-bold text-white bg-orange-400 border-r">{{ $izin ?: '-' }}</td>
                            <td class="px-2 py-1.5 text-center font-bold text-white bg-red-600 border-r">{{ $alpa ?: '-' }}</td>
                            <td class="px-2 py-1.5 text-center font-medium">{{ $hk ? $persen . '%' : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $totalHari + 8 }}" class="text-center py-8 text-base-content/40">
                                Tidak ada data untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        <div class="flex items-center justify-between px-4 py-3 border-t">
            <div class="flex items-center gap-2 text-sm text-base-content/60">
                <span>Tampilkan</span>
                <select class="select select-bordered select-xs" wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>baris</span>
            </div>
            @if ($karyawans->hasPages())
                {{ $karyawans->links('livewire::tailwind') }}
            @endif
        </div>
    </div>
</div>
