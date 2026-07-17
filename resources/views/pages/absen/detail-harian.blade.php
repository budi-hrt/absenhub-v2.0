<?php

use App\Models\Absen;
use App\Models\Karyawan;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public ?int $filterKaryawan = null;
    public string $bulan = '';
    public string $tahun = '';

    public array $listBulan = [];
    public array $listTahun = [];

    public function boot(): void
    {
        $this->listBulan = [
            ['id' => '01', 'name' => 'Januari'],
            ['id' => '02', 'name' => 'Februari'],
            ['id' => '03', 'name' => 'Maret'],
            ['id' => '04', 'name' => 'April'],
            ['id' => '05', 'name' => 'Mei'],
            ['id' => '06', 'name' => 'Juni'],
            ['id' => '07', 'name' => 'Juli'],
            ['id' => '08', 'name' => 'Agustus'],
            ['id' => '09', 'name' => 'September'],
            ['id' => '10', 'name' => 'Oktober'],
            ['id' => '11', 'name' => 'November'],
            ['id' => '12', 'name' => 'Desember'],
        ];

        $now = now();
        $this->listTahun = collect(range($now->year - 2, $now->year + 1))
            ->map(fn($y) => ['id' => (string) $y, 'name' => (string) $y])
            ->toArray();

        if (empty($this->bulan)) {
            $this->bulan = $now->format('m');
        }
        if (empty($this->tahun)) {
            $this->tahun = (string) $now->year;
        }
    }

    #[Computed]
    public function karyawans()
    {
        return Karyawan::where('is_active', true)
            ->orderBy('nama_karyawan')
            ->get()
            ->map(fn($k) => ['id' => $k->id, 'name' => $k->nama_karyawan . ' — ' . ($k->nik ?? '-')])
            ->toArray();
    }

    #[Computed]
    public function absens()
    {
        if (!$this->filterKaryawan || !$this->bulan || !$this->tahun) {
            return collect();
        }

        return Absen::with('karyawan.jabatan')
            ->where('karyawan_id', $this->filterKaryawan)
            ->whereYear('tanggal_absen', (int) $this->tahun)
            ->whereMonth('tanggal_absen', (int) $this->bulan)
            ->orderBy('tanggal_absen')
            ->get();
    }

    #[Computed]
    public function rekap()
    {
        $absens = $this->absens;
        if ($absens->isEmpty()) {
            return [];
        }

        return [
            'Hadir' => $absens->where('keterangan', 'Hadir')->count(),
            'Sakit' => $absens->where('keterangan', 'Sakit')->count(),
            'Izin' => $absens->where('keterangan', 'Izin')->count(),
            'Cuti' => $absens->where('keterangan', 'Cuti')->count(),
            'Alpa' => $absens->where('keterangan', 'Alpa')->count(),
            'Off' => $absens->where('keterangan', 'Off')->count(),
            'Dinas Luar' => $absens->where('keterangan', 'Dinas Luar')->count(),
            'Tidak Absen' => $absens->where('keterangan', 'Tidak Absen')->count(),
            'Libur' => $absens->where('keterangan', 'Libur')->count(),
            'Lainnya' => $absens->where('keterangan', 'Lainnya')->count(),
        ];
    }

    #[Computed]
    public function selectedKaryawan()
    {
        if (!$this->filterKaryawan) return null;
        return Karyawan::with('jabatan')->find($this->filterKaryawan);
    }

    public function headers(): array
    {
        return [
            ['key' => 'tanggal', 'label' => 'TANGGAL', 'sortable' => false],
            ['key' => 'hari', 'label' => 'HARI', 'sortable' => false],
            ['key' => 'scan_in', 'label' => 'CHECK IN', 'sortable' => false],
            ['key' => 'scan_out', 'label' => 'CHECK OUT', 'sortable' => false],
            ['key' => 'keterangan', 'label' => 'KETERANGAN', 'sortable' => false],
        ];
    }

    public function with(): array
    {
        return [
            'absens' => $this->absens,
            'headers' => $this->headers(),
            'rekap' => $this->rekap,
            'selectedKaryawan' => $this->selectedKaryawan,
            'karyawans' => $this->karyawans,
            'namaBulan' => collect($this->listBulan)->firstWhere('id', $this->bulan)['name'] ?? '',
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
    <x-header title="Detail Harian" separator progress-indicator />

    <div class="flex flex-wrap gap-3 mb-4 items-end">
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Karyawan</legend>
            <select class="select select-bordered select-sm w-64" wire:model.live="filterKaryawan">
                <option value="">— Pilih Karyawan —</option>
                @foreach ($karyawans as $k)
                    <option value="{{ $k['id'] }}">{{ $k['name'] }}</option>
                @endforeach
            </select>
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
        @if ($filterKaryawan)
            <a href="{{ route('absen.detail-harian.export', ['karyawan_id' => $filterKaryawan, 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                class="btn btn-outline btn-sm btn-primary">
                <x-icon name="o-document-arrow-down" class="w-4 h-4" />
                Excel
            </a>
        @endif
    </div>

    @if ($selectedKaryawan)
        <div class="flex items-center gap-3 mb-4 p-4 bg-base-200 rounded-xl">
            <div class="avatar">
                <div class="mask mask-squircle w-14 h-14">
                    <img src="{{ $selectedKaryawan->foto_karyawan ? Storage::url($selectedKaryawan->foto_karyawan) : 'https://i.pravatar.cc/150?u=' . $selectedKaryawan->nik }}" />
                </div>
            </div>
            <div>
                <div class="font-bold text-lg">{{ $selectedKaryawan->nama_karyawan }}</div>
                <div class="text-sm text-base-content/50">{{ $selectedKaryawan->nik }} · {{ $selectedKaryawan->jabatan?->nama_jabatan ?? '-' }}</div>
            </div>
            <div class="ml-auto text-sm text-base-content/50 font-medium">
                {{ $namaBulan }} {{ $tahun }}
            </div>
        </div>

        @if (count($rekap))
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach ($rekap as $label => $count)
                    @if ($count > 0)
                        <div class="px-3 py-1.5 rounded-lg text-xs font-medium border {{ $this->getColor($label) }}">
                            {{ $label }}: {{ $count }}
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <x-card shadow>
            <x-table :headers="$headers" :rows="$absens"
                show-empty-text empty-text="Tidak ada data absensi untuk periode ini">
                @scope('cell_tanggal', $row)
                    <span class="text-sm">{{ $row->tanggal_absen->format('d M Y') }}</span>
                @endscope

                @scope('cell_hari', $row)
                    <span class="text-sm text-base-content/70">{{ $row->tanggal_absen->format('l') }}</span>
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

                @scope('cell_keterangan', $row)
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium border {{ $this->getColor($row->keterangan) }}">
                        {{ $row->keterangan }}
                    </span>
                @endscope
            </x-table>
        </x-card>
    @else
        <div class="flex flex-col items-center justify-center py-16 text-base-content/40">
            <x-icon name="o-user" class="w-16 h-16 mb-4" />
            <p class="text-lg font-medium">Pilih Karyawan</p>
            <p class="text-sm">Pilih karyawan, bulan, dan tahun untuk melihat detail harian.</p>
        </div>
    @endif
</div>
