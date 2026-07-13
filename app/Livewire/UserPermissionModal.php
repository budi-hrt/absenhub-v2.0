<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Mary\Traits\Toast;

class UserPermissionModal extends Component
{
    use Toast;

    public bool $userModal = false;
    public ?int $selectedUserId = null;
    public string $selectedUserName = '';
    public array $selectedUserRoles = [];
    public array $rolePermissionIds = [];
    public array $directPermissionIds = [];
    public string $search = '';

    public function getListeners(): array
    {
        return [
            'openUserPermissionModal' => 'open',
        ];
    }

    public function open(int $userId): void
    {
        $user = User::with('roles.permissions')->findOrFail($userId);

        $this->selectedUserId = $user->id;
        $this->selectedUserName = $user->name;
        $this->selectedUserRoles = $user->roles->pluck('name')->toArray();
        $this->rolePermissionIds = $user->roles->flatMap->permissions->pluck('id')->unique()->values()->toArray();
        $this->directPermissionIds = $user->getDirectPermissions()->pluck('id')->toArray();
        $this->search = '';
        $this->userModal = true;
    }

    public function close(): void
    {
        $this->userModal = false;
        $this->reset(['selectedUserId', 'selectedUserName', 'selectedUserRoles', 'rolePermissionIds', 'directPermissionIds', 'search']);
    }

    public function toggleDirectPermission(int $permissionId): void
    {
        if (in_array($permissionId, $this->rolePermissionIds)) {
            return;
        }

        if (in_array($permissionId, $this->directPermissionIds)) {
            $this->directPermissionIds = array_values(array_diff($this->directPermissionIds, [$permissionId]));
        } else {
            $this->directPermissionIds[] = $permissionId;
        }
    }

    public function syncDirectPermissions(): void
    {
        $user = User::findOrFail($this->selectedUserId);
        $user->syncPermissions($this->directPermissionIds);

        $this->success("Permission untuk user \"{$this->selectedUserName}\" berhasil disimpan.", position: 'toast-top toast-end');
        $this->close();
        $this->dispatch('refreshUsers');
    }

    public function isRolePermission(int $permissionId): bool
    {
        return in_array($permissionId, $this->rolePermissionIds);
    }

    public function isDirectPermission(int $permissionId): bool
    {
        return in_array($permissionId, $this->directPermissionIds);
    }

    public function getGroupPermissionIds(string $group): array
    {
        return Permission::where('group', $group)->pluck('id')->toArray();
    }

    public function getGroupSelectedCount(string $group): int
    {
        $ids = $this->getGroupPermissionIds($group);
        $all = array_unique(array_merge($this->rolePermissionIds, $this->directPermissionIds));
        return count(array_intersect($ids, $all));
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

    public function getRolePermissionCount(): int
    {
        return count($this->rolePermissionIds);
    }

    public function getDirectPermissionCount(): int
    {
        return count($this->directPermissionIds);
    }

    public function render()
    {
        return view('livewire.user-permission-modal', [
            'groupedPermissions' => $this->getGroupedPermissions(),
            'totalCount' => $this->getTotalCount(),
        ]);
    }
}
