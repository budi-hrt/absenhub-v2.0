<?php

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Mary\Traits\Toast;

new class extends Component {

    use Toast;

    public string $search = '';
    public bool $addModal = false;
    public bool $editModal = false;
    public bool $editGroupModal = false;
    public ?int $permissionId = null;
    public string $permissionName = '';
    public string $permissionGroup = '';
    public string $newGroupName = '';
    public string $editGroupNameTarget = '';

    public function groupedPermissions(): array
    {
        $query = Permission::query()->orderBy('group')->orderBy('name');

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%")
                  ->orWhere('group', 'like', "%{$this->search}%");
        }

        $permissions = $query->get();
        $grouped = [];

        foreach ($permissions as $permission) {
            $group = $permission->group ?? 'Lainnya';
            $grouped[$group][] = $permission;
        }

        return $grouped;
    }

    public function getExistingGroups(): array
    {
        return Permission::distinct()->pluck('group')->filter()->values()->toArray();
    }

    public function addPermission(): void
    {
        $this->resetForm();
        $this->addModal = true;
    }

    public function editPermission(int $id): void
    {
        $permission = Permission::findOrFail($id);
        $this->permissionId = $permission->id;
        $this->permissionName = $permission->name;
        $this->permissionGroup = $this->getPermissionGroup($permission);
        $this->editModal = true;
    }

    public function editGroup(string $groupName): void
    {
        $this->editGroupNameTarget = $groupName;
        $this->newGroupName = $groupName;
        $this->editGroupModal = true;
    }

    public function savePermission(): void
    {
        $this->validate([
            'permissionName' => 'required|string|max:255',
            'permissionGroup' => 'required|string|max:255',
        ]);

        $data = [
            'name' => $this->permissionName,
            'group' => $this->permissionGroup,
            'guard_name' => 'web',
        ];

        if ($this->permissionId) {
            $permission = Permission::findOrFail($this->permissionId);
            $permission->update($data);
            $this->success('Permission berhasil diupdate.', position: 'toast-top toast-end');
        } else {
            $exists = Permission::where('name', $this->permissionName)->exists();
            if ($exists) {
                $this->error('Permission dengan nama tersebut sudah ada.', position: 'toast-top toast-end');
                return;
            }
            Permission::create($data);
            $roleSuperAdmin = Role::where('name', 'super-admin')->first();
            if ($roleSuperAdmin) {
                $roleSuperAdmin->givePermissionTo($this->permissionName);
            }
            $this->success('Permission berhasil ditambahkan dan ditambahkan ke role super-admin.', position: 'toast-top toast-end');
        }

        $this->closeModal();
    }

    public function saveGroup(): void
    {
        $this->validate([
            'newGroupName' => 'required|string|max:255',
        ]);

        $oldName = $this->editGroupNameTarget;
        $newName = $this->newGroupName;

        if ($oldName === $newName) {
            $this->closeModal();
            return;
        }

        $exists = Permission::where('group', $newName)->exists();
        if ($exists) {
            $this->error('Group dengan nama tersebut sudah ada.', position: 'toast-top toast-end');
            return;
        }

        Permission::where('group', $oldName)->update(['group' => $newName]);

        $this->success("Group \"$oldName\" berhasil direname ke \"$newName\".", position: 'toast-top toast-end');
        $this->closeModal();
    }

    public function closeModal(): void
    {
        $this->addModal = false;
        $this->editModal = false;
        $this->editGroupModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset(['permissionId', 'permissionName', 'permissionGroup', 'newGroupName', 'editGroupNameTarget']);
    }

    private function getPermissionGroup(Permission $permission): string
    {
        return $permission->group ?? '';
    }

    public function render()
    {
        return view('pages::permissions.index', [
            'groupedPermissions' => $this->groupedPermissions(),
            'existingGroups' => $this->getExistingGroups(),
        ]);
    }
};
?>

