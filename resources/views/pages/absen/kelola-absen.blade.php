<?php

use App\Models\Absen;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use Toast, WithPagination;

    public string $search = '';
    public string $filterTanggal = '';
    public string $filterKeterangan = '';

    public bool $collectiveModal = false;
    public array $selectedRows = [];
    public string $collectiveKeterangan = 'Hadir';
    public string $collectiveCatatan = '';

    public bool $editModal = false;
    public ?int $editAbsenId = null;
    public string $editKeterangan = 'Hadir';
    public string $editCatatan = '';

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

    public function editAbsen(int $id): void
    {
        $absen = Absen::findOrFail($id);
        $this->editAbsenId = $id;
        $this->editKeterangan = $absen->keterangan;
        $this->editCatatan = '';
        $this->editModal = true;
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editKeterangan' => 'required|string|in:Hadir,Dinas Luar,Cuti,Sakit,Izin,Tidak Absen,Alpa,Off,Libur,Lainnya',
        ]);

        $absen = Absen::findOrFail($this->editAbsenId);
        $absen->update(['keterangan' => $this->editKeterangan]);

        $this->success("Keterangan {$absen->karyawan->nama_karyawan} diperbarui.", position: 'toast-top toast-end');
        $this->editModal = false;
        $this->editAbsenId = null;
    }

    public function closeModal(): void
    {
        $this->collectiveModal = false;
        $this->editModal = false;
        $this->editAbsenId = null;
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
                <div class="flex gap-1">
                    <x-button icon="o-pencil" wire:click="editAbsen({{ $row->id }})"
                        class="btn-ghost btn-sm text-primary" spinner />
                </div>
            @endscope
        </x-table>
    </x-card>

    {{-- Modal Edit --}}
    <x-modal wire:model="editModal" title="Edit Keterangan Absensi" box-class="!max-w-md">
        <div class="space-y-4">
            <x-select wire:model="editKeterangan" label="Keterangan"
                :options="$keteranganOptions" option-value="id" option-label="name" />
            <x-input wire:model="editCatatan" label="Catatan (Opsional)"
                placeholder="Contoh: Penugasan proyek..." />
        </div>
        <x-slot:actions>
            <x-button label="Batal" wire:click="closeModal" type="button" />
            <x-button label="Simpan" class="btn-primary" wire:click="saveEdit" spinner />
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
