<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak - {{ config('app.name') }}</title>

    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'emerald');
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 0.5; }
            80%, 100% { transform: scale(2.2); opacity: 0; }
        }
        .pulse-ring::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            border: 2px solid currentColor;
            animation: pulse-ring 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }
    </style>
</head>

<body class="min-h-screen font-sans antialiased bg-base-200 flex items-center justify-center p-4">

    <div class="max-w-lg w-full text-center">

        {{-- Main Card --}}
        <div class="bg-base-100 shadow-2xl rounded-3xl p-10 md:p-14 border border-base-300/50 relative overflow-hidden">

            {{-- Decorative background blobs --}}
            <div class="absolute -top-16 -right-16 w-48 h-48 bg-error/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>

            {{-- Shield icon with pulse ring --}}
            <div class="relative inline-flex items-center justify-center w-24 h-24 bg-error/10 text-error rounded-[1.25rem] mb-8 shadow-sm animate-float">
                <span class="pulse-ring relative inline-flex items-center justify-center w-full h-full rounded-[1.25rem]">
                    <x-icon name="o-shield-exclamation" class="w-12 h-12" />
                </span>
            </div>

            {{-- Error Code --}}
            <h1 class="text-8xl font-black tracking-tighter text-base-content/10 leading-none select-none mb-2">403</h1>

            {{-- Title --}}
            <h2 class="text-2xl font-bold text-base-content -mt-4 mb-3">Akses Ditolak</h2>

            {{-- Divider --}}
            <div class="divider w-16 mx-auto my-4 before:bg-base-300 after:bg-base-300"></div>

            {{-- Message --}}
            <p class="text-base-content/60 text-sm mb-8 leading-relaxed max-w-sm mx-auto">
                {{ $exception?->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.' }}
            </p>

            {{-- Action Buttons --}}
            @php
                $homeUrl = auth()->check() && auth()->user()->hasRole('karyawan') ? '/dashboard' : '/';
            @endphp
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button onclick="window.history.back()" class="btn btn-outline btn-base-content/50 gap-2 flex-1">
                    <x-icon name="o-arrow-uturn-left" class="w-4 h-4" />
                    Kembali
                </button>
                <a href="{{ $homeUrl }}" class="btn btn-primary gap-2 flex-1 shadow-md hover:shadow-lg transition-shadow">
                    <x-icon name="o-home" class="w-4 h-4" />
                    Ke Beranda
                </a>
            </div>
        </div>

        {{-- Footer --}}
        <p class="text-xs text-base-content/30 mt-6">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </p>
    </div>
</body>

</html>
