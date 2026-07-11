<?php

use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use App\Models\Karyawan;

new class extends Component
{
    // Mary\Traits\
    use Toast;

    // Livewire\Component
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterActive(): void
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

    public $karyawanId = '';

    public $email = '';

    public $password = '';

    public $photo = null;

    public ?string $existingPhoto = null;

    public string $role = '';

    public string $filterRole = '';

    public string $filterActive = '';


    // Table headers
    public function headers(): array
    {
        return [
            // ['key' => 'index', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'User', 'class' => 'w-64'],
            ['key' => 'email', 'label' => 'E-mail', 'sortable' => false],
            ['key' => 'scan_wajah', 'label' => 'Scan Wajah', 'sortable' => false],
            ['key' => 'is_active', 'label' => 'Status', 'class' => 'w-20'],
            ['key' => 'action', 'label' => '', 'sortable' => false],
            //
        ];
    }




    public function users(): LengthAwarePaginator
    {
        return User::with('karyawan:user_id,nama_karyawan,jabatan_id')
            ->with('karyawan.jabatan:id,nama_jabatan')
            ->select('id', 'name', 'email', 'is_active', 'face_photo')
            ->selectRaw('CASE WHEN face_descriptor IS NOT NULL THEN 1 ELSE 0 END as scan_wajah')
            ->whereHas('roles', fn($q) => $q->where('name', ['karyawan']))
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            // ->when($this->filterRole, fn($q) => $q->whereHas('roles', fn($r) => $r->where('name', $this->filterRole)))
            ->when($this->filterActive === 'active', fn($q) => $q->where('is_active', 1))
            ->when($this->filterActive === 'inactive', fn($q) => $q->where('is_active', 0))
            ->orderBy($this->sortBy['column'], $this->sortBy['direction'])
            ->paginate(10);
        // return User::with('roles')->paginate(10);
    }



    public function deactivate(int $id): void
    {
        User::where('id', $id)->update(['is_active' => false]);
        $this->success('User berhasil dinonaktifkan.', position: 'toast-bottom');
    }

    public function activate(int $id): void
    {
        User::where('id', $id)->update(['is_active' => true]);
        $this->success('User berhasil diaktifkan kembali.', position: 'toast-bottom');
    }

    public function with(): array
    {
        $users = $this->users();
        $this->currentPage = $users->currentPage();
        $this->perPage = $users->perPage();
        return [
            'users' => $users,
            'headers' => $this->headers(),

        ];
    }
};
?>

<div x-data="{ confirmId: null, confirmName: '', confirmAction: 'deactivate' }" x-ref="page">
    <!-- HEADER -->
    <x-header title="Users Karyawan" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
        </x-slot:middle>

        <x-slot:actions>
            <x-button label="Tambah User" link="{{ route('users.create') }}" class="btn-primary" icon="o-plus"
                spinner />
            <x-button label="Filters" wire:click="openDrawer" responsive icon="o-funnel" spinner />
        </x-slot:actions>
    </x-header>

    <!-- Table -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$users" :sort-by="$sortBy" with-pagination>
            @scope('cell_name', $row)
            <div class="flex items-center space-x-3">
                <x-avatar
                    :image="$row->face_photo
                            ? Storage::url($row->face_photo)
                            : 'https://i.pravatar.cc/150?img=9'"
                    :title="$row->name"
                    :subtitle="$row->karyawan ?->jabatan ?->nama_jabatan ?? 'Tidak ada jabatan'"
                    class="!w-10" />
            </div>
            @endscope

            @scope('cell_scan_wajah', $row)
            @if ($row->scan_wajah)
            <x-icon name="o-check-badge" class="w-5 h-5 text-success" />
            @else
            <x-icon name="o-x-mark" class="w-5 h-5 text-gray-400" />
            @endif
            @endscope

            @scope('cell_is_active', $row)
            <x-badge :value="$row->is_active ? 'Active' : 'Inactive'" :class="$row->is_active
                    ? 'badge-success badge-soft'
                    : 'badge-error badge-soft'" />
            @endscope
            @scope('cell_action', $row)
            <div class="flex gap-1">
                <x-button icon="o-pencil" class="btn-ghost btn-xs" tooltip="Edit" link="/users/{{ $row->id }}/edit" />
                @if ($row->is_active)
                <x-button icon="o-no-symbol" class="btn-ghost btn-xs text-error" tooltip="Nonaktifkan"
                    x-on:click="confirmId = {{ $row->id }}; confirmName = '{{ $row->name }}'; confirmAction = 'deactivate'; $refs.confirmModal.showModal()" />
                @else
                <x-button icon="o-check" class="btn-ghost btn-xs text-success" tooltip="Aktifkan"
                    x-on:click="confirmId = {{ $row->id }}; confirmName = '{{ $row->name }}'; confirmAction = 'activate'; $refs.confirmModal.showModal()" />
                @endif
            </div>
            @endscope
        </x-table>

        <dialog x-ref="confirmModal" class="modal">
            <div class="modal-box">
                <h3 class="font-bold text-lg" x-text="confirmAction === 'deactivate' ? 'Nonaktifkan User' : 'Aktifkan User'"></h3>
                <p class="py-4">
                    <span x-text="confirmAction === 'deactivate'
                        ? 'Yakin ingin menonaktifkan'
                        : 'Yakin ingin mengaktifkan kembali'"></span>
                    <strong x-text="confirmName"></strong>?
                </p>
                <div class="modal-action">
                    <x-button label="Batal" @click="$refs.confirmModal.close()" />
                    <x-button label="Nonaktifkan" class="btn-error" icon="o-no-symbol"
                        x-show="confirmAction === 'deactivate'"
                        x-on:click="$wire.deactivate(confirmId); $refs.confirmModal.close()" />
                    <x-button label="Aktifkan" class="btn-success" icon="o-check"
                        x-show="confirmAction === 'activate'"
                        x-on:click="$wire.activate(confirmId); $refs.confirmModal.close()" />
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    </x-card>
</div>