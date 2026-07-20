<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>

    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'emerald');
        document.addEventListener('livewire:navigated', () => {
            document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'emerald');
        });
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-sans antialiased bg-base-200">

    {{-- NAVBAR mobile only --}}
    <x-nav sticky class="lg:hidden">
        <x-slot:brand>
            <x-app-brand />
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    {{-- MAIN --}}
    <x-main>
        {{-- SIDEBAR --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100">

            {{-- BRAND --}}
            <x-app-brand class="px-5 pt-4" />

            {{-- MENU --}}
            <livewire:sidebar-menu />

            {{-- DARK MODE TOGGLE --}}
            <x-menu>
                <li x-data="{ theme: localStorage.getItem('theme') || 'emerald' }"
                    x-init="$watch('theme', val => { localStorage.setItem('theme', val); document.documentElement.setAttribute('data-theme', val) })">
                    <a href="#" @click.prevent="theme = theme === 'emerald' ? 'dark' : 'emerald'">
                        <span x-show="theme === 'emerald'"><x-icon name="o-moon" class="w-5 h-5" /></span>
                        <span x-show="theme !== 'emerald'"><x-icon name="o-sun" class="w-5 h-5" /></span>
                        <span class="mary-hideable" x-text="theme === 'emerald' ? 'Dark Mode' : 'Light Mode'"></span>
                    </a>
                </li>
            </x-menu>
        </x-slot:sidebar>

        {{-- The `$slot` goes here --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    {{-- TOAST area --}}
    <x-toast />

    {{-- MOBILE DOCK NAVIGATION --}}
    <div class="dock lg:hidden bg-base-100 border-t border-base-200 z-50">
        <a href="{{ auth()->user()?->hasRole('karyawan') ? route('dashboard') : '/' }}" wire:navigate class="{{ request()->routeIs('dashboard') || request()->is('/') ? 'dock-active text-primary' : 'text-base-content/60' }}">
            <svg class="size-5 md:size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt"><polyline points="1 11 12 2 23 11" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="2"></polyline><path d="m5,13v7c0,1.105.895,2,2,2h10c1.105,0,2-.895,2-2v-7" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></path><line x1="12" y1="22" x2="12" y2="18" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></line></g></svg>
            <span class="dock-label text-[10px] md:text-xs">Home</span>
        </a>
        
        <a href="{{ auth()->user()?->hasRole('karyawan') ? route('karyawan.riwayat') : '#' }}" wire:navigate class="{{ request()->routeIs('karyawan.riwayat') ? 'dock-active text-primary' : 'text-base-content/60 hover:text-primary transition-colors' }}">
            <svg class="size-5 md:size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt"><polyline points="3 14 9 14 9 17 15 17 15 14 21 14" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="2"></polyline><rect x="3" y="3" width="18" height="18" rx="2" ry="2" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></rect></g></svg>
            <span class="dock-label text-[10px] md:text-xs">Riwayat</span>
        </a>
        
        <a href="{{ auth()->user()?->hasRole('karyawan') ? route('karyawan.profile') : '#' }}" wire:navigate class="{{ request()->routeIs('karyawan.profile') ? 'dock-active text-primary' : 'text-base-content/60 hover:text-primary transition-colors' }}">
            <svg class="size-5 md:size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt"><circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></circle><path d="m22,13.25v-2.5l-2.318-.966c-.167-.581-.395-1.135-.682-1.654l.954-2.318-1.768-1.768-2.318.954c-.518-.287-1.073-.515-1.654-.682l-.966-2.318h-2.5l-.966,2.318c-.581.167-1.135.395-1.654.682l-2.318-.954-1.768,1.768.954,2.318c-.287.518-.515,1.073-.682,1.654l-2.318.966v2.5l2.318.966c.167.581.395,1.135.682,1.654l-.954,2.318,1.768,1.768,2.318-.954c.518.287,1.073.515,1.654.682l.966,2.318h2.5l.966-2.318c.581-.167,1.135-.395,1.654-.682l2.318.954,1.768-1.768-.954-2.318c.287-.518.515-1.073.682-1.654l2.318-.966Z" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></path></g></svg>
            <span class="dock-label text-[10px] md:text-xs">Profile</span>
        </a>
    </div>
</body>

</html>