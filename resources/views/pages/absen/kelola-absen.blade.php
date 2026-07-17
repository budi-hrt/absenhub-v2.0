<?php

use App\Models\Absen;
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

    public function boot(): void
    {
        if (empty($this->filterTanggal)) {
            $this->filterTanggal = now()->format('Y-m-d');
        }

        $this->keteranganOptions = collect(['Hadir', 'Dinas Luar', 'Cuti', 'Sakit', 'Izin', 'Tidak Absen', 'Alpa', 'Off', 'Libur', 'Lainnya'])
            ->map(fn($k) => ['id' => $k, 'name' => $k])
            ->toArray();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTanggal(): void
    {
        $this->resetPage();
    }

    public function updatingFilterKeterangan(): void
    {
        $this->resetPage();
    }

    protected function getFilteredQuery()
    {
        return Absen::with('karyawan.jabatan')
            ->when($this->filterTanggal, fn($q) => $q->where('tanggal_absen', $this->filterTanggal))
            ->when($this->filterKeterangan, fn($q) => $q->where('keterangan', $this->filterKeterangan))
            ->when($this->search, function ($q) {
                $term = trim($this->search);
                $q->whereHas('karyawan', function ($kq) use ($term) {
                    $kq->where(function ($sub) use ($term) {
                        $sub->where('nama_karyawan', 'like', "%{$term}%")
                            ->orWhere('nik', 'like', "%{$term}%");
                    });
                });
            })
            ->orderBy('karyawan_id')
            ->orderBy('created_at');
    }

    #[Computed]
    public function absens()
    {
        return $this->getFilteredQuery()->paginate(10);
    }

    public function headers(): array
    {
        return [
            ['key' => 'no', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'karyawan', 'label' => 'KARYAWAN', 'sortable' => false],
            ['key' => 'tanggal', 'label' => 'TANGGAL', 'sortable' => false],
            ['key' => 'keterangan', 'label' => 'KETERANGAN', 'class' => 'w-48'],
            ['key' => 'scan_in', 'label' => 'CHECK IN', 'sortable' => false],
            ['key' => 'scan_out', 'label' => 'CHECK OUT', 'sortable' => false],
            ['key' => 'actions', 'label' => 'AKSI', 'class' => 'w-16'],
        ];
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

    public function updateKeterangan(int $id, string $value): void
    {
        Absen::where('id', $id)->update(['keterangan' => $value]);
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
        $allIds = $this->getFilteredQuery()->pluck('id')->toArray();
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
        $allIds = $this->getFilteredQuery()->pluck('id')->toArray();
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

        Absen::whereIn('id', $this->selectedRows)->update([
            'keterangan' => $this->collectiveKeterangan,
        ]);

        $count = count($this->selectedRows);
        $this->success("Keterangan {$count} absensi diperbarui secara kolektif.", position: 'toast-top toast-end');
        $this->selectedRows = [];
        $this->collectiveModal = false;
    }

    #[Computed]
    public function detailAbsen()
    {
        if (!$this->detailAbsenId) {
            return null;
        }

        return Absen::with('karyawan.jabatan')->find($this->detailAbsenId);
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

        $import = new AbsenImport;
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
                <x-button label="Keterangan Kolektif ({{ count($this->selectedRows) }})" icon="o-users"
                    class="btn-primary" wire:click="openCollective" spinner />
            @endif
        </x-slot:actions>
    </x-header>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3 mb-4">
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Tanggal</legend>
            <input type="date" class="input input-bordered input-sm w-44" wire:model.live="filterTanggal" />
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Keterangan</legend>
            <select class="select select-bordered select-sm w-48" wire:model.live="filterKeterangan">
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
            <button class="btn btn-ghost btn-sm" onclick="window.location.href='{{ route('absen.template') }}'">
                <x-icon name="o-document-arrow-down" class="w-4 h-4" />
                Template
            </button>
            <button class="btn btn-outline btn-sm btn-warning" wire:click="$set('importModal', true)" spinner>
                <x-icon name="o-arrow-up-tray" class="w-4 h-4" />
                Import
            </button>
            <button class="btn btn-primary btn-sm" wire:click="$set('filterTanggal', '{{ now()->format('Y-m-d') }}')">
                <x-icon name="o-calendar-days" class="w-4 h-4" />
                Hari Ini
            </button>
            <button class="btn btn-secondary btn-sm" wire:click="$set('filterTanggal', '{{ now()->subDay()->format('Y-m-d') }}')">
                <x-icon name="o-arrow-uturn-left" class="w-4 h-4" />
                Kemarin
            </button>
        </div>
    </div>

    {{-- Table --}}
    <x-card shadow>
        <x-table :headers="$headers" :rows="$absens" with-pagination
            show-empty-text empty-text="Tidak ada data ditemukan">
            @scope('header_no', $header)
                <label class="flex items-center gap-2">
                    <input type="checkbox" class="checkbox checkbox-sm checkbox-primary"
                        wire:click="selectAll()"
                        {{ $this->allSelected() ? 'checked' : '' }} />
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
                <div x-data="{ val: '{{ $row->keterangan }}' }" class="inline-block">
                    <select x-model="val"
                        class="select select-sm w-40 border cursor-pointer"
                        :class="{
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
                        @change="$wire.updateKeterangan({{ $row->id }}, $event.target.value)">
                        @foreach (['Hadir', 'Dinas Luar', 'Cuti', 'Sakit', 'Izin', 'Tidak Absen', 'Alpa', 'Off', 'Libur', 'Lainnya'] as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
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

            @scope('actions', $row)
                <x-button icon="o-eye" wire:click="openDetail({{ $row->id }})"
                    class="btn-ghost btn-sm text-primary" spinner />
            @endscope
        </x-table>
    </x-card>

    {{-- Modal Detail --}}
    <x-modal wire:model="detailModal" title="Detail Absensi" box-class="!max-w-2xl">
        @php $d = $this->detailAbsen; @endphp
        @if ($d)
            <div class="space-y-6">
                {{-- Employee Info --}}
                <div class="flex items-center gap-4">
                    <div class="avatar">
                        <div class="mask mask-squircle w-16 h-16">
                            <img src="{{ $d->karyawan->foto_karyawan ? Storage::url($d->karyawan->foto_karyawan) : 'https://i.pravatar.cc/150?u=' . $d->karyawan->nik }}" />
                        </div>
                    </div>
                    <div>
                        <div class="font-bold text-lg">{{ $d->karyawan->nama_karyawan }}</div>
                        <div class="text-sm text-base-content/50">{{ $d->karyawan->nik }} · {{ $d->karyawan->jabatan?->nama_jabatan ?? '-' }}</div>
                    </div>
                </div>

                {{-- Info Grid --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-xs text-base-content/50 uppercase tracking-wide">Tanggal</span>
                        <div class="font-medium">{{ $d->tanggal_absen->format('d M Y') }}</div>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/50 uppercase tracking-wide">Keterangan</span>
                        <div>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium border {{ $this->getKeteranganColor($d->keterangan) }}">
                                {{ $d->keterangan }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/50 uppercase tracking-wide">Check In</span>
                        <div class="font-mono font-medium">{{ $d->scan_in ?? '-' }}</div>
                    </div>
                    <div>
                        <span class="text-xs text-base-content/50 uppercase tracking-wide">Check Out</span>
                        <div class="font-mono font-medium">{{ $d->scan_out ?? '-' }}</div>
                    </div>
                </div>

                {{-- Photos --}}
                @if ($d->foto_in || $d->foto_out)
                    <div>
                        <span class="text-xs text-base-content/50 uppercase tracking-wide mb-2 block">Foto</span>
                        <div class="flex gap-3">
                            @if ($d->foto_in)
                                <div>
                                    <p class="text-xs text-base-content/50 mb-1">Check In</p>
                                    <img src="{{ Storage::url($d->foto_in) }}" class="w-40 h-40 object-cover rounded-xl border" />
                                </div>
                            @endif
                            @if ($d->foto_out)
                                <div>
                                    <p class="text-xs text-base-content/50 mb-1">Check Out</p>
                                    <img src="{{ Storage::url($d->foto_out) }}" class="w-40 h-40 object-cover rounded-xl border" />
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- GPS --}}
                @if ($d->lat_in && $d->long_in)
                    <div>
                        <span class="text-xs text-base-content/50 uppercase tracking-wide mb-2 block">Lokasi Check In</span>
                        <a href="https://www.google.com/maps?q={{ $d->lat_in }},{{ $d->long_in }}" target="_blank"
                            class="link link-primary text-sm flex items-center gap-1">
                            <x-icon name="o-map-pin" class="w-4 h-4" />
                            {{ $d->lat_in }}, {{ $d->long_in }}
                        </a>
                    </div>
                @endif

                {{-- Timestamps --}}
                <div class="text-xs text-base-content/40 border-t pt-3 flex justify-between">
                    <span>Dibuat: {{ $d->created_at->format('d M Y H:i') }}</span>
                    <span>Diperbarui: {{ $d->updated_at->format('d M Y H:i') }}</span>
                </div>
            </div>
        @endif
        <x-slot:actions>
            <x-button label="Tutup" wire:click="closeModal" type="button" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal Import --}}
    <x-modal wire:model="importModal" title="Import Absensi" box-class="!max-w-md">
        <div class="space-y-4">
            @unless ($importDone)
                <div>
                    <label class="label">
                        <span class="label-text">Pilih file CSV/Excel</span>
                    </label>
                    <input type="file" wire:model="importFile"
                        accept=".csv,.xlsx,.xls"
                        class="file-input file-input-bordered file-input-primary w-full" />
                    @error('importFile') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    <div class="text-xs text-base-content/50 mt-2 space-y-1">
                        <p>Format kolom: <code>No. ID</code>, <code>Tanggal</code>, <code>Scan Masuk</code>, <code>Scan Pulang</code></p>
                        <p>No. ID = PIN mesin absen</p>
                        <p>Tanggal = dd/mm/YYYY</p>
                    </div>
                </div>
            @else
                <div class="space-y-3">
                    <div class="flex items-center gap-2 text-success">
                        <x-icon name="o-check-circle" class="w-6 h-6" />
                        <span class="font-medium">{{ $importSuccess }} data berhasil diimport</span>
                    </div>
                    @if ($importDuplicate)
                        <div class="flex items-center gap-2 text-warning">
                            <x-icon name="o-exclamation-triangle" class="w-6 h-6" />
                            <span class="font-medium">{{ $importDuplicate }} data duplikat dilewati</span>
                        </div>
                    @endif
                    @if (count($importErrors))
                        <div>
                            <span class="text-sm text-base-content/50 font-medium">Gagal ({{ count($importErrors) }}):</span>
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
                <x-button label="Import" class="btn-primary" wire:click="import" spinner />
            @else
                <x-button label="Tutup" wire:click="resetImport" class="btn-primary" />
            @endunless
        </x-slot:actions>
    </x-modal>

    {{-- Modal Kolektif --}}
    <x-modal wire:model="collectiveModal" title="Keterangan Kolektif"
        subtitle="Ubah keterangan untuk {{ count($this->selectedRows) }} absensi terpilih" box-class="!max-w-md">
        <div class="space-y-4">
            <x-select wire:model="collectiveKeterangan" label="Pilih Keterangan"
                :options="$keteranganOptions" option-value="id" option-label="name" />
            <x-input wire:model="collectiveCatatan" label="Catatan Tambahan (Opsional)"
                placeholder="Contoh: Penugasan proyek di luar kota..." />
            <div class="alert alert-warning text-sm">
                <x-icon name="o-exclamation-triangle" class="w-5 h-5" />
                <span>Tindakan ini akan menimpa status absensi saat ini untuk semua karyawan yang dipilih.</span>
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Batal" wire:click="closeModal" type="button" />
            <x-button label="Simpan Perubahan" class="btn-primary" wire:click="saveCollective" spinner />
        </x-slot:actions>
    </x-modal>
</div>
