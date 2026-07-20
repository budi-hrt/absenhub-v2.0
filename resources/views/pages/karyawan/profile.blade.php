<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Profil Saya')] class extends Component {
    public function mount()
    {
        if (!auth()->user()->hasRole('karyawan')) {
            $this->redirect('/');
        }
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }

    public function with(): array
    {
        $user = auth()->user();
        // Load relasi jabatan dan status agar bisa ditampilkan
        $karyawan = $user->karyawan()->with(['jabatan', 'status'])->first();

        return [
            'user' => $user,
            'karyawan' => $karyawan,
        ];
    }
}; ?>

<div>
    {{-- Header --}}
    <x-header title="Profil Saya" separator progress-indicator />

    {{-- Profile Card --}}
    <div class="relative bg-base-100 rounded-[32px] overflow-hidden border border-base-300 shadow-sm mb-6 md:mb-8">
        {{-- Background Banner --}}
        <div class="h-32 md:h-48 bg-gradient-to-r from-primary to-secondary relative">
            <div class="absolute inset-0 bg-black/10"></div>
        </div>

        {{-- Avatar & Basic Info --}}
        <div class="px-6 md:px-10 pb-8 relative">
            <div class="flex flex-col md:flex-row md:items-end gap-4 md:gap-6 -mt-16 md:-mt-20 mb-6">
                <div class="relative w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-base-100 bg-base-200 overflow-hidden shrink-0 shadow-md">
                    @if ($user->face_photo)
                        <img src="{{ Storage::url($user->face_photo) }}" alt="Profile Photo" class="w-full h-full object-cover" />
                    @elseif ($karyawan && $karyawan->foto_karyawan)
                        <img src="{{ Storage::url($karyawan->foto_karyawan) }}" alt="Profile Photo" class="w-full h-full object-cover" />
                    @else
                        <img src="https://i.pravatar.cc/150?img=9" alt="Default Avatar" class="w-full h-full object-cover" />
                    @endif
                </div>
                
                <div class="flex-1 pb-2">
                    <h2 class="text-2xl md:text-3xl font-bold text-base-content">{{ $karyawan?->nama_karyawan ?? $user->name }}</h2>
                    <p class="text-primary font-medium text-sm md:text-base mt-1">{{ $karyawan?->jabatan?->nama_jabatan ?? 'Karyawan' }}</p>
                </div>

                <div class="pb-2">
                    <div class="badge {{ $karyawan?->is_active ? 'badge-success' : 'badge-error' }} badge-lg font-semibold">
                        {{ $karyawan?->is_active ? 'Aktif' : 'Non-Aktif' }}
                    </div>
                </div>
            </div>

            {{-- Info Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mt-6">
                {{-- NIK --}}
                <div class="p-4 rounded-2xl bg-base-200/50 border border-base-200">
                    <div class="flex items-center gap-3 mb-1">
                        <x-icon name="o-identification" class="w-5 h-5 text-base-content/50" />
                        <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">NIK</p>
                    </div>
                    <p class="text-base font-medium pl-8">{{ $karyawan?->nik ?? '-' }}</p>
                </div>

                {{-- Status Karyawan --}}
                <div class="p-4 rounded-2xl bg-base-200/50 border border-base-200">
                    <div class="flex items-center gap-3 mb-1">
                        <x-icon name="o-briefcase" class="w-5 h-5 text-base-content/50" />
                        <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Status Pekerjaan</p>
                    </div>
                    <p class="text-base font-medium pl-8">{{ $karyawan?->status?->nama_status ?? '-' }}</p>
                </div>

                {{-- Email --}}
                <div class="p-4 rounded-2xl bg-base-200/50 border border-base-200">
                    <div class="flex items-center gap-3 mb-1">
                        <x-icon name="o-envelope" class="w-5 h-5 text-base-content/50" />
                        <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Email</p>
                    </div>
                    <p class="text-base font-medium pl-8">{{ $karyawan?->email_karyawan ?? $user->email }}</p>
                </div>

                {{-- Telepon --}}
                <div class="p-4 rounded-2xl bg-base-200/50 border border-base-200">
                    <div class="flex items-center gap-3 mb-1">
                        <x-icon name="o-phone" class="w-5 h-5 text-base-content/50" />
                        <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Nomor Telepon</p>
                    </div>
                    <p class="text-base font-medium pl-8">{{ $karyawan?->telp_karyawan ?? '-' }}</p>
                </div>

                {{-- Tanggal Lahir --}}
                <div class="p-4 rounded-2xl bg-base-200/50 border border-base-200">
                    <div class="flex items-center gap-3 mb-1">
                        <x-icon name="o-calendar-days" class="w-5 h-5 text-base-content/50" />
                        <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Tanggal Lahir</p>
                    </div>
                    <p class="text-base font-medium pl-8">
                        @if($karyawan && $karyawan->tanggal_lahir)
                            {{ Carbon::parse($karyawan->tanggal_lahir)->locale('id')->isoFormat('D MMMM Y') }}
                            <span class="text-xs text-base-content/50 ml-1">({{ Carbon::parse($karyawan->tanggal_lahir)->age }} tahun)</span>
                        @else
                            -
                        @endif
                    </p>
                </div>

                {{-- Tanggal Masuk --}}
                <div class="p-4 rounded-2xl bg-base-200/50 border border-base-200">
                    <div class="flex items-center gap-3 mb-1">
                        <x-icon name="o-building-office-2" class="w-5 h-5 text-base-content/50" />
                        <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Bergabung Sejak</p>
                    </div>
                    <p class="text-base font-medium pl-8">
                        @if($karyawan && $karyawan->tanggal_masuk)
                            {{ Carbon::parse($karyawan->tanggal_masuk)->locale('id')->isoFormat('D MMMM Y') }}
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="mb-24 flex flex-col gap-3">
        <form wire:submit="logout">
            <x-button type="submit" label="Keluar (Logout)" icon="o-arrow-right-on-rectangle" class="btn-error w-full shadow-sm text-error-content font-bold" />
        </form>
    </div>
</div>
