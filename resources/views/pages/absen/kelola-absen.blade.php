<?php

use App\Models\Absen;
use App\Models\Karyawan;
use App\Imports\AbsenImport;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Maatwebsite\Excel\Facades\Excel;

new class extends Component {
    use Toast, WithPagination, WithFileUploads;

    public string $search = '';
    public string $filterTanggal = '';
    public string $filterKeterangan = '';

    public bool $collectiveModal = false;
    public array $selectedRows = [];
    public string $collectiveKeterangan = 'Hadir';
    public string $collectiveCatatan = '';

    public bool $detailModal = false;
    public ?int $detailAbsenId = null;

    public bool $importModal = false;
    public $importFile;
    public int $importSuccess = 0;
    public int $importDuplicate = 0;
    public array $importErrors = [];
    public bool $importDone = false;

    public array $keteranganOptions = [];

    public bool $showNoDataNotice = false;

    public function boot(): void
    {
        if (empty($this->filterTanggal)) {
            $this->filterTanggal = now()->subDay()->format('Y-m-d');
        }

        $this->keteranganOptions = collect(['Hadir', 'Dinas Luar', 'Cuti', 'Sakit', 'Izin', 'Tidak Absen', 'Alpa', 'Off', 'Libur', 'Lainnya'])
            ->map(fn($k) => ['id' => $k, 'name' => $k])
            ->toArray();

        $this->checkDataAvailability();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTanggal(): void
    {
        $this->resetPage();
    }

    public function updatedFilterTanggal(): void
    {
        $this->checkDataAvailability();
    }

    public function updatingFilterKeterangan(): void
    {
        $this->resetPage();
    }

    public function checkDataAvailability(): void
    {
        $hasData = Absen::where('tanggal_absen', $this->filterTanggal)->where(fn($q) => $q->whereNotNull('scan_in')->orWhereNotNull('scan_out'))->exists();

        $this->showNoDataNotice = !$hasData;
    }

    public function dismissNotice(): void
    {
        $this->showNoDataNotice = false;
    }

    public function openImportFromNotice(): void
    {
        $this->showNoDataNotice = false;
        $this->importModal = true;
    }

    protected function getFilteredKaryawanQuery()
    {
        $date = $this->filterTanggal;

        return Karyawan::with(['jabatan', 'user'])
            ->where('karyawans.is_active', true)
            ->where('karyawans.tanggal_masuk', '<=', $date)
            ->whereDoesntHave('nonaktifs', function ($q) use ($date) {
                $q->where('tanggal_nonaktif', '<=', $date)
                    ->where(function ($sub) use ($date) {
                        $sub->whereNull('tanggal_aktif')
                            ->orWhere('tanggal_aktif', '>', $date);
                    });
            })
            ->leftJoin('absens', function ($j) use ($date) {
                $j->on('absens.karyawan_id', '=', 'karyawans.id')->where('absens.tanggal_absen', $date);
            })
            ->select('karyawans.*', 'absens.id as absen_id', 'absens.tanggal_absen', 'absens.keterangan', 'absens.scan_in', 'absens.scan_out', 'absens.foto_in', 'absens.foto_out')
            ->when($this->filterKeterangan, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('absens.keterangan', $this->filterKeterangan)->orWhereNull('absens.keterangan');
                });
            })
            ->when($this->search, function ($q) {
                $term = trim($this->search);
                $q->where(function ($sub) use ($term) {
                    $sub->where('karyawans.nama_karyawan', 'like', "%{$term}%")->orWhere('karyawans.nik', 'like', "%{$term}%");
                });
            })
            ->orderBy('karyawans.nama_karyawan');
    }

    #[Computed]
    public function absens()
    {
        return $this->getFilteredKaryawanQuery()->paginate(10);
    }

    public function headers(): array
    {
        return [['key' => 'no', 'label' => '#', 'class' => 'w-1'], ['key' => 'karyawan', 'label' => 'KARYAWAN', 'sortable' => false], ['key' => 'tanggal', 'label' => 'TANGGAL', 'sortable' => false], ['key' => 'keterangan', 'label' => 'KETERANGAN', 'class' => 'w-48'], ['key' => 'scan_in', 'label' => 'CHECK IN', 'sortable' => false], ['key' => 'scan_out', 'label' => 'CHECK OUT', 'sortable' => false], ['key' => 'actions', 'label' => 'AKSI', 'class' => 'w-16']];
    }

    public function with(): array
    {
        $absens = $this->absens;
        $start = $absens->firstItem();
        $absens->getCollection()->transform(fn($item, $i) => tap($item)->setAttribute('row_no', $start + $i));

        return [
            'absens' => $absens,
            'headers' => $this->headers(),
        ];
    }

    public function setKeterangan(int $karyawanId, string $value): void
    {
        Absen::updateOrCreate(['karyawan_id' => $karyawanId, 'tanggal_absen' => $this->filterTanggal], ['keterangan' => $value]);
    }

    public function toggleSelect(int $id): void
    {
        if (in_array($id, $this->selectedRows)) {
            $this->selectedRows = array_values(array_diff($this->selectedRows, [$id]));
        } else {
            $this->selectedRows[] = $id;
        }
    }

    public function selectAll(): void
    {
        $allIds = $this->getFilteredKaryawanQuery()->pluck('karyawans.id')->toArray();
        if (count($this->selectedRows) === count($allIds)) {
            $this->selectedRows = [];
        } else {
            $this->selectedRows = $allIds;
        }
    }

    public function isSelected(int $id): bool
    {
        return in_array($id, $this->selectedRows);
    }

    public function allSelected(): bool
    {
        $allIds = $this->getFilteredKaryawanQuery()->pluck('karyawans.id')->toArray();
        return count($allIds) > 0 && count($this->selectedRows) === count($allIds);
    }

    public function openCollective(): void
    {
        $this->collectiveKeterangan = 'Hadir';
        $this->collectiveCatatan = '';
        $this->collectiveModal = true;
    }

    public function saveCollective(): void
    {
        $this->validate([
            'collectiveKeterangan' => 'required|string|in:Hadir,Dinas Luar,Cuti,Sakit,Izin,Tidak Absen,Alpa,Off,Libur,Lainnya',
        ]);

        foreach ($this->selectedRows as $karyawanId) {
            $this->setKeterangan($karyawanId, $this->collectiveKeterangan);
        }

        $count = count($this->selectedRows);
        $this->success("Keterangan {$count} absensi diperbarui.", position: 'toast-top toast-end');
        $this->selectedRows = [];
        $this->collectiveModal = false;
    }

    public function detailAbsen()
    {
        if (!$this->detailAbsenId) {
            return null;
        }

        $absen = Absen::with('karyawan.jabatan')->where('karyawan_id', $this->detailAbsenId)->where('tanggal_absen', $this->filterTanggal)->first();

        if (!$absen) {
            $karyawan = Karyawan::with('jabatan')->find($this->detailAbsenId);
            if ($karyawan) {
                return (object) [
                    'id' => null,
                    'karyawan_id' => $karyawan->id,
                    'karyawan' => $karyawan,
                    'tanggal_absen' => \Carbon\Carbon::parse($this->filterTanggal),
                    'keterangan' => null,
                    'mode' => null,
                    'scan_in' => null,
                    'scan_out' => null,
                    'foto_in' => null,
                    'foto_out' => null,
                    'lat_in' => null,
                    'long_in' => null,
                    'lat_out' => null,
                    'long_out' => null,
                    'created_at' => null,
                    'updated_at' => null,
                ];
            }

            return null;
        }

        return $absen;
    }

    public function openDetail(int $id): void
    {
        $this->detailAbsenId = $id;
        $this->detailModal = true;
    }

    public function import(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,xlsx,xls|max:5120',
        ]);

        $import = new AbsenImport();
        Excel::import($import, $this->importFile->getRealPath());

        $this->importSuccess = $import->successCount;
        $this->importDuplicate = $import->duplicateCount;
        $this->importErrors = $import->errors;
        $this->importDone = true;

        $this->resetPage();

        if ($this->importSuccess > 0) {
            $this->success("{$this->importSuccess} data absensi berhasil diimport.", position: 'toast-top toast-end');
        }
    }

    public function resetImport(): void
    {
        $this->reset(['importFile', 'importSuccess', 'importDuplicate', 'importErrors', 'importDone', 'importModal']);
        $this->dispatch('reset-import');
    }

    public function closeModal(): void
    {
        $this->collectiveModal = false;
        $this->detailModal = false;
        $this->detailAbsenId = null;
        $this->selectedRows = [];
    }

    public static function getKeteranganColor(string $keterangan): string
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
    <x-header title="Kelola Absensi" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Cari nama/NIK..." wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            @if (count($this->selectedRows) >= 2)
                <x-button label="Keterangan Kolektif ({{ count($this->selectedRows) }})"
                    icon="o-users" class="btn-primary" wire:click="openCollective" spinner />
            @endif
        </x-slot:actions>
    </x-header>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3 mb-4">
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Tanggal</legend>
            <input type="date" class="input input-bordered input-sm w-44"
                wire:model.live="filterTanggal" />
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Keterangan</legend>
            <select class="select select-bordered select-sm w-48"
                wire:model.live="filterKeterangan">
                <option value="">Semua Keterangan</option>
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
            <button class="btn btn-ghost btn-sm"
                onclick="window.location.href='{{ route('absen.template') }}'">
                <x-icon name="o-document-arrow-down" class="w-4 h-4" />
                Template
            </button>
            <button class="btn btn-outline btn-sm btn-warning"
                wire:click="$set('importModal', true)" spinner>
                <x-icon name="o-arrow-up-tray" class="w-4 h-4" />
                Import
            </button>
            <button class="btn btn-primary btn-sm"
                wire:click="$set('filterTanggal', '{{ now()->format('Y-m-d') }}')">
                <x-icon name="o-calendar-days" class="w-4 h-4" />
                Hari Ini
            </button>
            <button class="btn btn-secondary btn-sm"
                wire:click="$set('filterTanggal', '{{ now()->subDay()->format('Y-m-d') }}')">
                <x-icon name="o-arrow-uturn-left" class="w-4 h-4" />
                Kemarin
            </button>
        </div>
    </div>

    {{-- No Data Notice --}}
    @if ($showNoDataNotice)
        <div class="alert alert-warning shadow-sm mb-4">
            <x-icon name="o-exclamation-triangle" class="w-6 h-6" />
            <div>
                <p class="font-bold">Belum ada data absensi</p>
                <p class="text-sm">
                    Tanggal {{ \Carbon\Carbon::parse($this->filterTanggal)->format('d M Y') }}
                    belum ada transaksi absen dari mesin.
                </p>
            </div>
            <div class="flex gap-2">
                <button class="btn btn-indigo btn-sm" wire:click="openImportFromNotice">
                    Import Sekarang
                </button>
                <button class="btn btn-ghost btn-sm" wire:click="dismissNotice">
                    Abai
                </button>
            </div>
        </div>
    @endif

    {{-- Table --}}
    <x-card shadow class="min-h-[32rem]">
        <div wire:loading.class="opacity-40 pointer-events-none"
            wire:target="gotoPage,previousPage,nextPage,setKeterangan,filterTanggal,filterKeterangan,search"
            class="transition-opacity duration-200">
            <x-table :headers="$headers" :rows="$absens" with-pagination show-empty-text
                empty-text="Tidak ada data ditemukan">
                @scope('header_no', $header)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="checkbox checkbox-sm checkbox-primary"
                            wire:click="selectAll()" {{ $this->allSelected() ? 'checked' : '' }} />
                        <span class="text-xs">{{ $header['label'] }}</span>
                    </label>
                @endscope

                @scope('cell_no', $row)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="checkbox checkbox-sm checkbox-primary"
                            wire:change="toggleSelect({{ $row->id }})"
                            {{ in_array($row->id, $this->selectedRows) ? 'checked' : '' }} />
                        <span class="text-sm text-base-content/50">{{ $row->row_no }}</span>
                    </label>
                @endscope

                @scope('cell_karyawan', $row)
                    <div class="flex items-center gap-3">
                        <div class="avatar">
                            <div class="mask mask-squircle w-10 h-10">
                                <img src="{{ $row->foto_karyawan ? Storage::url($row->foto_karyawan) : 'https://i.pravatar.cc/150?u=' . $row->nik }}"
                                    alt="{{ $row->nama_karyawan }}" />
                            </div>
                        </div>
                        <div>
                            <div class="font-bold text-sm">{{ $row->nama_karyawan }}</div>
                            <div class="text-xs text-base-content/50">
                                {{ $row->jabatan?->nama_jabatan ?? '-' }}</div>
                        </div>
                    </div>
                @endscope

                @scope('cell_tanggal', $row)
                    <span
                        class="text-sm">{{ \Carbon\Carbon::parse($row->tanggal_absen ?? $this->filterTanggal)->format('d M Y') }}</span>
                @endscope

                @scope('cell_keterangan', $row)
                    <div x-data="{ val: '{{ $row->keterangan ?? '' }}' }" class="inline-block">
                        <select x-model="val" class="select select-sm w-40 border cursor-pointer"
                            :class="{
                                'bg-gray-100 text-gray-400 border-gray-200': val === '',
                                'bg-emerald-100 text-emerald-700 border-emerald-200': val === 'Hadir',
                                'bg-sky-100 text-sky-700 border-sky-200': val === 'Dinas Luar',
                                'bg-violet-100 text-violet-700 border-violet-200': val === 'Cuti',
                                'bg-amber-100 text-amber-700 border-amber-200': val === 'Sakit',
                                'bg-slate-100 text-slate-600 border-slate-200': val === 'Izin',
                                'bg-orange-100 text-orange-700 border-orange-200': val === 'Tidak Absen',
                                'bg-red-100 text-red-700 border-red-200': val === 'Alpa',
                                'bg-gray-100 text-gray-500 border-gray-200': val === 'Off',
                                'bg-cyan-100 text-cyan-700 border-cyan-200': val === 'Libur',
                                'bg-pink-100 text-pink-700 border-pink-200': val === 'Lainnya'
                            }"
                            wire:ignore.self
                            @change="$wire.setKeterangan({{ $row->id }}, $event.target.value)">
                            <option value="">Pilih Keterangan</option>
                            @foreach (['Hadir', 'Dinas Luar', 'Cuti', 'Sakit', 'Izin', 'Tidak Absen', 'Alpa', 'Off', 'Libur', 'Lainnya'] as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                @endscope

                @scope('cell_scan_in', $row)
                    <span
                        class="text-sm font-mono {{ $row->scan_in ? 'text-base-content' : 'text-base-content/40' }}">
                        {{ $row->scan_in ?? '-' }}
                    </span>
                @endscope

                @scope('cell_scan_out', $row)
                    <span
                        class="text-sm font-mono {{ $row->scan_out ? 'text-base-content' : 'text-base-content/40' }}">
                        {{ $row->scan_out ?? '-' }}
                    </span>
                @endscope

                @scope('actions', $row)
                    <x-button icon="o-eye" wire:click="openDetail({{ $row->id }})"
                        class="btn-ghost btn-sm text-primary" spinner />
                @endscope
            </x-table>
        </div>
    </x-card>

    {{-- Modal Detail --}}
    <div wire:key="detail-modal-wrap">
        <x-modal wire:model="detailModal" title="Detail Absensi" box-class="!max-w-md">
            @php $d = $detailModal ? $this->detailAbsen() : null; @endphp
            @if ($d)
                <div class="space-y-4">
                    {{-- Employee Info --}}
                    <div class="flex items-center gap-3">
                        <div class="avatar">
                            <div class="mask mask-squircle w-11 h-11">
                                <img src="{{ $d->karyawan->foto_karyawan ? Storage::url($d->karyawan->foto_karyawan) : 'https://i.pravatar.cc/150?u=' . $d->karyawan->nik }}" alt="{{ $d->karyawan->nama_karyawan }}" />
                            </div>
                        </div>
                        <div>
                            <div class="font-bold text-sm">{{ $d->karyawan->nama_karyawan }}</div>
                            <div class="text-xs text-base-content/50">{{ $d->karyawan->nik }} · {{ $d->karyawan->jabatan?->nama_jabatan ?? '-' }}</div>
                        </div>
                    </div>

                    @if (is_null($d->id))
                        <div class="alert alert-warning text-xs py-2 px-3 shadow-none">
                            <x-icon name="o-exclamation-triangle" class="w-4 h-4" />
                            <span>Karyawan belum melakukan absensi pada tanggal ini.</span>
                        </div>
                    @endif

                    {{-- Key-Value Grid --}}
                    <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-xs border-t border-b border-base-200 py-3">
                        <div>
                            <span class="text-base-content/40 block mb-0.5">Tanggal</span>
                            <span class="font-medium text-base-content">{{ \Carbon\Carbon::parse($d->tanggal_absen ?? $this->filterTanggal)->translatedFormat('d F Y') }}</span>
                        </div>
                        <div>
                            <span class="text-base-content/40 block mb-0.5">Keterangan</span>
                            @if ($d->keterangan)
                                <span class="inline-block px-2 py-0.5 rounded-full font-medium border {{ $this->getKeteranganColor($d->keterangan) }}">
                                    {{ $d->keterangan }}
                                </span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded-full font-medium bg-base-200 text-base-content/55">
                                    Belum Absen
                                </span>
                            @endif
                        </div>
                        <div>
                            <span class="text-base-content/40 block mb-0.5">Check In</span>
                            <div class="flex items-center gap-1 font-mono font-semibold {{ $d->scan_in ? 'text-base-content' : 'text-base-content/30' }}">
                                {{ $d->scan_in ?? '-' }}
                                @if ($d->lat_in && $d->long_in)
                                    <a href="https://www.google.com/maps?q={{ $d->lat_in }},{{ $d->long_in }}" target="_blank" class="text-success hover:underline flex items-center gap-0.5 ml-1">
                                        <x-icon name="o-map-pin" class="w-3 h-3" /> GPS
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="text-base-content/40 block mb-0.5">Check Out</span>
                            <div class="flex items-center gap-1 font-mono font-semibold {{ $d->scan_out ? 'text-base-content' : 'text-base-content/30' }}">
                                {{ $d->scan_out ?? '-' }}
                                @if ($d->lat_out && $d->long_out)
                                    <a href="https://www.google.com/maps?q={{ $d->lat_out }},{{ $d->long_out }}" target="_blank" class="text-info hover:underline flex items-center gap-0.5 ml-1">
                                        <x-icon name="o-map-pin" class="w-3 h-3" /> GPS
                                    </a>
                                @endif
                            </div>
                        </div>
                        @if ($d->mode)
                            <div>
                                <span class="text-base-content/40 block mb-0.5">Metode</span>
                                <span class="font-medium text-base-content/70">
                                    {{ $d->mode === 'face' ? 'Face Recognition' : 'Upload / Mesin' }}
                                </span>
                            </div>
                        @endif
                        @if ($d->scan_in && $d->scan_out)
                            @php
                                $in = \Carbon\Carbon::parse($d->scan_in);
                                $out = \Carbon\Carbon::parse($d->scan_out);
                                $diff = $in->diff($out);
                                $durasi = ($diff->h > 0 ? $diff->h . ' jam ' : '') . $diff->i . ' menit';
                            @endphp
                            <div>
                                <span class="text-base-content/40 block mb-0.5">Durasi Kerja</span>
                                <span class="font-medium text-base-content/80">{{ $durasi }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Photos --}}
                    @if ($d->foto_in || $d->foto_out)
                        <div class="space-y-1.5">
                            <span class="text-base-content/40 block">Foto Absen</span>
                            <div class="flex gap-3">
                                @if ($d->foto_in)
                                    <div class="w-1/2 space-y-1">
                                        <span class="text-[10px] text-base-content/50 block">Check In</span>
                                        <img src="{{ Storage::url($d->foto_in) }}" class="w-full h-28 object-cover rounded-lg border border-base-200" />
                                    </div>
                                @endif
                                @if ($d->foto_out)
                                    <div class="w-1/2 space-y-1">
                                        <span class="text-[10px] text-base-content/50 block">Check Out</span>
                                        <img src="{{ Storage::url($d->foto_out) }}" class="w-full h-28 object-cover rounded-lg border border-base-200" />
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Timestamps --}}
                    @if ($d->created_at)
                        <div class="text-[10px] text-base-content/30 flex justify-between pt-1">
                            <span>Dibuat: {{ $d->created_at->format('d/m/Y H:i') }}</span>
                            <span>Diperbarui: {{ $d->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            @endif
            <x-slot:actions>
                <x-button label="Tutup" wire:click="closeModal" type="button" />
            </x-slot:actions>
        </x-modal>
    </div>

    {{-- Modal Import --}}
    <div wire:key="import-modal-wrap" x-data="{
        fileName: '',
        selectedFile: null,
        uploading: false,
        progress: 0,
        doImport() {
            if (!this.selectedFile) {
                alert('Pilih file terlebih dahulu.');
                return;
            }
            this.uploading = true;
            this.progress = 0;
            $wire.upload(
                'importFile',
                this.selectedFile,
                () => {
                    this.uploading = false;
                    $wire.import();
                },
                () => { this.uploading = false; },
                (event) => { this.progress = event.detail.progress; }
            );
        }
    }"
        @reset-import.window="fileName = ''; selectedFile = null; uploading = false; progress = 0;"
        @do-import.window="doImport()"
        @import-file-selected.window="selectedFile = $event.detail.file; fileName = $event.detail.name;">
        <x-modal wire:model="importModal" title="Import Absensi" box-class="!max-w-md">
            <div class="space-y-4">
                @unless ($importDone)
                    <div>
                        <label class="label">
                            <span class="label-text">Pilih file CSV/Excel</span>
                        </label>

                        {{-- Custom file picker: dispatches file to wrapper x-data via window event --}}
                        <label
                            class="flex items-center gap-3 w-full cursor-pointer border-2 border-dashed border-base-300 rounded-xl p-4 hover:border-primary transition-colors"
                            x-data="{ localName: '' }"
                            :class="localName ? 'border-primary bg-primary/5' : ''"
                            @reset-import.window="localName = ''">
                            <x-icon name="o-document-arrow-up"
                                class="w-8 h-8 text-primary shrink-0" />
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate"
                                    x-text="localName || 'Klik untuk memilih file'"></p>
                                <p class="text-xs text-base-content/40" x-show="!localName">CSV, XLSX,
                                    atau XLS — maks. 5 MB</p>
                                <p class="text-xs text-success" x-show="localName" x-cloak>File siap
                                    diimport</p>
                            </div>
                            <input type="file" accept=".csv,.xlsx,.xls" class="hidden"
                                @change="const f = $event.target.files[0]; if(f){ localName = f.name; window.dispatchEvent(new CustomEvent('import-file-selected', { detail: { file: f, name: f.name } })); } else { localName = ''; }" />
                        </label>

                        {{-- Upload progress bar --}}
                        <div class="mt-3" x-show="uploading" x-cloak>
                            <div class="flex justify-between text-xs text-base-content/60 mb-1">
                                <span>Mengunggah file…</span>
                                <span x-text="progress + '%'"></span>
                            </div>
                            <progress class="progress progress-primary w-full" :value="progress"
                                max="100"></progress>
                        </div>

                        @error('importFile')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror

                        <div class="text-xs text-base-content/50 mt-3 space-y-1">
                            <p>Format kolom: <code>No. ID</code>, <code>Tanggal</code>, <code>Scan
                                    Masuk</code>, <code>Scan Pulang</code></p>
                            <p>No. ID = PIN mesin absen</p>
                            <p>Tanggal = dd/mm/YYYY</p>
                        </div>
                    </div>
                @else
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-success">
                            <x-icon name="o-check-circle" class="w-6 h-6" />
                            <span class="font-medium">{{ $importSuccess }} data berhasil
                                diimport</span>
                        </div>
                        @if ($importDuplicate)
                            <div class="flex items-center gap-2 text-warning">
                                <x-icon name="o-exclamation-triangle" class="w-6 h-6" />
                                <span class="font-medium">{{ $importDuplicate }} data duplikat
                                    dilewati</span>
                            </div>
                        @endif
                        @if (count($importErrors))
                            <div>
                                <span class="text-sm text-base-content/50 font-medium">Gagal
                                    ({{ count($importErrors) }}):</span>
                                <ul class="list-disc list-inside text-sm text-red-500 mt-1">
                                    @foreach ($importErrors as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endunless
            </div>
            <x-slot:actions>
                @unless ($importDone)
                    <x-button label="Batal" wire:click="resetImport" type="button" />
                    {{-- Dispatch window event so Alpine handler inside x-data can run, bypassing teleport scope --}}
                    <button class="btn btn-primary" id="btn-import-submit"
                        wire:loading.attr="disabled" wire:target="import"
                        onclick="window.dispatchEvent(new CustomEvent('do-import'))">
                        <span wire:loading.remove wire:target="import">Import</span>
                        <span wire:loading wire:target="import"
                            class="loading loading-spinner loading-sm"></span>
                        <span wire:loading wire:target="import">Memproses…</span>
                    </button>
                @else
                    <x-button label="Tutup" wire:click="resetImport" class="btn-primary" />
                @endunless
            </x-slot:actions>
        </x-modal>
    </div>

    {{-- Modal Kolektif --}}
    <div wire:key="collective-modal-wrap">
        <x-modal wire:model="collectiveModal" title="Keterangan Kolektif"
            subtitle="Ubah keterangan untuk {{ count($this->selectedRows) }} absensi terpilih"
            box-class="!max-w-md">
            <div class="space-y-4">
                <x-select wire:model="collectiveKeterangan" label="Pilih Keterangan"
                    :options="$keteranganOptions" option-value="id" option-label="name" />
                <x-input wire:model="collectiveCatatan" label="Catatan Tambahan (Opsional)"
                    placeholder="Contoh: Penugasan proyek di luar kota..." />
                <div class="alert alert-warning text-sm">
                    <x-icon name="o-exclamation-triangle" class="w-5 h-5" />
                    <span>Tindakan ini akan menimpa status absensi saat ini untuk semua karyawan
                        yang dipilih.</span>
                </div>
            </div>
            <x-slot:actions>
                <x-button label="Batal" wire:click="closeModal" type="button" />
                <x-button label="Simpan Perubahan" class="btn-primary"
                    wire:click="saveCollective" spinner />
            </x-slot:actions>
        </x-modal>
    </div>
</div>
