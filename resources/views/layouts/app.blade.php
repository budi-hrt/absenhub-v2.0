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
</body>

</html>