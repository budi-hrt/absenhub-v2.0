<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <x-menu activate-by-route>

        {{-- User --}}
        @if ($user = auth()->user())
        <x-menu-separator />

        <x-list-item :item="$user" value="name" sub-value="email" no-separator
            no-hover class="-mx-2 !-my-2 rounded">
            <x-slot:actions>
                <x-button icon="o-power" class="btn-circle btn-ghost btn-xs"
                    tooltip-left="logoff"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();" />
                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                    class="hidden">
                    @csrf
                </form>
            </x-slot:actions>
        </x-list-item>

        <x-menu-separator />
        @endif

        {{-- Dashboard --}}
        @hasrole('karyawan')
        <x-menu-item title="Dashboard" icon="o-home" link="/dashboard" />
        @else
        <x-menu-item title="Dashboard" icon="o-home" link="/" />
        @endhasrole

        {{-- Manajemen Users: admin/super-admin|operator|manager only --}}
        @hasanyrole('admin|super-admin|operator|manager')
        <x-menu-sub title="Manajemen Users" icon="o-cog-6-tooth">
            @haspermission('lihat-admin')
            <x-menu-item title="Data Users" icon="o-user" icon-classes="text-primary"
                link="/users" :exact="true" />
            @endhaspermission
            @haspermission('lihat-user-karyawan')
            <x-menu-item title="Users Karyawan" icon="o-users" icon-classes="text-warning"
                link="/users-karyawan" />
            @endhaspermission
        </x-menu-sub>
        @endhasanyrole

        {{-- Manajemen Karyawan --}}
        @hasanyrole('admin|super-admin|operator|manager')
        <x-menu-item title="Data Karyawan" icon="o-users" icon-classes="text-success"
            link="/karyawan" :exact="true" />
        @endhasanyrole


        <x-menu-separator />

        <li x-data="{ theme: localStorage.getItem('theme') || 'emerald' }"
            x-init="$watch('theme', val => { localStorage.setItem('theme', val); document.documentElement.setAttribute('data-theme', val) })">
            <a href="#" @click.prevent="theme = theme === 'emerald' ? 'dark' : 'emerald'">
                <span x-show="theme === 'emerald'"><x-icon name="o-moon" class="w-5 h-5" /></span>
                <span x-show="theme !== 'emerald'"><x-icon name="o-sun" class="w-5 h-5" /></span>
                <span x-text="theme === 'emerald' ? 'Dark Mode' : 'Light Mode'"></span>
            </a>
        </li>
    </x-menu>
</div>