<?php

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new #[Layout('layouts.app')] #[Title('Profil Saya')] class extends Component {
    use Toast, WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $telp_karyawan = '';
    public string $bio = '';

    public $photo;

    // Password fields
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();
        $karyawan = $user->karyawan;

        $this->name = $karyawan?->nama_karyawan ?? $user->name;
        $this->email = $karyawan?->email_karyawan ?? $user->email;
        $this->telp_karyawan = $karyawan?->telp_karyawan ?? '';
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }

    public function updateProfile(): void
    {
        $user = auth()->user();

        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'telp_karyawan' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        if ($user->karyawan) {
            $user->karyawan->update([
                'nama_karyawan' => $this->name,
                'email_karyawan' => $this->email,
                'telp_karyawan' => $this->telp_karyawan,
            ]);
        }

        $this->success('Data profil berhasil diperbarui.', position: 'toast-top toast-end');
    }

    public function updatedPhoto(): void
    {
        $this->validate([
            'photo' => 'image|max:2048',
        ]);

        $user = auth()->user();
        $path = $this->photo->store('karyawan-foto', 'public');

        $user->update(['face_photo' => $path]);

        if ($user->karyawan) {
            $user->karyawan->update(['foto_karyawan' => $path]);
        }

        $this->success('Foto profil berhasil diperbarui.', position: 'toast-top toast-end');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => 'required|string|current_password',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'new_password.min' => 'Kata sandi baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        $this->success('Kata sandi berhasil diperbarui.', position: 'toast-top toast-end');
    }

    public function with(): array
    {
        $user = auth()->user();
        $karyawan = $user->karyawan()->with(['jabatan', 'status'])->first();

        return [
            'user' => $user,
            'karyawan' => $karyawan,
        ];
    }
}; ?>

