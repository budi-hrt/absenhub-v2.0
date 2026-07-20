<?php

use App\Models\JatahCuti;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

new #[Layout('layouts.app')] #[Title('Pengaturan Jatah Cuti')] class extends Component {
    use Toast;

    public int $tahun;
    public int $jatah_cuti = 12;

    public bool $showForm = false;

    public function mount()
    {
        $this->tahun = now()->year;
    }

    public function edit(int $id)
    {
        $data = JatahCuti::findOrFail($id);
        $this->tahun = $data->tahun;
        $this->jatah_cuti = $data->jatah_cuti;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate([
            'tahun' => 'required|integer|min:2020|max:2099',
            'jatah_cuti' => 'required|integer|min:0|max:365',
        ]);

        JatahCuti::updateOrCreate(
            ['tahun' => $this->tahun],
            ['jatah_cuti' => $this->jatah_cuti]
        );

        $this->showForm = false;
        $this->reset(['tahun', 'jatah_cuti']);
        $this->tahun = now()->year;
        $this->jatah_cuti = 12;
        $this->success('Jatah cuti berhasil disimpan.');
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if ($this->showForm) {
            $this->tahun = now()->year;
            $this->jatah_cuti = 12;
        }
    }

    public function delete(int $id)
    {
        JatahCuti::findOrFail($id)->delete();
        $this->success('Data jatah cuti berhasil dihapus.');
    }

    public function with(): array
    {
        $daftarJatah = JatahCuti::orderByDesc('tahun')->get();

        return [
            'daftarJatah' => $daftarJatah,
        ];
    }
}; ?>

<div>
    <x-header title="Pengaturan Jatah Cuti" separator progress-indicator>
        <x-slot:actions>
            <x-button wire:click="toggleForm" :label="$showForm ? 'Batal' : 'Tambah Tahun'" :icon="$showForm ? 'o-x-mark' : 'o-plus'" :class="$showForm ? 'btn-ghost' : 'btn-primary'" />
        </x-slot:actions>
    </x-header>

    {{-- Form --}}
    @if($showForm)
    <x-card title="Set Jatah Cuti" class="mb-6 border border-base-300 shadow-sm">
        <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <x-input label="Tahun" wire:model="tahun" type="number" min="2020" max="2099" />
            <x-input label="Jatah Cuti (hari)" wire:model="jatah_cuti" type="number" min="0" max="365" />
            <div class="pt-2">
                <x-button type="submit" label="Simpan" icon="o-check" class="btn-primary w-full" spinner="save" />
            </div>
        </form>
    </x-card>
    @endif

    {{-- Tabel --}}
    <x-card class="border border-base-300 shadow-sm">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tahun</th>
                        <th>Jatah Cuti</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($daftarJatah as $item)
                        <tr class="hover">
                            <td class="font-bold text-lg">{{ $item->tahun }}</td>
                            <td>
                                <span class="badge badge-primary badge-lg font-bold">{{ $item->jatah_cuti }} hari</span>
                            </td>
                            <td class="text-right">
                                <x-button wire:click="edit({{ $item->id }})" icon="o-pencil-square" class="btn-sm btn-ghost text-primary" spinner />
                                <x-button wire:click="delete({{ $item->id }})" wire:confirm="Yakin ingin menghapus jatah cuti tahun {{ $item->tahun }}?" icon="o-trash" class="btn-sm btn-ghost text-error" spinner />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-8 text-base-content/50">
                                Belum ada data jatah cuti. Klik "Tambah Tahun" untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
