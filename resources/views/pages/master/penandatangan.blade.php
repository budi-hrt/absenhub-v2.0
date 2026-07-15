<?php

use App\Models\Penandatangan;
use App\Models\Jabatan;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use Toast, WithPagination;

    public string $search = '';
    public bool $modal = false;
    public ?int $editId = null;
    public string $nama_penandatangan = '';
    public string $alamat = '';
    public ?int $jabatan_id = null;
    public bool $is_active = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function save(): void
    {
        $this->validate([
            'nama_penandatangan' => 'required|string|max:255',
            'alamat' => 'required|string',
            'jabatan_id' => 'nullable|exists:jabatans,id',
        ]);

        if ($this->editId) {
            $p = Penandatangan::findOrFail($this->editId);
            $p->update([
                'nama_penandatangan' => $this->nama_penandatangan,
                'alamat' => $this->alamat,
                'jabatan_id' => $this->jabatan_id,
                'is_active' => $this->is_active,
            ]);
            $this->success("Penandatangan {$p->nama_penandatangan} berhasil diperbarui.", position: 'toast-top toast-end');
        } else {
            $p = Penandatangan::create([
                'nama_penandatangan' => $this->nama_penandatangan,
                'alamat' => $this->alamat,
                'jabatan_id' => $this->jabatan_id,
                'is_active' => $this->is_active,
            ]);
            $this->success("Penandatangan {$p->nama_penandatangan} berhasil ditambahkan.", position: 'toast-top toast-end');
        }

        $this->closeModal();
    }

    public function edit(int $id): void
    {
        $p = Penandatangan::with('jabatan')->findOrFail($id);
        $this->editId = $p->id;
        $this->nama_penandatangan = $p->nama_penandatangan;
        $this->alamat = $p->alamat;
        $this->jabatan_id = $p->jabatan_id;
        $this->is_active = $p->is_active;
        $this->modal = true;
    }

    public function tambah(): void
    {
        $this->reset('editId', 'nama_penandatangan', 'alamat', 'jabatan_id');
        $this->is_active = true;
        $this->modal = true;
    }

    public function toggleActive(int $id): void
    {
        $p = Penandatangan::findOrFail($id);
        $p->update(['is_active' => !$p->is_active]);
        $this->success(
            "Penandatangan {$p->nama_penandatangan} di" . ($p->is_active ? 'aktifkan' : 'nonaktifkan'),
            position: 'toast-top toast-end'
        );
    }

    public function closeModal(): void
    {
        $this->modal = false;
        $this->editId = null;
        $this->nama_penandatangan = '';
        $this->alamat = '';
        $this->jabatan_id = null;
        $this->is_active = true;
    }

    public function penandatangans()
    {
        return Penandatangan::with('jabatan')
            ->when($this->search, fn($q) => $q->where('nama_penandatangan', 'like', "%{$this->search}%"))
            ->orderBy('nama_penandatangan')
            ->paginate(10);
    }

    public function with(): array
    {
        $penandatangans = $this->penandatangans();
        $start = $penandatangans->firstItem();
        $penandatangans->getCollection()->transform(fn($item, $i) => tap($item)->setAttribute('row_no', $start + $i));

        return [
            'penandatangans' => $penandatangans,
            'jabatans' => Jabatan::where('is_active', true)->orderBy('nama_jabatan')->get(),
        ];
    }
};
?>

<div>
    <x-header title="Master Penandatangan" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Cari penandatangan..." wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Tambah Penandatangan" icon="o-plus" class="btn-primary" wire:click="tambah" />
        </x-slot:actions>
    </x-header>

    <x-card shadow>
        <x-table :headers="[
            ['key' => 'no', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'nama_penandatangan', 'label' => 'Nama Penandatangan', 'sortable' => false],
            ['key' => 'alamat', 'label' => 'Alamat', 'sortable' => false],
            ['key' => 'jabatan', 'label' => 'Jabatan', 'sortable' => false],
            ['key' => 'status', 'label' => 'Status', 'class' => 'w-24'],
        ]" :rows="$penandatangans" with-pagination>
            @scope('cell_no', $row)
                <span class="text-sm text-base-content/50">{{ $row->row_no }}</span>
            @endscope

            @scope('cell_jabatan', $row)
                <span class="text-sm">{{ $row->jabatan?->nama_jabatan ?? '-' }}</span>
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
    <x-modal wire:model="modal" title="{{ $editId ? 'Edit Penandatangan' : 'Tambah Penandatangan' }}"
        subtitle="{{ $editId ? 'Ubah data penandatangan' : 'Buat penandatangan baru' }}">
        <x-form wire:submit.prevent="save">
            <x-input wire:model="nama_penandatangan" label="Nama Penandatangan" placeholder="Masukkan nama" required />
            <x-textarea wire:model="alamat" label="Alamat" placeholder="Masukkan alamat" required />
            <x-select wire:model="jabatan_id" label="Jabatan" placeholder="Pilih jabatan"
                :options="$jabatans->map(fn($j) => ['id' => $j->id, 'name' => $j->nama_jabatan])->toArray()"
                option-value="id" option-label="name" />
            <x-checkbox wire:model="is_active" label="Aktif" />
            <x-slot:actions>
                <x-button label="Batal" wire:click="closeModal" type="button" />
                <x-button label="{{ $editId ? 'Simpan Perubahan' : 'Simpan' }}" class="btn-primary" type="submit" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
