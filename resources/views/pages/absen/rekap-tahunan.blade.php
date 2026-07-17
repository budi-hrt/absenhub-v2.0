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
    public string $tahun = '';
    public string $perPage = '10';

    public array $listTahun = [];

    public function boot(): void
    {
        $now = now()->setTimezone('Asia/Makassar');
        $this->listTahun = collect(range($now->year - 2, $now->year + 1))
            ->map(fn($y) => ['id' => (string) $y, 'name' => (string) $y])
            ->toArray();

        if (empty($this->tahun)) $this->tahun = (string) $now->year;
    }

    public function updatingSearch(): void
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
        $tahun = (int) $this->tahun;
        $lastDay = Carbon::create($tahun, 12)->endOfYear();

        return Karyawan::with('jabatan')
            ->where('tanggal_masuk', '<=', $lastDay)
            ->where(function ($q) use ($tahun) {
                $q->where('is_active', true)
                    ->orWhereHas('absens', function ($aq) use ($tahun) {
                        $aq->whereYear('tanggal_absen', $tahun);
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
            ->get()
            ->groupBy('karyawan_id');
    }

    public function with(): array
    {
        $tahun = (int) $this->tahun;
        return [
            'karyawans' => $this->karyawans,
            'absenRecords' => $this->absenRecords,
            'totalHari' => Carbon::create($tahun, 12)->endOfYear()->dayOfYear,
        ];
    }
};
?>

<div>
    <x-header title="Rekap Tahunan" separator progress-indicator />

    {{-- Filters --}}
    <section class="flex flex-wrap items-end gap-3 mb-2 p-3 bg-base-200 rounded-xl">
        <fieldset class="fieldset flex-1 min-w-[200px]">
            <legend class="fieldset-legend text-xs">Cari Karyawan</legend>
            <input type="text" class="input input-bordered input-sm w-full" placeholder="Cari nama/NIK..."
                wire:model.live.debounce="search" />
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Tahun</legend>
            <select class="select select-bordered select-sm w-28" wire:model.live="tahun">
                @foreach ($listTahun as $t)
                    <option value="{{ $t['id'] }}">{{ $t['name'] }}</option>
                @endforeach
            </select>
        </fieldset>
    </section>

    {{-- Table --}}
    <div class="bg-base-100 border rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-xs" wire:key="rekap-tahunan-{{ $tahun }}">
                <thead>
                    <tr class="bg-base-200 border-b">
                        <th class="px-2 py-2 text-left font-semibold text-base-content/70 border-r w-[120px]">NAMA</th>
                        <th class="px-2 py-2 text-left font-semibold text-base-content/70 border-r w-[60px]">JABATAN</th>
                        <th class="px-2 py-2 text-center font-semibold text-base-content/70 border-r w-12" title="Hari Kerja">HK</th>
                        <th class="px-2 py-2 text-center font-semibold text-emerald-700 bg-emerald-50 border-r w-12">Hadir</th>
                        <th class="px-2 py-2 text-center font-semibold text-sky-700 bg-sky-100 border-r w-12">DL</th>
                        <th class="px-2 py-2 text-center font-semibold text-green-700 bg-green-100 border-r w-12">Cuti</th>
                        <th class="px-2 py-2 text-center font-semibold border-r w-12 bg-yellow-100">Sakit</th>
                        <th class="px-2 py-2 text-center font-semibold text-orange-700 bg-orange-100 border-r w-12">Izin</th>
                        <th class="px-2 py-2 text-center font-semibold text-red-700 bg-red-100 border-r w-12">Alpa</th>
                        <th class="px-2 py-2 text-center font-semibold text-gray-600 bg-gray-200 border-r w-10">Off</th>
                        <th class="px-2 py-2 text-center font-semibold text-gray-500 bg-gray-100 border-r w-12">Libur</th>
                        <th class="px-2 py-2 text-center font-semibold text-blue-700 bg-blue-100 border-r w-12">Lain</th>
                        <th class="px-2 py-2 text-center font-semibold text-base-content/70 w-12">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($karyawans as $k)
                        @php
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
                            $lainnya = $allRecords->where('keterangan', 'Lainnya')->count();
                            $persen = $totalHari > 0 ? round((($hadir + $dn + $cuti + $off + $libur) / $totalHari) * 100) : 0;
                            $isAlumni = !$k->is_active;
                        @endphp
                        <tr class="hover:bg-base-200/50 transition-colors">
                            <td class="px-2 py-1.5 font-medium border-r whitespace-nowrap overflow-hidden text-ellipsis max-w-[120px]" title="{{ $k->nama_karyawan }}">
                                {{ $k->nama_karyawan }}
                                @if ($isAlumni)
                                    <span class="badge badge-xs badge-ghost ml-1">Alumni</span>
                                @endif
                            </td>
                            <td class="px-2 py-1.5 text-base-content/60 border-r whitespace-nowrap overflow-hidden text-ellipsis max-w-[60px]" title="{{ $k->jabatan?->nama_jabatan ?? '-' }}">{{ $k->jabatan?->nama_jabatan ?? '-' }}</td>
                            <td class="px-2 py-1.5 text-center font-medium border-r">{{ $hk ?: '-' }}</td>
                            <td class="px-2 py-1.5 text-center font-medium text-emerald-700 bg-emerald-50 border-r">{{ $hadir ?: '-' }}</td>
                            <td class="px-2 py-1.5 text-center font-medium text-sky-700 bg-sky-100 border-r">{{ $dn ?: '-' }}</td>
                            <td class="px-2 py-1.5 text-center font-medium text-green-700 bg-green-100 border-r">{{ $cuti ?: '-' }}</td>
                            <td class="px-2 py-1.5 text-center font-medium border-r bg-yellow-100">{{ $sakit ?: '-' }}</td>
                            <td class="px-2 py-1.5 text-center font-medium text-orange-700 bg-orange-100 border-r">{{ $izin ?: '-' }}</td>
                            <td class="px-2 py-1.5 text-center font-medium text-red-700 bg-red-100 border-r">{{ $alpa ?: '-' }}</td>
                            <td class="px-2 py-1.5 text-center font-medium text-gray-600 bg-gray-200 border-r">{{ $off ?: '-' }}</td>
                            <td class="px-2 py-1.5 text-center font-medium text-gray-500 bg-gray-100 border-r">{{ $libur ?: '-' }}</td>
                            <td class="px-2 py-1.5 text-center font-medium text-blue-700 bg-blue-100 border-r">{{ $lainnya ?: '-' }}</td>
                            <td class="px-2 py-1.5 text-center font-medium">{{ $totalHari ? $persen . '%' : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center py-8 text-base-content/40">
                                Tidak ada data untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
