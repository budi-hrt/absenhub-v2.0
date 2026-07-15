<?php

use App\Models\Status;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use Toast, WithPagination;

    public string $search = '';
    public bool $modal = false;
    public ?int $editId = null;
    public string $nama_status = '';
    public bool $is_active = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function save(): void
    {
        $this->validate([
            'nama_status' => 'required|string|max:255',
        ]);

        if ($this->editId) {
            $s = Status::findOrFail($this->editId);
            $s->update([
                'nama_status' => $this->nama_status,
                'is_active' => $this->is_active,
            ]);
            $this->success("Status kerja {$s->nama_status} berhasil diperbarui.", position: 'toast-top toast-end');
        } else {
            $s = Status::create([
                'nama_status' => $this->nama_status,
                'is_active' => $this->is_active,
            ]);
            $this->success("Status kerja {$s->nama_status} berhasil ditambahkan.", position: 'toast-top toast-end');
        }

        $this->closeModal();
    }

    public function edit(int $id): void
    {
        $s = Status::findOrFail($id);
        $this->editId = $s->id;
        $this->nama_status = $s->nama_status;
        $this->is_active = $s->is_active;
        $this->modal = true;
    }

    public function tambah(): void
    {
        $this->reset('editId', 'nama_status');
        $this->is_active = true;
        $this->modal = true;
    }

    public function toggleActive(int $id): void
    {
        $s = Status::findOrFail($id);
        $s->update(['is_active' => !$s->is_active]);
        $this->success(
            "Status kerja {$s->nama_status} di" . ($s->is_active ? 'aktifkan' : 'nonaktifkan'),
            position: 'toast-top toast-end'
        );
    }

    public function closeModal(): void
    {
        $this->modal = false;
        $this->editId = null;
        $this->nama_status = '';
        $this->is_active = true;
    }

    public function statuses()
    {
        return Status::withCount('karyawans')
            ->when($this->search, fn($q) => $q->where('nama_status', 'like', "%{$this->search}%"))
            ->orderBy('nama_status')
            ->paginate(10);
    }

    public function with(): array
    {
        $statuses = $this->statuses();
        $start = $statuses->firstItem();
        $statuses->getCollection()->transform(fn($item, $i) => tap($item)->setAttribute('row_no', $start + $i));

        return [
            'statuses' => $statuses,
        ];
    }
};
?>

<div>
    <x-header title="Master Status Kerja" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Cari status kerja..." wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Tambah Status Kerja" icon="o-plus" class="btn-primary" wire:click="tambah" />
        </x-slot:actions>
    </x-header>

    <x-card shadow>
        <x-table :headers="[
            ['key' => 'no', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'nama_status', 'label' => 'Nama Status Kerja', 'sortable' => false],
            ['key' => 'karyawans_count', 'label' => 'Jumlah Karyawan', 'class' => 'w-32'],
            ['key' => 'status', 'label' => 'Status', 'class' => 'w-24'],
        ]" :rows="$statuses" with-pagination>
            @scope('cell_no', $row)
                <span class="text-sm text-base-content/50">{{ $row->row_no }}</span>
            @endscope

            @scope('cell_karyawans_count', $row)
                <span class="text-sm">{{ $row->karyawans_count }} karyawan</span>
            @endscope

            @scope('cell_status', $row)
                <x-badge :value="$row->is_active ? 'Aktif' : 'Nonaktif'"
                    :class="$row->is_active ? 'badge-success badge-soft' : 'badge-error badge-soft'" />
            @endscope

            @scope('actions', $row)
                <div class="flex gap-1">
                    <x-button icon="o-pencil" wire:click="edit({{ $row->id }})"
                        class="btn-ghost btn-sm text-primary" spinner />
                    <x-button :icon="$row->is_active ? 'o-no-symbol' : 'o-check-badge'"
                        wire:click="toggleActive({{ $row->id }})"
                        :class="$row->is_active ? 'btn-ghost btn-sm text-error' : 'btn-ghost btn-sm text-success'"
                        spinner />
                </div>
            @endscope
        </x-table>
    </x-card>

    {{-- Modal Create/Edit --}}
    <x-modal wire:model="modal" title="{{ $editId ? 'Edit Status Kerja' : 'Tambah Status Kerja' }}"
        subtitle="{{ $editId ? 'Ubah data status kerja' : 'Buat status kerja baru' }}">
        <x-form wire:submit.prevent="save">
            <x-input wire:model="nama_status" label="Nama Status Kerja" placeholder="Masukkan nama status kerja" required />
            <x-checkbox wire:model="is_active" label="Aktif" />
            <x-slot:actions>
                <x-button label="Batal" wire:click="closeModal" type="button" />
                <x-button label="{{ $editId ? 'Simpan Perubahan' : 'Simpan' }}" class="btn-primary" type="submit" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
