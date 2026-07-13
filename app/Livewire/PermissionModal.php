<?php

namespace App\Livewire;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Mary\Traits\Toast;

class PermissionModal extends Component
{
    use Toast;

    public bool $permissionModal = false;
    public ?int $selectedRoleId = null;
    public string $selectedRoleName = '';
    public array $selectedPermissions = [];
    public string $search = '';
    public bool $selectAll = false;

    public function open(int $roleId): void
    {
        $role = Role::findOrFail($roleId);
        $this->selectedRoleId = $role->id;
        $this->selectedRoleName = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
        $this->search = '';
        $this->selectAll = $this->isAllSelected();
        $this->permissionModal = true;
    }

    public function close(): void
    {
        $this->permissionModal = false;
        $this->reset(['selectedRoleId', 'selectedRoleName', 'selectedPermissions', 'search', 'selectAll']);
    }

    public function togglePermission(int $permissionId): void
    {
        if (in_array($permissionId, $this->selectedPermissions)) {
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, [$permissionId]));
        } else {
            $this->selectedPermissions[] = $permissionId;
        }
        $this->selectAll = $this->isAllSelected();
    }

    public function toggleGroup(string $group): void
    {
        $groupPermissionIds = $this->getGroupPermissionIds($group);
        $allSelected = count(array_intersect($groupPermissionIds, $this->selectedPermissions)) === count($groupPermissionIds);

        if ($allSelected) {
            $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, $groupPermissionIds));
        } else {
            $this->selectedPermissions = array_unique(array_merge($this->selectedPermissions, $groupPermissionIds));
        }
        $this->selectAll = $this->isAllSelected();
    }

    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selectedPermissions = [];
        } else {
            $this->selectedPermissions = Permission::pluck('id')->toArray();
        }
        $this->selectAll = !$this->selectAll;
    }

    public function syncPermissions(): void
    {
        $role = Role::findOrFail($this->selectedRoleId);
        $role->syncPermissions($this->selectedPermissions);
        $this->success("Permission untuk role \"{$this->selectedRoleName}\" berhasil disimpan.", position: 'toast-top toast-end');
        $this->close();
        $this->dispatch('refreshRoles');
    }

    public function getGroupPermissionIds(string $group): array
    {
        return Permission::where('group', $group)->pluck('id')->toArray();
    }

    public function isAllSelected(): bool
    {
        return Permission::count() === count($this->selectedPermissions);
    }

    public function isGroupFullySelected(string $group): bool
    {
        $ids = $this->getGroupPermissionIds($group);
        return count($ids) > 0 && count(array_intersect($ids, $this->selectedPermissions)) === count($ids);
    }

    public function isGroupPartiallySelected(string $group): bool
    {
        $ids = $this->getGroupPermissionIds($group);
        $intersect = array_intersect($ids, $this->selectedPermissions);
        return count($intersect) > 0 && count($intersect) < count($ids);
    }

    public function getGroupSelectedCount(string $group): int
    {
        $ids = $this->getGroupPermissionIds($group);
        return count(array_intersect($ids, $this->selectedPermissions));
    }

    public function getGroupTotalCount(string $group): int
    {
        return Permission::where('group', $group)->count();
    }

    public function getGroupedPermissions(): array
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

    public function getTotalCount(): int
    {
        return Permission::count();
    }

    public function getListeners(): array
    {
        return [
            'openPermissionModal' => 'open',
        ];
    }

    public function render()
    {
        return view('livewire.permission-modal', [
            'groupedPermissions' => $this->getGroupedPermissions(),
            'totalCount' => $this->getTotalCount(),
        ]);
    }
}
