<div>
    <x-modal wire:model="permissionModal"
        title="Kelola Permission: {{ $selectedRoleName }}"
        subtitle="Centang permission yang ingin diberikan ke role ini"
        box-class="!w-11/12 !max-w-xl">

        {{-- Header Stats --}}
        <div class="flex items-center justify-between mb-4 p-3 bg-base-200 rounded-xl">
            <div class="flex items-center gap-2">
                <x-icon name="o-shield-check" class="w-5 h-5 text-primary" />
                <span class="text-sm font-medium">
                    <span class="text-primary font-bold">{{ count($selectedPermissions) }}</span>
                    <span class="text-base-content/60">/ {{ $totalCount }} permission dipilih</span>
                </span>
            </div>
            <div class="badge badge-primary badge-lg">
                {{ count($selectedPermissions) }}/{{ $totalCount }}
            </div>
        </div>

        {{-- Search --}}
        <div class="mb-4">
            <x-input icon="o-magnifying-glass" placeholder="Cari permission..." wire:model.live.debounce="search"
                clearable />
        </div>

        {{-- Select All --}}
        <div class="mb-4 flex items-center justify-between p-3 bg-primary/5 border border-primary/20 rounded-xl">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" class="checkbox checkbox-primary checkbox-sm"
                    wire:click="toggleSelectAll"
                    @checked($selectAll) />
                <span class="font-semibold text-sm">Pilih Semua</span>
            </label>
            <span class="text-xs text-base-content/60">
                {{ count($selectedPermissions) }}/{{ $totalCount }} terpilih
            </span>
        </div>

        {{-- Permission Groups --}}
        <div class="space-y-3 max-h-[50vh] overflow-y-auto pr-1">
            @forelse ($groupedPermissions as $group => $permissions)
                @php
                    $groupSelected = $this->getGroupSelectedCount($group);
                    $groupTotal = count($permissions);
                    $isFullySelected = $groupSelected === $groupTotal;
                @endphp

                <div class="border border-base-300 rounded-xl overflow-hidden transition-all hover:border-primary/30">
                    {{-- Group Header --}}
                    <div class="flex items-center justify-between p-3 bg-base-100 cursor-pointer select-none
                        {{ $isFullySelected ? 'bg-primary/5' : '' }}"
                        wire:click="toggleGroup('{{ $group }}')">
                        <div class="flex items-center gap-3">
                            <span class="font-semibold text-sm">{{ $group }}</span>
                            <span class="text-xs text-base-content/50">
                                {{ $groupTotal }} permission
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="badge {{ $isFullySelected ? 'badge-primary' : ($groupSelected > 0 ? 'badge-warning' : 'badge-soft') }} badge-sm">
                                {{ $groupSelected }}/{{ $groupTotal }}
                            </span>
                        </div>
                    </div>

                    {{-- Permission List --}}
                    <div class="border-t border-base-200 p-3 grid grid-cols-1 sm:grid-cols-2 gap-1 bg-base-50">
                        @foreach ($permissions as $permission)
                            <label class="flex items-center gap-2 p-2 rounded-lg cursor-pointer transition-all
                                hover:bg-base-200/50 {{ in_array($permission->id, $selectedPermissions) ? 'bg-primary/5' : '' }}">
                                <input type="checkbox" class="checkbox checkbox-primary checkbox-xs"
                                    wire:click="togglePermission({{ $permission->id }})"
                                    @checked(in_array($permission->id, $selectedPermissions)) />
                                <span class="text-sm">{{ $permission->name }}</span>
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
            <x-button label="Simpan" class="btn-primary" wire:click="syncPermissions" spinner="syncPermissions" />
        </x-slot:actions>
    </x-modal>
</div>
