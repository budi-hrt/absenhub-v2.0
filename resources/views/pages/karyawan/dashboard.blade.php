<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Dashboard')] class extends Component {
    public function mount()
    {
        if (!auth()->user()->hasRole('karyawan')) {
            $this->redirect('/');
        }
    }
}; ?>

<div>
    <x-header title="Dashboard Karyawan" separator />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-card>
            <div class="flex items-center gap-3">
                <x-icon name="o-user" class="w-8 h-8 text-primary shrink-0" />
                <div>
                    <div class="text-2xl font-bold">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-base-content/50">Nama Lengkap</div>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-3">
                <x-icon name="o-briefcase" class="w-8 h-8 text-warning shrink-0" />
                <div>
                    <div class="text-2xl font-bold">{{ auth()->user()->karyawan?->jabatan?->nama_jabatan ?? '-' }}</div>
                    <div class="text-xs text-base-content/50">Jabatan</div>
                </div>
            </div>
        </x-card>
        <x-card>
            <div class="flex items-center gap-3">
                <x-icon name="o-check-badge" class="w-8 h-8 text-success shrink-0" />
                <div>
                    <div class="text-2xl font-bold">{{ auth()->user()->is_active ? 'Aktif' : 'Nonaktif' }}</div>
                    <div class="text-xs text-base-content/50">Status</div>
                </div>
            </div>
        </x-card>
    </div>

    <x-card>
        <div class="text-center py-6 text-base-content/50">
            <x-icon name="o-home" class="w-12 h-12 mx-auto mb-3 text-base-content/30" />
            <p class="text-sm">Selamat datang di AbsenHub, <strong>{{ auth()->user()->name }}</strong></p>
            <p class="text-xs mt-1">Sistem Informasi Absensi Digital</p>
        </div>
    </x-card>
</div>
