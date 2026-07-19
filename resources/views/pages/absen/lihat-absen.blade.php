<?php

use App\Models\Absen;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component
{
    use Toast, WithPagination;

    public string $search = '';

    public string $filterTanggalAwal = '';

    public string $filterTanggalAkhir = '';

    public string $filterKeterangan = '';

    public string $dateError = '';

    public bool $showData = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterTanggalAwal(): void
    {
        $this->filterTanggalAkhir = '';
        $this->showData = false;
        $this->dateError = '';
        $this->resetPage();
    }

    public function updatedFilterTanggalAkhir(): void
    {
        $this->showData = false;
        $this->dateError = '';
        $this->resetPage();
    }

    public function submitFilter(): void
    {
        $this->dateError = '';

        if (empty($this->filterTanggalAwal)) {
            $this->dateError = 'Tanggal awal wajib diisi.';
            $this->showData = false;

            return;
        }

        if (empty($this->filterTanggalAkhir)) {
            $this->dateError = 'Tanggal akhir wajib diisi.';
            $this->showData = false;

            return;
        }

        if ($this->filterTanggalAwal > $this->filterTanggalAkhir) {
            $this->dateError = 'Tanggal akhir tidak boleh sebelum tanggal awal.';
            $this->showData = false;

            return;
        }

        if (Carbon::parse($this->filterTanggalAwal)->diffInDays($this->filterTanggalAkhir) > 31) {
            $this->dateError = 'Maksimal rentang tanggal adalah 31 hari. Silakan perkecil rentang tanggal.';
            $this->showData = false;

            return;
        }

        $this->showData = true;
        $this->resetPage();
    }

    public function setHariIni(): void
    {
        $today = now()->format('Y-m-d');
        $this->filterTanggalAwal = $today;
        $this->filterTanggalAkhir = $today;
        $this->submitFilter();
    }

    public function setKemarin(): void
    {
        $yesterday = now()->subDay()->format('Y-m-d');
        $this->filterTanggalAwal = $yesterday;
        $this->filterTanggalAkhir = $yesterday;
        $this->submitFilter();
    }

    public function updatingFilterKeterangan(): void
    {
        $this->resetPage();
    }

    protected function getFilteredQuery()
    {
        if (! $this->showData || empty($this->filterTanggalAwal) || empty($this->filterTanggalAkhir)) {
            return Absen::with('karyawan.jabatan')->whereRaw('0=1');
        }

        return Absen::with('karyawan.jabatan')
            ->whereBetween('tanggal_absen', [$this->filterTanggalAwal, $this->filterTanggalAkhir])
            ->when($this->filterKeterangan, fn ($q) => $q->where('keterangan', $this->filterKeterangan))
            ->when($this->search, function ($q) {
                $term = trim($this->search);
                $q->whereHas('karyawan', function ($kq) use ($term) {
                    $kq->where(function ($sub) use ($term) {
                        $sub->where('nama_karyawan', 'like', "%{$term}%")
                            ->orWhere('nik', 'like', "%{$term}%");
                    });
                });
            })
            ->orderBy('tanggal_absen', 'desc')
            ->orderBy('karyawan_id');
    }

    #[Computed]
    public function absens()
    {
        return $this->getFilteredQuery()->paginate(10);
    }

    #[Computed]
    public function rekap()
    {
        if (! $this->showData || empty($this->filterTanggalAwal) || empty($this->filterTanggalAkhir)) {
            return ['alpa' => 0, 'sakit' => 0, 'cuti' => 0, 'izin' => 0];
        }

        $query = Absen::whereBetween('tanggal_absen', [$this->filterTanggalAwal, $this->filterTanggalAkhir]);

        if ($this->search) {
            $term = trim($this->search);
            $query->whereHas('karyawan', function ($kq) use ($term) {
                $kq->where(function ($sub) use ($term) {
                    $sub->where('nama_karyawan', 'like', "%{$term}%")
                        ->orWhere('nik', 'like', "%{$term}%");
                });
            });
        }

        return [
            'alpa' => (clone $query)->where('keterangan', 'Alpa')->count(),
            'sakit' => (clone $query)->where('keterangan', 'Sakit')->count(),
            'cuti' => (clone $query)->where('keterangan', 'Cuti')->count(),
            'izin' => (clone $query)->where('keterangan', 'Izin')->count(),
        ];
    }

    public function headers(): array
    {
        return [
            ['key' => 'no', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'karyawan', 'label' => 'KARYAWAN', 'sortable' => false],
            ['key' => 'tanggal', 'label' => 'TANGGAL', 'sortable' => false],
            ['key' => 'keterangan', 'label' => 'KETERANGAN', 'class' => 'w-36'],
            ['key' => 'scan_in', 'label' => 'CHECK IN', 'sortable' => false],
            ['key' => 'scan_out', 'label' => 'CHECK OUT', 'sortable' => false],
            ['key' => 'terlambat', 'label' => 'TERLAMBAT', 'class' => 'w-28', 'sortable' => false],
        ];
    }

    public function with(): array
    {
        $absens = $this->absens;
        $start = $absens->firstItem() ?? 1;
        $absens->getCollection()->transform(fn ($item, $i) => tap($item)->setAttribute('row_no', $start + $i));

        return [
            'absens' => $absens,
            'headers' => $this->headers(),
            'rekap' => $this->rekap,
        ];
    }

    public static function getColor(string $keterangan): string
    {
        return match ($keterangan) {
            'Hadir' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'Dinas Luar' => 'bg-sky-100 text-sky-700 border-sky-200',
            'Cuti' => 'bg-violet-100 text-violet-700 border-violet-200',
            'Sakit' => 'bg-amber-100 text-amber-700 border-amber-200',
            'Izin' => 'bg-slate-100 text-slate-600 border-slate-200',
            'Tidak Absen' => 'bg-orange-100 text-orange-700 border-orange-200',
            'Alpa' => 'bg-red-100 text-red-700 border-red-200',
            'Off' => 'bg-gray-100 text-gray-500 border-gray-200',
            'Libur' => 'bg-cyan-100 text-cyan-700 border-cyan-200',
            'Lainnya' => 'bg-pink-100 text-pink-700 border-pink-200',
            default => 'bg-gray-100 text-gray-500 border-gray-200',
        };
    }
};
?>