<div>
    <x-header title="Permissions" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-magnifying-glass" placeholder="Cari permission..." wire:model.live.debounce="search"
                clearable />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Tambah Permission" icon="o-plus" class="btn-primary" wire:click="addPermission" spinner />
        </x-slot:actions>
    </x-header>

    <div class="space-y-2">
        @forelse ($groupedPermissions as $group => $permissions)
            <div class="border border-base-300 rounded-xl overflow-hidden transition-all hover:border-primary/20"
                x-data="{ open: false }">
                {{-- Group Header --}}
                <div class="flex items-center justify-between p-4 bg-base-100 cursor-pointer select-none">
                    <div class="flex items-center gap-3" @click="open = !open">
                        <span class="text-base-content/40 transition-transform duration-200" x-text="open ? '▼' : '▶'"></span>
                        <span class="font-semibold">{{ $group }}</span>
                        <span class="text-sm text-base-content/50">{{ count($permissions) }} permission</span>
                    </div>
                    <button type="button" class="btn btn-ghost btn-sm text-warning"
                        wire:click="editGroup('{{ addslashes($group) }}')">
                        <x-icon name="o-pencil" class="w-4 h-4" />
                    </button>
                </div>

                {{-- Permission Grid --}}
                <div x-show="open" x-transition.opacity.duration.200ms>
                    <div class="border-t border-base-200 p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 bg-base-50">
                        @foreach ($permissions as $permission)
                            <div class="flex items-center justify-between p-2.5 rounded-lg bg-base-100 border border-base-200 hover:border-primary/30 transition-all">
                                <span class="text-sm">{{ $permission->name }}</span>
                                <button type="button" class="btn btn-ghost btn-xs text-warning"
                                    wire:click="editPermission({{ $permission->id }})">
                                    <x-icon name="o-pencil" class="w-3.5 h-3.5" />
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 text-base-content/40">
                <x-icon name="o-shield-check" class="w-16 h-16 mx-auto mb-4" />
                <p class="text-lg font-medium">Tidak ada permission ditemukan</p>
                <p class="text-sm mt-1">Klik "Tambah Permission" untuk membuat baru</p>
            </div>
        @endforelse
    </div>

    <!-- Modal Tambah/Edit Permission -->
    <x-modal wire:model="addModal" title="Tambah Permission" subtitle="Buat permission baru">
        <x-form wire:submit.prevent="savePermission">
            <x-input wire:model="permissionName" label="Nama Permission" icon="o-key" placeholder="contoh: lihat-laporan" />
            <div class="fieldset py-0">
                <legend class="fieldset-legend mb-0.5">Group</legend>
                <input type="text" class="input input-bordered w-full" wire:model="permissionGroup"
                    placeholder="Ketik atau pilih group" list="group-list" />
                <datalist id="group-list">
                    @foreach ($existingGroups as $group)
                        <option value="{{ $group }}">
                    @endforeach
                </datalist>
            </div>
            <x-slot:actions>
                <x-button label="Batal" wire:click="closeModal" type="button" />
                <x-button label="Simpan" class="btn-primary" type="submit" spinner="savePermission" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    <x-modal wire:model="editModal" title="Edit Permission" subtitle="Ubah nama atau group permission">
        <x-form wire:submit.prevent="savePermission">
            <x-input wire:model="permissionName" label="Nama Permission" icon="o-key" placeholder="contoh: lihat-laporan" />
            <div class="fieldset py-0">
                <legend class="fieldset-legend mb-0.5">Group</legend>
                <input type="text" class="input input-bordered w-full" wire:model="permissionGroup"
                    placeholder="Ketik atau pilih group" list="group-list-edit" />
                <datalist id="group-list-edit">
                    @foreach ($existingGroups as $group)
                        <option value="{{ $group }}">
                    @endforeach
                </datalist>
            </div>
            <x-slot:actions>
                <x-button label="Batal" wire:click="closeModal" type="button" />
                <x-button label="Simpan" class="btn-primary" type="submit" spinner="savePermission" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    <!-- Modal Edit Group -->
    <x-modal wire:model="editGroupModal" title="Rename Group" subtitle="Ubah nama group permission">
        <x-form wire:submit.prevent="saveGroup">
            <div class="mb-2 text-sm text-base-content/60">
                Group: <span class="font-semibold text-base-content">{{ $editGroupNameTarget }}</span>
            </div>
            <x-input wire:model="newGroupName" label="Nama Group Baru" icon="o-folder" placeholder="Nama group baru" />
            <x-slot:actions>
                <x-button label="Batal" wire:click="closeModal" type="button" />
                <x-button label="Simpan" class="btn-primary" type="submit" spinner="saveGroup" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
