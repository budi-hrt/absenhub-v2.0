<?php

use App\Models\MasaKontrak;
use App\Models\Kontrak;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use Toast, WithPagination;

    public string $search = '';
    public bool $modal = false;
    public ?int $editId = null;
    public string $status_kontrak = '';
    public bool $is_active = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function save(): void
    {
        $this->validate([
            'status_kontrak' => 'required|string|max:255',
        ]);

        if ($this->editId) {
            $m = MasaKontrak::findOrFail($this->editId);
            $m->update([
                'status_kontrak' => $this->status_kontrak,
                'is_active' => $this->is_active,
            ]);
            $this->success("Masa kontrak {$m->status_kontrak} berhasil diperbarui.", position: 'toast-top toast-end');
        } else {
            $m = MasaKontrak::create([
                'status_kontrak' => $this->status_kontrak,
                'is_active' => $this->is_active,
            ]);
            $this->success("Masa kontrak {$m->status_kontrak} berhasil ditambahkan.", position: 'toast-top toast-end');
        }

        $this->closeModal();
    }

    public function edit(int $id): void
    {
        $m = MasaKontrak::findOrFail($id);
        $this->editId = $m->id;
        $this->status_kontrak = $m->status_kontrak;
        $this->is_active = $m->is_active;
        $this->modal = true;
    }

    public function tambah(): void
    {
        $this->reset('editId', 'status_kontrak');
        $this->is_active = true;
        $this->modal = true;
    }

    public function toggleActive(int $id): void
    {
        $m = MasaKontrak::findOrFail($id);
        $m->update(['is_active' => !$m->is_active]);
        $this->success(
            "Masa kontrak {$m->status_kontrak} di" . ($m->is_active ? 'aktifkan' : 'nonaktifkan'),
            position: 'toast-top toast-end'
        );
    }

    public function closeModal(): void
    {
        $this->modal = false;
        $this->editId = null;
        $this->status_kontrak = '';
        $this->is_active = true;
    }

    public function masaKontraks()
    {
        return MasaKontrak::selectSub(
                Kontrak::whereColumn('masa_kontrak_id', 'masa_kontraks.id')
                    ->whereHas('karyawan', fn($q) => $q->where('is_active', true)->where('status_id', 2))
                    ->selectRaw('COUNT(DISTINCT karyawan_id)'),
                'total_karyawan'
            )
            ->addSelect('masa_kontraks.*')
            ->when($this->search, fn($q) => $q->where('status_kontrak', 'like', "%{$this->search}%"))
            ->orderBy('status_kontrak')
            ->paginate(10);
    }

    public function with(): array
    {
        $masaKontraks = $this->masaKontraks();
        $start = $masaKontraks->firstItem();
        $masaKontraks->getCollection()->transform(fn($item, $i) => tap($item)->setAttribute('row_no', $start + $i));

        return [
            'masaKontraks' => $masaKontraks,
        ];
    }
};
?>

<div>
    <x-header title="Master Masa Kontrak" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Cari masa kontrak..." wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Tambah Masa Kontrak" icon="o-plus" class="btn-primary" wire:click="tambah" />
        </x-slot:actions>
    </x-header>

    <x-card shadow>
        <x-table :headers="[
            ['key' => 'no', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'status_kontrak', 'label' => 'Masa Kontrak', 'sortable' => false],
            ['key' => 'total_karyawan', 'label' => 'Jumlah Karyawan', 'class' => 'w-32'],
            ['key' => 'status', 'label' => 'Status', 'class' => 'w-24'],
        ]" :rows="$masaKontraks" with-pagination>
            @scope('cell_no', $row)
                <span class="text-sm text-base-content/50">{{ $row->row_no }}</span>
            @endscope

            @scope('cell_total_karyawan', $row)
                <span class="text-sm">{{ $row->total_karyawan ?? 0 }} karyawan</span>
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
    <x-modal wire:model="modal" title="{{ $editId ? 'Edit Masa Kontrak' : 'Tambah Masa Kontrak' }}"
        subtitle="{{ $editId ? 'Ubah data masa kontrak' : 'Buat masa kontrak baru' }}">
        <x-form wire:submit.prevent="save">
            <x-input wire:model="status_kontrak" label="Masa Kontrak" placeholder="Contoh: 3 Bulan, 6 Bulan, 1 Tahun" required />
            <x-checkbox wire:model="is_active" label="Aktif" />
            <x-slot:actions>
                <x-button label="Batal" wire:click="closeModal" type="button" />
                <x-button label="{{ $editId ? 'Simpan Perubahan' : 'Simpan' }}" class="btn-primary" type="submit" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
