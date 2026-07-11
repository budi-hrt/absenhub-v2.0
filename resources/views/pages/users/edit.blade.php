<?php

use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads, Toast;

    public int $userId;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public bool $showPasswordField = false;

    public $photo = null;
    public ?string $existingPhoto = null;
    public ?array $faceDescriptor = null;
    public bool $hasFaceDescriptor = false;
    public bool $saving = false;

    public function mount(int $user): void
    {
        $this->userId = $user;
        $u = User::findOrFail($user);

        $this->name = $u->name;
        $this->email = $u->email;
        $this->existingPhoto = $u->face_photo;
        $this->hasFaceDescriptor = !empty($u->face_descriptor);
    }

    public function saveFaceDescriptor(string $json): void
    {
        $this->faceDescriptor = json_decode($json, true);
        $this->hasFaceDescriptor = true;
    }

    public function handleFaceError(string $message): void
    {
        $this->faceDescriptor = null;
        $this->hasFaceDescriptor = false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$this->userId}",
            'password' => 'nullable|min:6',
            'photo' => 'nullable|image|max:2048',
        ];
    }

    public function validationMessages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.min' => 'Password minimal 6 karakter',
            'photo.image' => 'File harus berupa gambar',
            'photo.max' => 'Ukuran foto maksimal 2MB',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->saving = true;

        try {
            $user = User::findOrFail($this->userId);

            $data = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            if ($this->photo) {
                if ($this->existingPhoto && Storage::disk('public')->exists($this->existingPhoto)) {
                    Storage::disk('public')->delete($this->existingPhoto);
                }
                $data['face_photo'] = $this->photo->store('photos', 'public');
            }

            if ($this->faceDescriptor) {
                $data['face_descriptor'] = $this->faceDescriptor;
            }

            $user->update($data);

            $this->success('User berhasil diperbarui!', position: 'toast-bottom');
            $this->redirect('/users-karyawan');
        } catch (\Exception $e) {
            $this->saving = false;
            $this->error('Gagal menyimpan: ' . $e->getMessage(), position: 'toast-bottom');
        }
    }

    public function cancel(): void
    {
        $this->redirect('/users-karyawan');
    }
}; ?>

<div x-data="{
    modelsReady: false,
    previewUrl: '{{ $existingPhoto ? Storage::url($existingPhoto) : '' }}',
    status: '{{ $hasFaceDescriptor ? 'success' : 'waiting' }}',
    async handlePhoto(e) {
        const file = e.target.files[0];
        if (!file) return;

        this.previewUrl = URL.createObjectURL(file);
        this.status = 'processing';

        if (!this.modelsReady) {
            this.status = 'waiting';
            alert('Model deteksi wajah masih dimuat, coba lagi sebentar.');
            return;
        }

        try {
            const descriptor = await getDescriptorFromBlob(file);
            await $wire.call('saveFaceDescriptor', JSON.stringify(descriptor));
            this.status = 'success';
        } catch (err) {
            await $wire.call('handleFaceError', err.message);
            this.status = 'error';
        }
    }
}" x-init="initFaceModels().then(() => modelsReady = true)">
    <x-header title="Edit User" separator />

    <x-card class="mt-4">
        <x-form wire:submit="save">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- KOLOM KIRI: Upload Foto + Face Detection --}}
                <div class="space-y-6">
                    <h3 class="font-bold text-lg flex items-center gap-2">
                        <x-icon name="o-camera" class="w-5 h-5" />
                        Foto & Scan Wajah
                        <span x-show="!modelsReady" class="loading loading-spinner loading-xs"></span>
                    </h3>

                    {{-- Preview Foto --}}
                    <div class="flex justify-center">
                        <div class="w-56 h-56 rounded-full border-4 border-dashed border-base-300 overflow-hidden bg-base-200 flex items-center justify-center">
                            <template x-if="previewUrl">
                                <img :src="previewUrl" class="w-full h-full object-cover" />
                            </template>
                            <template x-if="!previewUrl">
                                <div class="text-center">
                                    <x-icon name="o-user" class="w-16 h-16 text-gray-400 mx-auto" />
                                    <p class="text-sm text-gray-400 mt-2">Upload Foto</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Upload Input --}}
                    <div class="flex justify-center">
                        <input type="file" accept="image/*"
                               wire:model="photo"
                               x-on:change="handlePhoto($event)"
                               class="file-input file-input-bordered file-input-primary w-full max-w-xs" />
                    </div>

                    {{-- Status --}}
                    <div class="flex items-center justify-center gap-2 text-sm"
                         x-show="status === 'processing'">
                        <span class="loading loading-spinner loading-sm"></span>
                        <span>Memproses deteksi wajah...</span>
                    </div>

                    <div class="flex items-center justify-center gap-2 text-sm text-success"
                         x-show="status === 'success'">
                        <x-icon name="o-check-circle" class="w-5 h-5" />
                        <span>Wajah terdeteksi!</span>
                    </div>

                    <div class="flex items-center justify-center gap-2 text-sm text-error"
                         x-show="status === 'error'">
                        <x-icon name="o-x-circle" class="w-5 h-5" />
                        <span>Wajah tidak terdeteksi. Gunakan foto yang jelas.</span>
                    </div>
                </div>

                {{-- KOLOM KANAN: Form Data --}}
                <div class="space-y-6">
                    <h3 class="font-bold text-lg flex items-center gap-2">
                        <x-icon name="o-document-text" class="w-5 h-5" />
                        Data User
                    </h3>

                    {{-- Nama --}}
                    <x-input label="Nama" icon="o-user" placeholder="Nama lengkap"
                             wire:model="name" />

                    {{-- Email --}}
                    <x-input label="Email" icon="o-envelope" placeholder="email@domain.com"
                             wire:model="email" type="email" />

                    {{-- Toggle Password --}}
                    <div class="flex items-center gap-2">
                        <x-toggle wire:model.live="showPasswordField" label="Ganti Password" />
                    </div>

                    {{-- Password --}}
                    @if ($showPasswordField)
                        <x-input label="Password Baru" icon="o-key" placeholder="Minimal 6 karakter"
                                 wire:model="password" type="password" />
                    @endif
                </div>
            </div>

            <div class="divider"></div>

            {{-- Actions --}}
            <div class="flex justify-end gap-2">
                <x-button label="Batal" icon="o-x-mark" wire:click="cancel" />
                <x-button label="Simpan" icon="o-check" class="btn-success" type="submit" spinner="saving" />
            </div>
        </x-form>
    </x-card>
</div>
