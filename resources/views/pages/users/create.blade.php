<?php

use App\Models\User;
use App\Models\Karyawan;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Hash;

new class extends Component {
    use WithFileUploads, Toast;

    public string $karyawanSearch = '';
    public array $karyawanResults = [];
    public ?int $karyawanId = null;
    public string $selectedNama = '';
    public string $selectedJabatan = '';
    public string $selectedEmail = '';

    public string $email = '';
    public string $password = '12345678';

    public $photo = null;
    public ?array $faceDescriptor = null;
    public bool $saving = false;

    public function updatedKaryawanSearch(): void
    {
        if (strlen($this->karyawanSearch) < 2) {
            $this->karyawanResults = [];
            return;
        }

        $this->karyawanResults = Karyawan::where('is_active', true)
            ->whereNull('user_id')
            ->where('nama_karyawan', 'like', "%{$this->karyawanSearch}%")
            ->with('jabatan:id,nama_jabatan')
            ->limit(10)
            ->get()
            ->map(fn($k) => [
                'id' => $k->id,
                'nama_karyawan' => $k->nama_karyawan,
                'jabatan' => $k->jabatan?->nama_jabatan ?? '-',
                'email' => $k->email_karyawan,
            ])
            ->toArray();
    }

    public function selectKaryawan(int $id): void
    {
        $karyawan = Karyawan::with('jabatan:id,nama_jabatan')->findOrFail($id);

        $this->karyawanId = $karyawan->id;
        $this->selectedNama = $karyawan->nama_karyawan;
        $this->selectedJabatan = $karyawan->jabatan?->nama_jabatan ?? '-';
        $this->selectedEmail = $karyawan->email_karyawan ?? '';
        $this->email = $karyawan->email_karyawan ?? '';
        $this->karyawanSearch = $karyawan->nama_karyawan;
        $this->karyawanResults = [];
    }

    public function clearKaryawan(): void
    {
        $this->karyawanId = null;
        $this->selectedNama = '';
        $this->selectedJabatan = '';
        $this->selectedEmail = '';
        $this->email = '';
        $this->karyawanSearch = '';
        $this->karyawanResults = [];
    }

    public function saveFaceDescriptor(string $json): void
    {
        $this->faceDescriptor = json_decode($json, true);
    }

    public function handleFaceError(string $message): void
    {
        $this->faceDescriptor = null;
    }

    public function rules(): array
    {
        return [
            'karyawanId' => 'required|exists:karyawans,id',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'photo' => 'required|image|max:2048',
            'faceDescriptor' => 'required|array',
        ];
    }

    public function validationMessages(): array
    {
        return [
            'karyawanId.required' => 'Pilih karyawan terlebih dahulu',
            'karyawanId.exists' => 'Karyawan tidak ditemukan',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'photo.required' => 'Foto wajib diupload',
            'photo.image' => 'File harus berupa gambar',
            'photo.max' => 'Ukuran foto maksimal 2MB',
            'faceDescriptor.required' => 'Wajah tidak terdeteksi di foto',
            'faceDescriptor.array' => 'Data face descriptor tidak valid',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->saving = true;

        try {
            $photoPath = $this->photo->store('photos', 'public');

            $user = User::create([
                'name' => $this->selectedNama,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'face_photo' => $photoPath,
                'face_descriptor' => $this->faceDescriptor,
                'is_active' => true,
            ]);

            Karyawan::where('id', $this->karyawanId)->update(['user_id' => $user->id]);
            $user->assignRole('karyawan');

            $this->success('User berhasil dibuat!', position: 'toast-bottom');
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
    previewUrl: null,
    status: 'waiting',
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
    <x-header title="Tambah User Karyawan" separator />

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

                    {{-- Autocomplete Karyawan --}}
                    <div class="relative" x-data="{ showDropdown: false }">
                        <x-input label="Karyawan" icon="o-user" placeholder="Ketik nama karyawan..."
                                 wire:model.live.debounce.300ms="karyawanSearch"
                                 x-on:focus="showDropdown = true"
                                 x-on:click.away="showDropdown = false"
                                 autocomplete="off" />

                        @if (!empty($karyawanResults) && $karyawanSearch && !$karyawanId)
                            <div class="absolute z-20 w-full bg-base-100 border border-base-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-auto"
                                 x-show="showDropdown">
                                @foreach ($karyawanResults as $k)
                                    <div class="px-4 py-3 hover:bg-base-200 cursor-pointer border-b border-base-200 last:border-b-0"
                                         wire:click="selectKaryawan({{ $k['id'] }})"
                                         x-on:click="showDropdown = false">
                                        <div class="font-bold">{{ $k['nama_karyawan'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $k['jabatan'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Selected Karyawan Info --}}
                    @if ($karyawanId)
                        <div class="alert alert-success">
                            <x-icon name="o-check-badge" class="w-5 h-5" />
                            <div>
                                <div class="font-bold">{{ $selectedNama }}</div>
                                <div class="text-sm">{{ $selectedJabatan }}</div>
                            </div>
                            <button type="button" class="btn btn-ghost btn-xs" wire:click="clearKaryawan">
                                <x-icon name="o-x-mark" class="w-4 h-4" />
                            </button>
                        </div>
                    @endif

                    {{-- Email --}}
                    <x-input label="Email" icon="o-envelope" placeholder="email@domain.com"
                             wire:model="email" type="email" />

                    {{-- Password --}}
                    <x-input label="Password" icon="o-key" placeholder="Default: 12345678"
                             wire:model="password" type="password" />

                    <div class="text-sm text-gray-500">
                        Password default: <strong>12345678</strong> (bisa diubah)
                    </div>
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
