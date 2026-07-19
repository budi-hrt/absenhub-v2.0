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

        {{-- Master Data --}}
        @hasanyrole('admin|super-admin|operator|manager')
        <x-menu-sub title="Master Data" icon="o-circle-stack">
            <x-menu-item title="Jabatan" icon="o-briefcase" :exact="true"
                link="/master/jabatan" />
            <x-menu-item title="Status Kerja" icon="o-check-badge" :exact="true"
                link="/master/status-kerja" />
            <x-menu-item title="Masa Kontrak" icon="o-clock" :exact="true"
                link="/master/masa-kontrak" />
            <x-menu-item title="Penandatangan" icon="o-pencil-square" :exact="true"
                link="/master/penandatangan" />
        </x-menu-sub>
        @endhasanyrole

        {{-- Manajemen Karyawan --}}
        @hasanyrole('admin|super-admin|operator|manager')
        <x-menu-item title="Data Karyawan" icon="o-users" icon-classes="text-success"
            link="/karyawan" :exact="true" />
        @endhasanyrole

        {{-- Manajemen Absensi --}}
        @hasanyrole('admin|super-admin|operator|manager')
        <x-menu-sub title="Manajemen Absensi" icon="o-calendar-days">
            <x-menu-item title="Kelola Absensi" icon="o-pencil-square" icon-classes="text-primary"
                link="/absen/kelola" :exact="true" />
            <x-menu-item title="Lihat Absensi" icon="o-eye" icon-classes="text-success"
                link="/absen/lihat" :exact="true" />
            <x-menu-item title="Detail Harian" icon="o-document-text" icon-classes="text-info"
                link="/absen/detail-harian" :exact="true" />
            <x-menu-item title="Rekap Bulanan" icon="o-table-cells" icon-classes="text-accent"
                link="/absen/rekap-bulanan" :exact="true" />
            <x-menu-item title="Rekap Tahunan" icon="o-calendar" icon-classes="text-secondary"
                link="/absen/rekap-tahunan" :exact="true" />
            <x-menu-item title="Laporan Bulanan" icon="o-chart-bar" icon-classes="text-error"
                link="/absen/laporan-bulanan" :exact="true" />
            <x-menu-item title="Pengaturan Jam Kerja" icon="o-clock" icon-classes="text-warning"
                link="/pengaturan/absen" :exact="true" />
            <x-menu-item title="Pengaturan Lokasi" icon="o-map-pin" icon-classes="text-primary"
                link="/pengaturan/lokasi" :exact="true" />
        </x-menu-sub>
        @endhasanyrole

        {{-- Super Admin: super-admin only --}}
        @hasrole('super-admin')
        <x-menu-sub title="Super Admin" icon="o-shield-check">
            <x-menu-item title="Roles" icon="o-user-group" icon-classes="text-primary"
                link="/roles" :exact="true" />
            <x-menu-item title="Permissions" icon="o-key" icon-classes="text-warning"
                link="/permissions" :exact="true" />
            <x-menu-item title="Feature Flags" icon="o-flag" icon-classes="text-success"
                link="/feature-flags" :exact="true" />
        </x-menu-sub>
        @endhasrole

    </x-menu>
</div>