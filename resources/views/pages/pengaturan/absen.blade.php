<?php

use App\Models\PengaturanAbsen;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use Toast, WithPagination;

    public string $search = '';
    public bool $modal = false;
    public ?int $editId = null;

    public string $nama_pengaturan = '';
    public string $jam_masuk = '08:00';
    public string $jam_pulang = '17:00';
    public int $toleransi_menit = 10;
    public string $tanggal_mulai = '';
    public ?string $tanggal_akhir = '';
    public bool $is_active = true;

    public function boot(): void
    {
        if (empty($this->tanggal_mulai)) {
            $this->tanggal_mulai = now()->format('Y-m-d');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function save(): void
    {
        $this->validate([
            'nama_pengaturan' => 'required|string|max:255',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_pulang' => 'required|date_format:H:i',
            'toleransi_menit' => 'required|integer|min:0|max:60',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'is_active' => 'boolean',
        ]);

        $data = [
            'nama_pengaturan' => $this->nama_pengaturan,
            'jam_masuk' => $this->jam_masuk,
            'jam_pulang' => $this->jam_pulang,
            'toleransi_menit' => $this->toleransi_menit,
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_akhir' => $this->tanggal_akhir ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->editId) {
            $p = PengaturanAbsen::findOrFail($this->editId);
            $p->update($data);
            $this->success("Pengaturan \"{$p->nama_pengaturan}\" berhasil diperbarui.", position: 'toast-top toast-end');
        } else {
            $p = PengaturanAbsen::create($data);
            $this->success("Pengaturan \"{$p->nama_pengaturan}\" berhasil ditambahkan.", position: 'toast-top toast-end');
        }

        $this->closeModal();
    }

    public function edit(int $id): void
    {
        $p = PengaturanAbsen::findOrFail($id);
        $this->editId = $p->id;
        $this->nama_pengaturan = $p->nama_pengaturan;
        $this->jam_masuk = $p->jam_masuk->format('H:i');
        $this->jam_pulang = $p->jam_pulang->format('H:i');
        $this->toleransi_menit = $p->toleransi_menit;
        $this->tanggal_mulai = $p->tanggal_mulai->format('Y-m-d');
        $this->tanggal_akhir = $p->tanggal_akhir?->format('Y-m-d');
        $this->is_active = $p->is_active;
        $this->modal = true;
    }

    public function tambah(): void
    {
        $this->reset('editId', 'nama_pengaturan');
        $this->jam_masuk = '08:00';
        $this->jam_pulang = '17:00';
        $this->toleransi_menit = 10;
        $this->tanggal_mulai = now()->format('Y-m-d');
        $this->tanggal_akhir = '';
        $this->is_active = true;
        $this->modal = true;
    }

    public function toggleActive(int $id): void
    {
        $p = PengaturanAbsen::findOrFail($id);
        $p->update(['is_active' => !$p->is_active]);
        $this->success(
            "Pengaturan \"{$p->nama_pengaturan}\" di" . ($p->is_active ? 'aktifkan' : 'nonaktifkan'),
            position: 'toast-top toast-end'
        );
    }

    public function delete(int $id): void
    {
        $p = PengaturanAbsen::findOrFail($id);
        $p->delete();
        $this->success("Pengaturan \"{$p->nama_pengaturan}\" berhasil dihapus.", position: 'toast-top toast-end');
    }

    public function closeModal(): void
    {
        $this->modal = false;
        $this->editId = null;
    }

    #[Computed]
    public function pengaturans()
    {
        return PengaturanAbsen::query()
            ->when($this->search, fn($q) => $q->where('nama_pengaturan', 'like', "%{$this->search}%"))
            ->orderBy('tanggal_mulai', 'desc')
            ->paginate(10);
    }

    public function with(): array
    {
        $pengaturans = $this->pengaturans;
        $start = $pengaturans->firstItem();
        $pengaturans->getCollection()->transform(fn($item, $i) => tap($item)->setAttribute('row_no', $start + $i));

        return [
            'pengaturans' => $pengaturans,
        ];
    }
};
?>

<div>
    <x-header title="Pengaturan Jam Kerja" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Cari pengaturan..." wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Tambah Pengaturan" icon="o-plus" class="btn-primary" wire:click="tambah" />
        </x-slot:actions>
    </x-header>

    <x-card shadow>
        <x-table :headers="[
            ['key' => 'no', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'nama', 'label' => 'NAMA', 'sortable' => false],
            ['key' => 'jam', 'label' => 'JAM KERJA', 'sortable' => false],
            ['key' => 'toleransi', 'label' => 'TOLERANSI', 'class' => 'w-24'],
            ['key' => 'periode', 'label' => 'PERIODE', 'sortable' => false],
            ['key' => 'status', 'label' => 'STATUS', 'class' => 'w-24'],
        ]" :rows="$pengaturans" with-pagination>
            @scope('cell_no', $row)
                <span class="text-sm text-base-content/50">{{ $row->row_no }}</span>
            @endscope

            @scope('cell_nama', $row)
                <span class="text-sm font-medium">{{ $row->nama_pengaturan }}</span>
            @endscope

            @scope('cell_jam', $row)
                <span class="text-sm font-mono">{{ $row->jam_masuk->format('H:i') }} - {{ $row->jam_pulang->format('H:i') }}</span>
            @endscope

            @scope('cell_toleransi', $row)
                <span class="text-sm">{{ $row->toleransi_menit }} menit</span>
            @endscope

            @scope('cell_periode', $row)
                <span class="text-sm">
                    {{ $row->tanggal_mulai->format('d M Y') }}
                    —
                    {{ $row->tanggal_akhir ? $row->tanggal_akhir->format('d M Y') : 'Berlangsung' }}
                </span>
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
                    <x-button icon="o-trash" wire:click="delete({{ $row->id }})"
                        class="btn-ghost btn-sm text-error" spinner />
                </div>
            @endscope
        </x-table>
    </x-card>

    {{-- Modal Create/Edit --}}
    <x-modal wire:model="modal" title="{{ $editId ? 'Edit Pengaturan' : 'Tambah Pengaturan' }}"
        subtitle="{{ $editId ? 'Ubah data pengaturan jam kerja' : 'Buat pengaturan jam kerja baru' }}" box-class="!max-w-lg">
        <x-form wire:submit.prevent="save">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <x-input wire:model="nama_pengaturan" label="Nama Pengaturan"
                        placeholder="Contoh: Jam Kerja Normal" required />
                </div>
                <x-input wire:model="jam_masuk" label="Jam Masuk" type="time" required />
                <x-input wire:model="jam_pulang" label="Jam Pulang" type="time" required />
                <x-input wire:model="toleransi_menit" label="Toleransi (menit)" type="number" min="0" max="60" required />
                <div></div>
                <x-input wire:model="tanggal_mulai" label="Tanggal Mulai" type="date" required />
                <x-input wire:model="tanggal_akhir" label="Tanggal Akhir (opsional)" type="date" />
            </div>
            <x-checkbox wire:model="is_active" label="Aktif" class="mt-4" />
            <x-slot:actions>
                <x-button label="Batal" wire:click="closeModal" type="button" />
                <x-button label="{{ $editId ? 'Simpan Perubahan' : 'Simpan' }}" class="btn-primary" type="submit" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