<div>
    <x-header title="Lihat Absensi" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Cari nama/NIK..." wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            @if ($showData && $filterTanggalAwal && $filterTanggalAkhir)
                <a href="{{ route('absen.lihat.pdf', [
                    'tanggal_awal' => $filterTanggalAwal,
                    'tanggal_akhir' => $filterTanggalAkhir,
                    'keterangan' => $filterKeterangan,
                    'search' => $search
                ]) }}" target="_blank" class="btn btn-error btn-sm text-white">
                    <x-icon name="o-document-arrow-down" class="w-4 h-4" />
                    Export PDF (F4)
                </a>
            @endif
        </x-slot:actions>
    </x-header>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-4 items-end mb-6 bg-base-200/50 p-4 rounded-xl border border-base-200">
        <fieldset class="fieldset p-0 m-0">
            <legend class="fieldset-legend text-xs font-semibold">Tanggal Awal</legend>
            <input type="date" class="input input-bordered input-sm w-44" wire:model.live="filterTanggalAwal" />
        </fieldset>
        <fieldset class="fieldset p-0 m-0">
            <legend class="fieldset-legend text-xs font-semibold">Tanggal Akhir</legend>
            <input type="date" class="input input-bordered input-sm w-44" wire:model.live="filterTanggalAkhir" />
        </fieldset>

        <div>
            <button class="btn btn-primary btn-sm px-4" 
                wire:click="submitFilter" 
                spinner="submitFilter"
                @if (empty($filterTanggalAwal) || empty($filterTanggalAkhir)) disabled @endif>
                <x-icon name="o-magnifying-glass" class="w-4 h-4" />
                Lihat Absen
            </button>
        </div>

        <fieldset class="fieldset p-0 m-0">
            <legend class="fieldset-legend text-xs font-semibold">Keterangan</legend>
            <select class="select select-bordered select-sm w-44" wire:model.live="filterKeterangan">
                <option value="">Semua</option>
                <option value="Hadir">Hadir</option>
                <option value="Dinas Luar">Dinas Luar</option>
                <option value="Cuti">Cuti</option>
                <option value="Sakit">Sakit</option>
                <option value="Izin">Izin</option>
                <option value="Tidak Absen">Tidak Absen</option>
                <option value="Alpa">Alpa</option>
                <option value="Off">Off</option>
                <option value="Libur">Libur</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </fieldset>

        <div class="flex items-end gap-2 ml-auto">
            <button class="btn btn-outline btn-sm" wire:click="setHariIni" spinner="setHariIni">
                <x-icon name="o-calendar-days" class="w-4 h-4" />
                Hari Ini
            </button>
            <button class="btn btn-outline btn-sm" wire:click="setKemarin" spinner="setKemarin">
                <x-icon name="o-arrow-uturn-left" class="w-4 h-4" />
                Kemarin
            </button>
        </div>
    </div>

    @if ($dateError)
        <div class="flex items-center gap-2 mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl">
            <x-icon name="o-exclamation-triangle" class="w-4 h-4 shrink-0" />
            <span>{{ $dateError }}</span>
        </div>
    @endif

    @if (!$showData)
        {{-- Guidance / Empty State --}}
        <div class="flex flex-col items-center justify-center py-16 px-4 bg-base-100 rounded-2xl border border-base-200 shadow-sm text-center">
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-4">
                <x-icon name="o-calendar" class="w-8 h-8" />
            </div>
            <h3 class="text-base font-bold text-base-content">Tentukan Periode Absensi</h3>
            <p class="text-xs text-base-content/60 max-w-sm mt-2 leading-relaxed">
                Silakan pilih <strong>Tanggal Awal</strong> dan <strong>Tanggal Akhir</strong> di atas, kemudian klik tombol <strong>Lihat Absen</strong> untuk menampilkan data absensi karyawan.
            </p>
            <div class="flex gap-2 mt-6">
                <button class="btn btn-sm btn-outline btn-primary" wire:click="setHariIni" spinner="setHariIni">
                    <x-icon name="o-calendar-days" class="w-4 h-4" />
                    Hari Ini
                </button>
                <button class="btn btn-sm btn-outline btn-secondary" wire:click="setKemarin" spinner="setKemarin">
                    <x-icon name="o-arrow-uturn-left" class="w-4 h-4" />
                    Kemarin
                </button>
            </div>
        </div>
    @else
        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
            {{-- Alpa --}}
            <div class="rounded-xl px-4 py-3 bg-red-50 border border-red-200">
                <div class="text-xs font-semibold text-red-600 uppercase tracking-wide">Alpa</div>
                <div class="text-2xl font-bold text-red-700 mt-1">{{ $rekap['alpa'] }}</div>
            </div>
            {{-- Sakit --}}
            <div class="rounded-xl px-4 py-3 bg-amber-50 border border-amber-200">
                <div class="text-xs font-semibold text-amber-600 uppercase tracking-wide">Sakit</div>
                <div class="text-2xl font-bold text-amber-700 mt-1">{{ $rekap['sakit'] }}</div>
            </div>
            {{-- Cuti --}}
            <div class="rounded-xl px-4 py-3 bg-violet-50 border border-violet-200">
                <div class="text-xs font-semibold text-violet-600 uppercase tracking-wide">Cuti</div>
                <div class="text-2xl font-bold text-violet-700 mt-1">{{ $rekap['cuti'] }}</div>
            </div>
            {{-- Izin --}}
            <div class="rounded-xl px-4 py-3 bg-slate-50 border border-slate-200">
                <div class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Izin</div>
                <div class="text-2xl font-bold text-slate-700 mt-1">{{ $rekap['izin'] }}</div>
            </div>
        </div>

        {{-- Table --}}
        <x-card shadow>
            <x-table :headers="$headers" :rows="$absens" with-pagination
                show-empty-text empty-text="Tidak ada data ditemukan">
                @scope('cell_no', $row)
                    <span class="text-sm text-base-content/50">{{ $row->row_no }}</span>
                @endscope

                @scope('cell_karyawan', $row)
                    <div class="flex items-center gap-3">
                        <div class="avatar">
                            <div class="mask mask-squircle w-10 h-10">
                                <img src="{{ $row->karyawan->foto_karyawan ? Storage::url($row->karyawan->foto_karyawan) : 'https://i.pravatar.cc/150?u=' . $row->karyawan->nik }}" alt="{{ $row->karyawan->nama_karyawan }}" />
                            </div>
                        </div>
                        <div>
                            <div class="font-bold text-sm">{{ $row->karyawan->nama_karyawan }}</div>
                            <div class="text-xs text-base-content/50">{{ $row->karyawan->jabatan?->nama_jabatan ?? '-' }}</div>
                        </div>
                    </div>
                @endscope

                @scope('cell_tanggal', $row)
                    <span class="text-sm">{{ $row->tanggal_absen->format('d M Y') }}</span>
                @endscope

                @scope('cell_keterangan', $row)
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium border {{ $this->getColor($row->keterangan) }}">
                        {{ $row->keterangan }}
                    </span>
                @endscope

                @scope('cell_scan_in', $row)
                    <span class="text-sm font-mono {{ $row->scan_in ? 'text-base-content' : 'text-base-content/40' }}">
                        {{ $row->scan_in ?? '-' }}
                    </span>
                @endscope

                @scope('cell_scan_out', $row)
                    <span class="text-sm font-mono {{ $row->scan_out ? 'text-base-content' : 'text-base-content/40' }}">
                        {{ $row->scan_out ?? '-' }}
                    </span>
                @endscope

                @scope('cell_terlambat', $row)
                    @php $menit = \App\Services\LatenessCalculator::getMinutesLate($row->scan_in, $row->tanggal_absen->format('Y-m-d')); @endphp
                    @if ($menit)
                        <span class="badge badge-sm badge-error badge-outline">{{ $menit }} min</span>
                    @else
                        <span class="text-base-content/30">-</span>
                    @endif
                @endscope
            </x-table>
        </x-card>
    @endif
</div>
