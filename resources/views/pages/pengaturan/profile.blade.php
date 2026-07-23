<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new #[Layout('layouts.app')] #[Title('Pengaturan Profil')] class extends Component {
    use Toast, WithFileUploads;

    public string $name = '';
    public string $email = '';
    public string $bio = '';

    public $photo;

    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();

        $this->name = $user->name;
        $this->email = $user->email;
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
            'bio' => 'nullable|string|max:500',
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $this->success('Data profil berhasil diperbarui.', position: 'toast-top toast-end');
    }

    public function updatedPhoto(): void
    {
        $this->validate([
            'photo' => 'image|max:2048',
        ]);

        $user = auth()->user();
        $path = $this->photo->store('face-photos', 'public');

        $user->update(['face_photo' => $path]);

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

        return [
            'user' => $user,
        ];
    }
}; ?>

<div>
    <x-header title="Pengaturan Profil" subtitle="Kelola data pribadi, foto profil, dan kata sandi akun Anda" separator progress-indicator />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        {{-- Left Column: Profile Overview --}}
        <aside class="lg:col-span-4 flex flex-col items-center text-center p-6 bg-base-100 rounded-2xl border border-base-300 shadow-sm relative">
            <div class="relative group">
                <div class="w-36 h-36 rounded-full border-4 border-base-200 overflow-hidden bg-base-200 relative shadow-md">
                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover" />
                    @elseif ($user->face_photo)
                        <img src="{{ Storage::url($user->face_photo) }}" alt="{{ $user->name }}" class="w-full h-full object-cover" />
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-primary/10 text-primary font-bold text-3xl">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @endif
                </div>

                <label for="profile-photo-input" class="absolute bottom-1 right-1 bg-primary text-primary-content p-2.5 rounded-full shadow-lg hover:bg-primary/90 transition-all cursor-pointer active:scale-95 flex items-center justify-center">
                    <x-icon name="o-camera" class="w-5 h-5" />
                </label>
                <input type="file" id="profile-photo-input" wire:model="photo" class="hidden" accept="image/*">
            </div>

            <div wire:loading wire:target="photo" class="mt-2 text-xs font-semibold text-primary animate-pulse">
                Mengunggah foto...
            </div>

            <h2 class="mt-4 font-bold text-xl md:text-2xl text-base-content">{{ $user->name }}</h2>
            <p class="text-sm text-base-content/70 mt-0.5">{{ $user->email }}</p>

            <div class="mt-3 flex flex-wrap justify-center gap-1.5">
                <span class="px-3 py-1 bg-primary/10 text-primary text-xs font-semibold rounded-full uppercase tracking-wider">
                    {{ $user->getRoleNames()->first() ?? 'ADMIN' }}
                </span>
            </div>
        </aside>

        {{-- Right Column: Forms --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Card: Perbarui Data Pribadi --}}
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
                            <input type="text" wire:model="name" class="input input-bordered input-sm w-full text-sm font-medium focus:input-primary" />
                            @error('name') <span class="text-error text-[11px] mt-0.5">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-semibold text-base-content/80">Alamat Email</label>
                            <input type="email" wire:model="email" class="input input-bordered input-sm w-full text-sm font-medium focus:input-primary" />
                            @error('email') <span class="text-error text-[11px] mt-0.5">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col gap-1 md:col-span-2">
                            <label class="text-xs font-semibold text-base-content/80">Bio / Catatan Singkat</label>
                            <textarea wire:model="bio" rows="3" placeholder="Tuliskan catatan singkat tentang Anda..." class="textarea textarea-bordered text-sm font-medium focus:textarea-primary resize-none"></textarea>
                        </div>

                        <div class="md:col-span-2 flex justify-end gap-2 mt-2">
                            <button type="submit" class="btn btn-primary btn-sm gap-1.5 shadow-sm">
                                <x-icon name="o-check" class="w-4 h-4" />
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Card: Ubah Kata Sandi --}}
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
                            <input type="password" wire:model="current_password" placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" class="input input-bordered input-sm w-full text-sm font-medium focus:input-primary" />
                            @error('current_password') <span class="text-error text-[11px] mt-0.5">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-semibold text-base-content/80">Kata Sandi Baru</label>
                                <input type="password" wire:model="new_password" placeholder="Min. 8 karakter" class="input input-bordered input-sm w-full text-sm font-medium focus:input-primary" />
                                @error('new_password') <span class="text-error text-[11px] mt-0.5">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-semibold text-base-content/80">Konfirmasi Kata Sandi Baru</label>
                                <input type="password" wire:model="new_password_confirmation" placeholder="Ulangi kata sandi" class="input input-bordered input-sm w-full text-sm font-medium focus:input-primary" />
                            </div>
                        </div>

                        <div class="flex justify-end mt-2">
                            <button type="submit" class="btn btn-neutral btn-sm gap-1.5 shadow-sm">
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
