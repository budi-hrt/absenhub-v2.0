<?php

use App\Models\User;
use Livewire\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use Toast, WithFileUploads;

    use WithPagination;

    protected $listeners = ['refreshUsers' => '$refresh'];

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public bool $drawer = false;

    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    public int $currentPage = 1;
    public int $perPage = 10;
    public bool $userModal = false;
    public ?int $userId = null;
    public bool $showPasswordField = false;

    public $name = '';

    public $email = '';

    public $password = '';

    public $photo = null;

    public ?string $existingPhoto = null;

    public string $role = '';

    public string $filterRole = '';

    public string $filterActive = '';

    public function updatingFilterRole(): void
    {
        $this->resetPage();
    }

    public function updatingFilterActive(): void
    {
        $this->resetPage();
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required|exists:roles,name',
            'photo' => 'nullable|image|max:2048',
        ];

        if ($this->userId) {
            $rules['email'] .= '|unique:users,email,' . $this->userId;
            if ($this->password) {
                $rules['password'] = 'min:6';
            }
        } else {
            $rules['email'] .= '|unique:users';
            $rules['password'] = 'required|min:6';
        }

        return $rules;
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->showPasswordField = false;
        $this->existingPhoto = $user->face_photo;
        $this->role = $user->roles->first()->name ?? '';
        $this->photo = null;
        $this->userModal = true;
    }

    // Clear filters
    public function clear(): void
    {
        $this->reset();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    // Open permission modal
    public function permission(int $id): void
    {
        $this->dispatch('openUserPermissionModal', userId: $id);
    }

    // Delete action
    public function delete($id): void
    {
        // updtae is_active to 0 or delete the user from database
        // User::destroy($id);
        User::where('id', $id)->update(['is_active' => 0]);
        $nama = User::where('id', $id)->value('name');

        $this->success("User #$nama deactivated.", position: 'toast-bottom');
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'no', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'User', 'class' => 'w-64'],
            ['key' => 'email', 'label' => 'E-mail', 'sortable' => false],
            ['key' => 'role', 'label' => 'Role', 'sortable' => false],
            ['key' => 'is_active', 'label' => 'Status', 'class' => 'w-20'],
            //
        ];
    }

    /**
     * For demo purpose, this is a static collection.
     *
     * On real projects you do it with Eloquent collections.
     * Please, refer to maryUI docs to see the eloquent examples.
     */
    public function users(): LengthAwarePaginator
    {
        return User::with('roles')
            ->whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['karyawan', 'super-admin']))
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->filterRole, fn($q) => $q->whereHas('roles', fn($r) => $r->where('name', $this->filterRole)))
            ->when($this->filterActive === 'active', fn($q) => $q->where('is_active', 1))
            ->when($this->filterActive === 'inactive', fn($q) => $q->where('is_active', 0))
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->paginate(10);
        // return User::with('roles')->paginate(10);
    }

    public function with(): array
    {
        $users = $this->users();
        $this->currentPage = $users->currentPage();
        $this->perPage = $users->perPage();
        return [
            'users' => $users,
            'headers' => $this->headers(),
            'roles' => \Spatie\Permission\Models\Role::whereNotIn('name', ['karyawan', 'super-admin'])->pluck('name'),
        ];
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->photo) {
            // Hapus foto lama jika ada
            if ($this->existingPhoto && Storage::disk('public')->exists($this->existingPhoto)) {
                Storage::disk('public')->delete($this->existingPhoto);
            }
            $data['face_photo'] = $this->photo->store('photos', 'public');
        }

        if ($this->userId) {
            $user = User::findOrFail($this->userId);
            $user->update($data);
            $user->syncRoles($this->role);
            $this->success('User updated successfully.', position: 'toast-bottom');
        } else {
            $user = User::create($data);
            $user->syncRoles($this->role);
            $this->success('User added successfully.', position: 'toast-bottom');
        }

        $this->resetForm();
        $this->userModal = false;
    }

    public function createUser(): void
    {
        $this->resetForm();
        $this->userModal = true;
    }

    public function closeModal(): void
    {
        $this->userModal = false;
        $this->resetForm();
    }

    public function openDrawer(): void
    {
        $this->drawer = true;
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'email', 'password', 'userId', 'showPasswordField', 'photo', 'existingPhoto', 'role']);
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Data Users" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
        </x-slot:middle>

        <x-slot:actions>
            <x-button label="Tambah User" wire:click="createUser" class="btn-primary" icon="o-plus"
                spinner />
            <x-button label="Filters" wire:click="openDrawer" responsive icon="o-funnel" spinner />
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$users" :sort-by="$sortBy" with-pagination>
            // nomor 1 dan seterusnya

            @scope('cell_no', $row)
            <span
                class="text-sm text-gray-500">{{ $loop->iteration + ($this->currentPage - 1) * $this->perPage }}</span>
            @endscope

            //tambahkan image di sebelah kiri nama user, jika user tidak
            memiliki avatar, gunakan avatar default

            @scope('cell_name', $row)
            <div class="flex items-center space-x-3">
                <x-avatar
                    :image="$row->face_photo
                            ? Storage::url($row->face_photo)
                            : 'https://i.pravatar.cc/150?img=9'"
                    :title="$row->name"
                    :subtitle="$row->email"
                    class="!w-10" />
            </div>
            @endscope
            @scope('cell_role', $row)
            <x-badge :value="$row->roles->first()->name" :class="$row->roles->first()->name === 'admin' ? 'badge-error badge-soft' : 'badge-warning badge-soft'" />
            @endscope
            @scope('cell_is_active', $row)
            <x-badge :value="$row->is_active ? 'Active' : 'Inactive'" :class="$row->is_active
                    ? 'badge-success badge-soft'
                    : 'badge-error badge-soft'" />
            @endscope

            @scope('actions', $row)
            <div class="flex gap-1">
                <x-button icon="o-pencil" wire:click="edit({{ $row->id }})"
                    wire:target="edit({{ $row->id }})" spinner
                    class="btn-ghost btn-sm text-primary" />
                <x-button icon="o-shield-check" wire:click="permission({{ $row->id }})"
                    wire:target="permission({{ $row->id }})" spinner
                    class="btn-ghost btn-sm text-warning" />
                <x-button icon="o-trash" wire:click="delete({{ $row->id }})"
                    wire:confirm="Are you sure? " spinner class="btn-ghost btn-sm text-error" />
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button
        class="lg:w-1/3">
        <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass"
            @keydown.enter="$wire.drawer = false" />

        <div class="fieldset py-0 mt-5">
            <legend class="fieldset-legend mb-0.5">Role</legend>
            <label class="select w-full">
                <select wire:model.live="filterRole" style="color: #1f2937; background-color: #ffffff;">
                    <option value="">Semua Role</option>
                    @foreach ($roles as $roleName)
                    <option value="{{ $roleName }}">{{ $roleName }}</option>
                    @endforeach
                </select>
            </label>
        </div>

        <div class="fieldset py-0 mt-5">
            <legend class="fieldset-legend mb-0.5">Status</legend>
            <label class="select w-full">
                <select wire:model.live="filterActive" style="color: #1f2937; background-color: #ffffff;">
                    <option value="">Semua Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </label>
        </div>

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-success"
                @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>

    <!-- Modal User -->
    <x-modal wire:model="userModal" title="{{ $userId ? 'Edit User' : 'Tambah User' }}"
        subtitle="{{ $userId ? 'Mengubah data user' : 'Formulir untuk menambahkan user baru' }}">
        <x-form wire:submit="save">
            {{-- Foto Upload dengan Preview --}}
            <div wire:key="photo-{{ $userId ?? 'new' }}" x-data="{
                previewUrl: '{{ $existingPhoto ? Storage::url($existingPhoto) : '' }}',
                handleFile(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.previewUrl = URL.createObjectURL(file);
                    }
                }
            }">
                <label class="label mb-1 text-sm font-medium">Foto Profil</label>
                <div class="flex items-center gap-4">
                    {{-- Preview Circle --}}
                    <div class="relative shrink-0">
                        <div class="w-20 h-20 rounded-full overflow-hidden ring-2 ring-base-300 bg-base-200 flex items-center justify-center">
                            <template x-if="previewUrl">
                                <img :src="previewUrl" class="w-full h-full object-cover" alt="Preview" />
                            </template>
                            <template x-if="!previewUrl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-base-content/30" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z" />
                                </svg>
                            </template>
                        </div>
                        @if ($photo || $existingPhoto)
                        <div class="absolute -top-1 -right-1">
                            <span class="badge badge-success badge-xs">✓</span>
                        </div>
                        @endif
                    </div>

                    {{-- Upload Button Area --}}
                    <div class="flex-1">
                        <label class="flex flex-col items-center justify-center w-full h-20 border-2 border-dashed border-base-300 rounded-xl cursor-pointer hover:border-primary hover:bg-primary/5 transition-all duration-200">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-base-content/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs text-base-content/50">Klik untuk pilih foto</span>
                                <span class="text-xs text-base-content/30">JPG, PNG, max 2MB</span>
                            </div>
                            <input type="file" class="hidden" accept="image/*"
                                wire:model="photo"
                                x-on:change="handleFile($event)" />
                        </label>
                        @error('photo')
                        <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <x-input label="Nama" icon="o-user" placeholder="Nama User" wire:model="name" />
            <x-input label="Email" icon="o-envelope" placeholder="The e-mail" wire:model="email" />
            <div class="fieldset py-0">
                <legend class="fieldset-legend mb-0.5">Role</legend>
                <label class="select w-full">
                    <select wire:model="role" style="color: #1f2937; background-color: #ffffff;">
                        <option value="">Pilih Role</option>
                        @foreach ($roles as $roleName)
                        <option value="{{ $roleName }}">{{ $roleName }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
            @if ($userId && !$showPasswordField)
            <x-button label="Ganti Password" wire:click="$set('showPasswordField', true)"
                class="btn-ghost btn-sm" icon="o-key" />
            @endif
            @if (!$userId || $showPasswordField)
            <x-password label="Password" wire:model="password" right />
            @endif

            {{-- Notice we are using now the `actions` slot from `x-form`, not from modal --}}
            <x-slot:actions>
                <x-button label="Batal" wire:click="closeModal" />
                <x-button label="Simpan" class="btn-success" type="save" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    <livewire:user-permission-modal />
</div>