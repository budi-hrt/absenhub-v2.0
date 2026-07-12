<?php


use Livewire\Component;
use \Spatie\Permission\Models\Role;
use Mary\Traits\Toast;

new class extends Component {

    use Toast;



    public string $roleName = '';
    public bool $roleAddModal = false;
    public ?int $roleId = null;


    public function addRole(): void
    {
        $this->roleAddModal = true;
        $this->resetForm();
    }
    public function closeModal(): void
    {
        $this->roleAddModal = false;
        $this->resetForm();
    }

    public function roles()
    {
        $roles = Role::whereNotIn('name', ['super-admin', 'karyawan'])->get();
        return $roles;
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#'],
            ['key' => 'name', 'label' => 'Nama Role'],
            ['key' => 'guard_name', 'label' => 'Guard Name'],
            // <-- nested attributes 
        ];
    }

    public function with(): array
    {
        return [
            'roles' => $this->roles(),
            'headers' => $this->headers(),
        ];
    }

    public function edit(int $id): void
    {
        $role = Role::findOrFail($id);
        $this->roleId = $role->id;
        $this->roleName = $role->name;
        $this->roleAddModal = true;
    }

    public function saveRole(): void
    {
        $this->validate([
            'roleName' => 'required|string|max:255',
        ]);

        if ($this->roleId) {
            $role = Role::findOrFail($this->roleId);
            $role->update(['name' => $this->roleName]);
            $this->success('Role updated successfully.', position: 'toast-top toast-end');
        } else {
            Role::create(['name' => $this->roleName]);
            $this->success('Role created successfully.', position: 'toast-top toast-end');
        }

        $this->closeModal();
    }

    public function resetForm(): void
    {
        $this->reset(['roleName', 'roleId']);
    }
};

?>

<div>
    <x-header title="Roles" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input icon="o-bolt" placeholder="Search..." />
        </x-slot:middle>
        <x-slot:actions>
            <x-button icon="o-funnel" class="btn btn-soft btn-accent" />
            <x-button icon="o-plus" class="btn-primary" wire:click="addRole" spinner />
        </x-slot:actions>
    </x-header>

    <x-card shadow>
        <x-table :headers="$headers" :rows="$roles">
            @scope('actions', $row)
            <div class="flex gap-1">
                <x-button icon="o-pencil" wire:click="edit({{ $row->id }})" class="btn btn-ghost btn-sm text-success" spinner />
                <x-button icon="o-shield-exclamation" wire:click="permission({{ $row->id }})" class="btn btn-ghost btn-sm text-warning" />
            </div>
            @endscope
        </x-table>

    </x-card>


    <!-- Modal tambah -->
    <x-modal wire:model="roleAddModal" title="{{ $roleId ? 'Edit Role' : 'Tambah Role' }}" subtitle="{{ $roleId ? 'Edit role yang sudah ada' : 'Tambah role baru' }}">
        <x-form wire:submit.prevent="saveRole">
            <x-input wire:model="roleName" label="Nama Role" icon="o-user" placeholder="Nama Role" />


            {{-- Notice we are using now the `actions` slot from `x-form`, not from modal --}}
            <x-slot:actions>
                <x-button label="Batal" wire:click="closeModal" type="button" />
                <x-button label="Simpan" class="btn-primary" type="submit" spinner="saveRole" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>