<?php

use App\Models\FeatureFlag;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Mary\Traits\Toast;

new class extends Component
{
    use Toast;

    public string $search = '';

    public string $filterGroup = '';

    public bool $editModal = false;
    public ?int $editingFeatureId = null;
    public string $editingName = '';
    public string $editingDescription = '';

    #[Computed]
    public function features()
    {
        return FeatureFlag::query()
            ->when($this->search, function ($q) {
                $term = trim($this->search);
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', "%{$term}%")
                        ->orWhere('key', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%");
                });
            })
            ->when($this->filterGroup, function ($q) {
                $q->where('group', $this->filterGroup);
            })
            ->orderBy('name')
            ->get()
            ->groupBy('group');
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total' => FeatureFlag::count(),
            'enabled' => FeatureFlag::where('is_enabled', true)->count(),
            'disabled' => FeatureFlag::where('is_enabled', false)->count(),
        ];
    }

    public function toggleFeature(int $id): void
    {
        $flag = FeatureFlag::findOrFail($id);
        $flag->is_enabled = !$flag->is_enabled;
        $flag->save();

        $statusStr = $flag->is_enabled ? 'diaktifkan' : 'dinonaktifkan';
        $this->success("Fitur '{$flag->name}' berhasil {$statusStr}.", position: 'toast-top toast-end');
    }

    public function editFeature(int $id): void
    {
        $flag = FeatureFlag::findOrFail($id);
        $this->editingFeatureId = $flag->id;
        $this->editingName = $flag->name;
        $this->editingDescription = $flag->description ?? '';
        $this->editModal = true;
    }

    public function saveFeature(): void
    {
        $this->validate([
            'editingName' => 'required|string|max:255',
            'editingDescription' => 'nullable|string|max:1000',
        ]);

        $flag = FeatureFlag::findOrFail($this->editingFeatureId);
        $flag->update([
            'name' => $this->editingName,
            'description' => $this->editingDescription,
        ]);

        $this->success("Informasi fitur '{$flag->name}' berhasil diperbarui.", position: 'toast-top toast-end');
        $this->editModal = false;
        $this->reset(['editingFeatureId', 'editingName', 'editingDescription']);
    }

    public function closeModal(): void
    {
        $this->editModal = false;
        $this->reset(['editingFeatureId', 'editingName', 'editingDescription']);
    }
};
?>