<div>
    @if (auth()->user()->hasRole('karyawan'))
        {{-- Profile Karyawan View --}}
        <x-header title="Profil Saya" separator progress-indicator />

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
                            @if ($karyawan && $karyawan->tanggal_lahir)
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
                            @if ($karyawan && $karyawan->tanggal_masuk)
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
    @else
        {{-- Profile Admin View (Stitch Design) --}}
        <div class="space-y-6">
            <x-header title="Pengaturan Profil Admin" subtitle="Kelola data pribadi, foto profil, dan kata sandi akun Anda" separator progress-indicator />

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Left Column: Profile Overview -->
                <aside class="lg:col-span-4 flex flex-col items-center text-center p-6 bg-base-100 rounded-2xl border border-base-300 shadow-sm relative">
                    <div class="relative group">
                        <div class="w-36 h-36 rounded-full border-4 border-base-200 overflow-hidden bg-base-200 relative shadow-md">
                            @if ($photo)
                                <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover" />
                            @elseif ($user->face_photo)
                                <img src="{{ Storage::url($user->face_photo) }}" alt="{{ $user->name }}" class="w-full h-full object-cover" />
                            @elseif ($karyawan && $karyawan->foto_karyawan)
                                <img src="{{ Storage::url($karyawan->foto_karyawan) }}" alt="{{ $user->name }}" class="w-full h-full object-cover" />
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-primary/10 text-primary font-bold text-2xl">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>

                        <!-- Camera upload trigger -->
                        <label for="profile-photo-input" class="absolute bottom-1 right-1 bg-primary text-primary-content p-2.5 rounded-full shadow-lg hover:bg-primary/90 transition-all cursor-pointer active:scale-95 flex items-center justify-center">
                            <x-icon name="o-camera" class="w-5 h-5" />
                        </label>
                        <input type="file" id="profile-photo-input" wire:model="photo" class="hidden" accept="image/*">
                    </div>

                    <div wire:loading wire:target="photo" class="mt-2 text-xs font-semibold text-primary animate-pulse">
                        Mengunggah foto...
                    </div>

                    <h2 class="mt-4 font-bold text-xl md:text-2xl text-base-content">{{ $user->name }}</h2>
                    <p class="text-xs md:text-sm text-base-content/70 mt-0.5">
                        {{ $user->email }}
                    </p>

                    <div class="mt-3 flex flex-wrap justify-center gap-1.5">
                        <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-semibold rounded-full uppercase tracking-wider">
                            {{ $user->getRoleNames()->first() ?? 'ADMIN' }}
                        </span>
                    </div>
                </aside>

                <!-- Right Column: Forms -->
                <div class="lg:col-span-8 space-y-6">
                    <!-- Card 1: Data Pribadi -->
                    <div class="bg-base-100 rounded-2xl border border-base-300 shadow-sm overflow-hidden">
                        <div class="h-1 bg-primary w-full"></div>
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-6">
                                <x-icon name="o-user" class="w-5 h-5 text-primary" />
                                <h3 class="text-lg font-bold text-base-content">Perbarui Data Pribadi</h3>
                            </div>

                            <form wire:submit="updateProfile" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-base-content/80">Nama Lengkap</label>
                                    <input type="text" wire:model="name" class="input input-bordered input-sm w-full text-xs font-medium focus:input-primary" />
                                    @error('name') <span class="text-error text-[11px] mt-0.5">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-base-content/80">Alamat Email</label>
                                    <input type="email" wire:model="email" class="input input-bordered input-sm w-full text-xs font-medium focus:input-primary" />
                                    @error('email') <span class="text-error text-[11px] mt-0.5">{{ $message }}</span> @enderror
                                </div>

                                <div class="flex flex-col gap-1 md:col-span-2">
                                    <label class="text-xs font-semibold text-base-content/80">Bio / Catatan Singkat</label>
                                    <textarea wire:model="bio" rows="3" placeholder="Tuliskan catatan singkat tentang Anda..." class="textarea textarea-bordered text-xs font-medium focus:textarea-primary resize-none"></textarea>
                                </div>

                                <div class="md:col-span-2 flex justify-end gap-2 mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm text-xs gap-1.5 shadow-sm">
                                        <x-icon name="o-check" class="w-4 h-4" />
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Card 2: Ubah Kata Sandi -->
                    <div class="bg-base-100 rounded-2xl border border-base-300 shadow-sm overflow-hidden">
                        <div class="h-1 bg-primary w-full"></div>
                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-6">
                                <x-icon name="o-lock-closed" class="w-5 h-5 text-primary" />
                                <h3 class="text-lg font-bold text-base-content">Ubah Kata Sandi</h3>
                            </div>

                            <form wire:submit="updatePassword" class="space-y-4">
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-semibold text-base-content/80">Kata Sandi Saat Ini</label>
                                    <input type="password" wire:model="current_password" placeholder="••••••••" class="input input-bordered input-sm w-full text-xs font-medium focus:input-primary" />
                                    @error('current_password') <span class="text-error text-[11px] mt-0.5">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="flex flex-col gap-1">
                                        <label class="text-xs font-semibold text-base-content/80">Kata Sandi Baru</label>
                                        <input type="password" wire:model="new_password" placeholder="Min. 8 karakter" class="input input-bordered input-sm w-full text-xs font-medium focus:input-primary" />
                                        @error('new_password') <span class="text-error text-[11px] mt-0.5">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="flex flex-col gap-1">
                                        <label class="text-xs font-semibold text-base-content/80">Konfirmasi Kata Sandi Baru</label>
                                        <input type="password" wire:model="new_password_confirmation" placeholder="Ulangi kata sandi" class="input input-bordered input-sm w-full text-xs font-medium focus:input-primary" />
                                    </div>
                                </div>

                                <div class="flex justify-end mt-2">
                                    <button type="submit" class="btn btn-neutral btn-sm text-xs gap-1.5 shadow-sm">
                                        <x-icon name="o-key" class="w-4 h-4" />
                                        Perbarui Kata Sandi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
