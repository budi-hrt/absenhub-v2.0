<?php

use App\Models\Jabatan;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use Toast, WithPagination;

    public string $search = '';
    public bool $modal = false;
    public ?int $editId = null;
    public string $nama_jabatan = '';
    public bool $is_active = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function save(): void
    {
        $this->validate([
            'nama_jabatan' => 'required|string|max:255',
        ]);

        if ($this->editId) {
            $j = Jabatan::findOrFail($this->editId);
            $j->update([
                'nama_jabatan' => $this->nama_jabatan,
                'is_active' => $this->is_active,
            ]);
            $this->success("Jabatan {$j->nama_jabatan} berhasil diperbarui.", position: 'toast-top toast-end');
        } else {
            $j = Jabatan::create([
                'nama_jabatan' => $this->nama_jabatan,
                'is_active' => $this->is_active,
            ]);
            $this->success("Jabatan {$j->nama_jabatan} berhasil ditambahkan.", position: 'toast-top toast-end');
        }

        $this->closeModal();
    }

    public function edit(int $id): void
    {
        $j = Jabatan::findOrFail($id);
        $this->editId = $j->id;
        $this->nama_jabatan = $j->nama_jabatan;
        $this->is_active = $j->is_active;
        $this->modal = true;
    }

    public function tambah(): void
    {
        $this->reset('editId', 'nama_jabatan');
        $this->is_active = true;
        $this->modal = true;
    }

    public function toggleActive(int $id): void
    {
        $j = Jabatan::findOrFail($id);
        $j->update(['is_active' => !$j->is_active]);
        $this->success(
            "Jabatan {$j->nama_jabatan} di" . ($j->is_active ? 'aktifkan' : 'nonaktifkan'),
            position: 'toast-top toast-end'
        );
    }

    public function closeModal(): void
    {
        $this->modal = false;
        $this->editId = null;
        $this->nama_jabatan = '';
        $this->is_active = true;
    }

    public function jabatans()
    {
        return Jabatan::withCount('karyawans')
            ->when($this->search, fn($q) => $q->where('nama_jabatan', 'like', "%{$this->search}%"))
            ->orderBy('nama_jabatan')
            ->paginate(10);
    }

    public function with(): array
    {
        $jabatans = $this->jabatans();
        $start = $jabatans->firstItem();
        $jabatans->getCollection()->transform(fn($item, $i) => tap($item)->setAttribute('row_no', $start + $i));

        return [
            'jabatans' => $jabatans,
        ];
    }
};
?>

<div>
    <x-header title="Master Data Jabatan" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Cari jabatan..." wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Tambah Jabatan" icon="o-plus" class="btn-primary" wire:click="tambah" />
        </x-slot:actions>
    </x-header>

    <x-card shadow>
        <x-table :headers="[
            ['key' => 'no', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'nama_jabatan', 'label' => 'Nama Jabatan', 'sortable' => false],
            ['key' => 'karyawans_count', 'label' => 'Jumlah Karyawan', 'class' => 'w-32'],
            ['key' => 'status', 'label' => 'Status', 'class' => 'w-24'],
        ]" :rows="$jabatans" with-pagination>
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
    <x-modal wire:model="modal" title="{{ $editId ? 'Edit Jabatan' : 'Tambah Jabatan' }}"
        subtitle="{{ $editId ? 'Ubah data jabatan' : 'Buat jabatan baru' }}">
        <x-form wire:submit.prevent="save">
            <x-input wire:model="nama_jabatan" label="Nama Jabatan" placeholder="Masukkan nama jabatan" required />
            <x-checkbox wire:model="is_active" label="Aktif" />
            <x-slot:actions>
                <x-button label="Batal" wire:click="closeModal" type="button" />
                <x-button label="{{ $editId ? 'Simpan Perubahan' : 'Simpan' }}" class="btn-primary" type="submit" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