<div>
    <x-header title="Feature Flags" subtitle="Kelola aktifasi modul dan fitur aplikasi" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Cari fitur..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
    </x-header>

    {{-- Warning Alert --}}
    <div class="alert alert-warning shadow-sm mb-6 text-sm">
        <x-icon name="o-exclamation-triangle" class="w-5 h-5 text-warning-content shrink-0" />
        <span>Perubahan feature flag akan langsung berlaku pada sistem. Pastikan Anda memahami dampak dari setiap perubahan.</span>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="stats shadow border border-base-200 bg-base-100">
            <div class="stat">
                <div class="stat-title text-xs font-semibold">Total Fitur</div>
                <div class="stat-value text-2xl font-bold mt-1">{{ $this->stats['total'] }}</div>
                <div class="stat-desc text-[10px] text-base-content/40">Seluruh modul terdaftar</div>
            </div>
        </div>
        <div class="stats shadow border border-base-200 bg-base-100">
            <div class="stat">
                <div class="stat-title text-xs font-semibold text-success">Aktif</div>
                <div class="stat-value text-2xl font-bold mt-1 text-success">{{ $this->stats['enabled'] }}</div>
                <div class="stat-desc text-[10px] text-success/70">Siap digunakan</div>
            </div>
        </div>
        <div class="stats shadow border border-base-200 bg-base-100">
            <div class="stat">
                <div class="stat-title text-xs font-semibold text-error">Nonaktif</div>
                <div class="stat-value text-2xl font-bold mt-1 text-error">{{ $this->stats['disabled'] }}</div>
                <div class="stat-desc text-[10px] text-error/70">Fitur dimatikan</div>
            </div>
        </div>
    </div>

    {{-- Group Filter Tabs --}}
    <div class="flex gap-2 mb-6 border-b border-base-200 pb-3 overflow-x-auto">
        <button wire:click="$set('filterGroup', '')" class="btn btn-sm {{ $filterGroup === '' ? 'btn-primary' : 'btn-ghost' }}">
            Semua
        </button>
        <button wire:click="$set('filterGroup', 'absensi')" class="btn btn-sm {{ $filterGroup === 'absensi' ? 'btn-primary' : 'btn-ghost' }}">
            Absensi
        </button>
        <button wire:click="$set('filterGroup', 'karyawan')" class="btn btn-sm {{ $filterGroup === 'karyawan' ? 'btn-primary' : 'btn-ghost' }}">
            Karyawan
        </button>
        <button wire:click="$set('filterGroup', 'laporan')" class="btn btn-sm {{ $filterGroup === 'laporan' ? 'btn-primary' : 'btn-ghost' }}">
            Laporan
        </button>
    </div>

    {{-- Features Card Lists --}}
    @if(count($this->features) > 0)
        @foreach ($this->features as $groupName => $flags)
            <x-card shadow class="mb-6" separator>
                <x-slot:title>
                    <div class="flex items-center gap-2">
                        @php
                            $groupColors = match($groupName) {
                                'absensi' => 'text-primary',
                                'karyawan' => 'text-success',
                                'laporan' => 'text-warning',
                                default => 'text-base-content/60'
                            };
                            $groupIcon = match($groupName) {
                                'absensi' => 'o-calendar-days',
                                'karyawan' => 'o-users',
                                'laporan' => 'o-chart-bar',
                                default => 'o-cog'
                            };
                        @endphp
                        <x-icon name="{{ $groupIcon }}" class="w-5 h-5 {{ $groupColors }}" />
                        <span class="capitalize font-bold text-base">{{ $groupName }}</span>
                    </div>
                </x-slot:title>

                <div class="divide-y divide-base-200">
                    @foreach ($flags as $flag)
                        <div class="flex items-center justify-between py-3 transition-all duration-200 hover:bg-base-200/20 px-2 rounded-lg">
                            <div class="flex-1 min-w-0 pr-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-sm text-base-content">{{ $flag->name }}</span>
                                    <span class="font-mono text-[10px] text-base-content/40 bg-base-200 px-1.5 py-0.5 rounded">{{ $flag->key }}</span>
                                </div>
                                <p class="text-xs text-base-content/60 leading-relaxed">{{ $flag->description }}</p>
                            </div>
                            <div class="flex items-center gap-4 shrink-0">
                                <div class="text-right">
                                    @if ($flag->is_enabled)
                                        <span class="badge badge-success badge-sm font-medium">Aktif</span>
                                    @else
                                        <span class="badge badge-error badge-sm font-medium text-white">Nonaktif</span>
                                    @endif
                                </div>
                                <input type="checkbox" class="toggle toggle-primary toggle-sm" 
                                    wire:click="toggleFeature({{ $flag->id }})" 
                                    wire:loading.attr="disabled"
                                    {{ $flag->is_enabled ? 'checked' : '' }} />
                                <x-button icon="o-pencil" wire:click="editFeature({{ $flag->id }})" class="btn-ghost btn-sm text-primary" tooltip="Edit Info" />
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endforeach
    @else
        <div class="text-center py-12 border border-dashed border-base-300 rounded-xl">
            <x-icon name="o-exclamation-circle" class="w-12 h-12 text-base-content/30 mx-auto mb-3" />
            <p class="text-base-content/50 text-sm">Tidak ada feature flag yang ditemukan.</p>
        </div>
    @endif

    {{-- Edit Modal --}}
    <x-modal wire:model="editModal" title="Edit Feature Flag" subtitle="Sesuaikan informasi nama dan deskripsi feature flag" box-class="!max-w-md">
        <x-form wire:submit.prevent="saveFeature">
            <x-input wire:model="editingName" label="Nama Fitur" placeholder="Nama Fitur" required />
            <x-input wire:model="editingDescription" label="Deskripsi" placeholder="Deskripsi mengenai fungsi fitur ini..." />

            <x-slot:actions>
                <x-button label="Batal" wire:click="closeModal" type="button" />
                <x-button label="Simpan" class="btn-primary" type="submit" spinner="saveFeature" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
