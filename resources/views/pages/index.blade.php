<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Dashboard')] class extends Component {
    public function mount()
    {
        if (auth()->user()->hasRole('karyawan')) {
            $this->redirect('/dashboard');
        }
    }
}; ?>

<div>
    <x-header title="Dashboard" separator />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-card>
            <div class="flex items-center gap-3">
                <x-icon name="o-users" class="w-8 h-8 text-primary shrink-0" />
                <div>
                    <div class="text-2xl font-bold">{{ \App\Models\User::count() }}</div>
                    <div class="text-xs text-base-content/50">Total Users</div>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-3">
                <x-icon name="o-shield-check" class="w-8 h-8 text-error shrink-0" />
                <div>
                    <div class="text-2xl font-bold">{{ \App\Models\User::role('admin')->count() }}</div>
                    <div class="text-xs text-base-content/50">Admin</div>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-3">
                <x-icon name="o-user-group" class="w-8 h-8 text-warning shrink-0" />
                <div>
                    <div class="text-2xl font-bold">{{ \App\Models\User::role('manager')->count() }}</div>
                    <div class="text-xs text-base-content/50">Manager</div>
                </div>
            </div>
        </x-card>
    </div>

    <x-card>
        <div class="text-center py-6 text-base-content/50">
            <p class="text-sm">Selamat datang di AbsenHub</p>
        </div>
    </x-card>

</div>
