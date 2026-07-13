<div>
    <x-modal wire:model="userModal"
        title="Permission: {{ $selectedUserName }}"
        subtitle="Role: {{ implode(', ', $selectedUserRoles) }}"
        class="!w-11/12 !max-w-3xl">

        {{-- Header Stats --}}
        <div class="flex items-center gap-3 mb-4 p-3 bg-base-200 rounded-xl flex-wrap">
            <div class="flex items-center gap-2">
                <x-icon name="o-shield-check" class="w-5 h-5 text-primary" />
                <span class="text-sm font-medium">
                    <span class="font-bold">{{ $totalCount }}</span>
                    <span class="text-base-content/60">total permission</span>
                </span>
            </div>
            <div class="divider divider-horizontal"></div>
            <div class="flex items-center gap-2">
                <span class="badge badge-info badge-sm">Role: {{ $this->getRolePermissionCount() }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge badge-success badge-sm">Direct: {{ $this->getDirectPermissionCount() }}</span>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex items-center gap-4 mb-4 text-xs">
            <div class="flex items-center gap-1.5">
                <span class="badge badge-info badge-xs">Role</span>
                <span class="text-base-content/50">Dari role, tidak bisa diubah</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="badge badge-success badge-xs">Direct</span>
                <span class="text-base-content/50">Permission langsung ke user</span>
            </div>
        </div>

        {{-- Search --}}
        <div class="mb-4">
            <x-input icon="o-magnifying-glass" placeholder="Cari permission..." wire:model.live.debounce="search"
                clearable />
        </div>

        {{-- Permission Groups --}}
        <div class="space-y-3 max-h-[50vh] overflow-y-auto pr-1">
            @forelse ($groupedPermissions as $group => $permissions)
                @php
                    $groupSelected = $this->getGroupSelectedCount($group);
                    $groupTotal = count($permissions);
                @endphp

                <div class="border border-base-300 rounded-xl overflow-hidden transition-all hover:border-primary/30">
                    {{-- Group Header --}}
                    <div class="flex items-center justify-between p-3 bg-base-100">
                        <div class="flex items-center gap-3">
                            <div>
                                <span class="font-semibold text-sm">{{ $group }}</span>
                                <div class="text-xs text-base-content/50 mt-0.5">
                                    {{ $groupTotal }} permission
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="badge {{ $groupSelected === $groupTotal ? 'badge-primary' : ($groupSelected > 0 ? 'badge-warning' : 'badge-soft') }} badge-sm">
                                {{ $groupSelected }}/{{ $groupTotal }}
                            </span>
                        </div>
                    </div>

                    {{-- Permission List --}}
                    <div class="border-t border-base-200 p-3 grid grid-cols-1 sm:grid-cols-2 gap-1 bg-base-50">
                        @foreach ($permissions as $permission)
                            @php
                                $isRole = $this->isRolePermission($permission->id);
                                $isDirect = $this->isDirectPermission($permission->id);
                                $isChecked = $isRole || $isDirect;
                            @endphp

                            <label class="flex items-center gap-2 p-2 rounded-lg transition-all
                                {{ $isChecked ? 'bg-primary/5' : '' }}
                                {{ $isRole ? 'opacity-70' : 'cursor-pointer hover:bg-base-200/50' }}">

                                @if ($isRole)
                                    {{-- Role permission: read-only checkbox --}}
                                    <input type="checkbox" class="checkbox checkbox-info checkbox-xs"
                                        checked disabled />
                                    <span class="text-sm">{{ $permission->name }}</span>
                                    <span class="badge badge-info badge-xs ml-auto">Role</span>
                                @elseif ($isDirect)
                                    {{-- Direct permission: editable, currently checked --}}
                                    <input type="checkbox" class="checkbox checkbox-success checkbox-xs"
                                        wire:click="toggleDirectPermission({{ $permission->id }})"
                                        checked />
                                    <span class="text-sm">{{ $permission->name }}</span>
                                    <span class="badge badge-success badge-xs ml-auto">Direct</span>
                                @else
                                    {{-- No permission: editable, unchecked --}}
                                    <input type="checkbox" class="checkbox checkbox-xs"
                                        wire:click="toggleDirectPermission({{ $permission->id }})" />
                                    <span class="text-sm">{{ $permission->name }}</span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-base-content/40">
                    <x-icon name="o-magnifying-glass" class="w-12 h-12 mx-auto mb-3" />
                    <p class="text-sm">Tidak ada permission ditemukan</p>
                </div>
            @endforelse
        </div>

        {{-- Actions --}}
        <x-slot:actions>
            <x-button label="Batal" wire:click="close" />
            <x-button label="Simpan" class="btn-primary" wire:click="syncDirectPermissions" spinner="syncDirectPermissions" />
        </x-slot:actions>
    </x-modal>
</div>
