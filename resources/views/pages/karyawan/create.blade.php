<?php

use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Status;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new class extends Component {
    use Toast, WithFileUploads;

    public string $nik = '';
    public string $nama = '';
    public string $jk = 'L';
    public ?int $jabatanId = null;
    public string $tempatLahir = '';
    public string $tanggalLahir = '';
    public string $agama = '';
    public string $statusPernikahan = '';
    public string $telp = '';
    public string $email = '';
    public $foto = null;
    public string $alamat = '';
    public string $npwp = '';
    public string $pendidikan = '';
    public string $berijazah = '';
    public string $rekening = '';
    public ?int $statusId = null;
    public string $tanggalMasuk = '';
    public ?int $pinMesin = null;

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:255|unique:karyawans,nik',
            'jk' => 'required|in:L,P',
            'jabatanId' => 'required|exists:jabatans,id',
            'tempatLahir' => 'required|string|max:255',
            'tanggalLahir' => 'nullable|date',
            'agama' => 'required|string|max:255',
            'statusPernikahan' => 'required|string|max:255',
            'telp' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'foto' => 'nullable|image|max:2048',
            'alamat' => 'nullable|string',
            'npwp' => 'nullable|string|max:255',
            'pendidikan' => 'nullable|string|max:255',
            'berijazah' => 'nullable|string|max:255',
            'rekening' => 'nullable|string|max:255',
            'statusId' => 'required|exists:statuses,id',
            'tanggalMasuk' => 'nullable|date',
            'pinMesin' => 'nullable|integer|unique:karyawans,pin_mesin',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama wajib diisi',
            'nik.unique' => 'NIK sudah digunakan',
            'jabatanId.required' => 'Jabatan wajib dipilih',
            'jabatanId.exists' => 'Jabatan tidak valid',
            'statusId.required' => 'Status kerja wajib dipilih',
            'statusId.exists' => 'Status kerja tidak valid',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'telp.required' => 'Telepon wajib diisi',
            'pinMesin.unique' => 'PIN Mesin sudah digunakan',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'nik' => $this->nik,
            'nama_karyawan' => $this->nama,
            'jk_karyawan' => $this->jk,
            'jabatan_id' => $this->jabatanId,
            'tempat_lahir' => $this->tempatLahir,
            'tanggal_lahir' => $this->tanggalLahir ?: null,
            'agama_karyawan' => $this->agama,
            'status_pernikahan' => $this->statusPernikahan,
            'telp_karyawan' => $this->telp,
            'email_karyawan' => $this->email,
            'alamat_karyawan' => $this->alamat,
            'npwp_karyawan' => $this->npwp,
            'pendidikan' => $this->pendidikan,
            'berijazah' => $this->berijazah,
            'rekening' => $this->rekening,
            'status_id' => $this->statusId,
            'tanggal_masuk' => $this->tanggalMasuk ?: null,
            'pin_mesin' => $this->pinMesin,
        ];

        if ($this->foto) {
            $data['foto_karyawan'] = $this->foto->store('karyawan', 'public');
        }

        Karyawan::create($data);
        $this->success('Karyawan berhasil ditambahkan.', position: 'toast-top toast-end');
        $this->redirect(route('karyawan.index'));
    }
};
?>

<div>
    <x-header title="Tambah Karyawan Baru" subtitle="Lengkapi informasi di bawah ini untuk menambahkan anggota tim baru ke dalam sistem." separator />

    <div class="space-y-6 pb-6">

        {{-- Section 1: Data Pribadi --}}
        <x-card>
            <div class="flex items-center gap-2 border-b border-base-300 pb-4 mb-6">
                <x-icon name="o-user" class="w-5 h-5 text-primary" />
                <h3 class="font-bold text-lg">Data Pribadi</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

                {{-- Kolom Foto --}}
                <div class="md:col-span-3 flex flex-col items-center gap-4">
                    <div class="w-40 h-40 rounded-xl border-2 border-dashed border-base-300 overflow-hidden bg-base-200 flex flex-col items-center justify-center relative group">
                        @if ($foto)
                            <img src="{{ $foto->temporaryUrl() }}" class="absolute inset-0 w-full h-full object-cover" />
                        @else
                            <div class="flex flex-col items-center text-base-content/50" id="emptyState">
                                <x-icon name="o-camera" class="w-10 h-10 mb-1" />
                                <span class="text-xs text-center px-2">Unggah Foto Karyawan</span>
                            </div>
                        @endif
                        <label class="absolute inset-0 cursor-pointer opacity-0 group-hover:opacity-100 bg-primary/20 transition-opacity flex items-center justify-center rounded-xl">
                            <span class="badge badge-soft badge-primary px-4 py-3 text-xs font-bold shadow-md">Ubah Foto</span>
                            <input type="file" accept="image/*" class="hidden" wire:model="foto" />
                        </label>
                    </div>
                    <p class="text-xs text-base-content/50 text-center">Format: JPG, PNG. Maks: 2MB.</p>
                    @error('foto') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Kolom Form --}}
                <div class="md:col-span-9 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-input wire:model="nik" label="NIK" placeholder="Masukkan 16 digit NIK" />
                    <x-input wire:model="nama" label="Nama Lengkap" placeholder="Sesuai KTP" />

                    <div class="form-control">
                        <label class="label"><span class="label-text">Jenis Kelamin</span></label>
                        <div class="flex gap-4 pt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" class="radio radio-primary radio-sm" wire:model="jk" value="L" />
                                <span>Laki-laki</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" class="radio radio-primary radio-sm" wire:model="jk" value="P" />
                                <span>Perempuan</span>
                            </label>
                        </div>
                    </div>

                    <x-input wire:model="tempatLahir" label="Tempat Lahir" placeholder="Kota/Kabupaten" />
                    <x-input wire:model="tanggalLahir" label="Tanggal Lahir" type="date" />

                    <div>
                        <label class="label"><span class="label-text">Agama</span></label>
                        <select class="select select-bordered w-full" wire:model="agama">
                            <option value="">Pilih Agama</option>
                            <option>Islam</option>
                            <option>Kristen Protestan</option>
                            <option>Katolik</option>
                            <option>Hindu</option>
                            <option>Buddha</option>
                            <option>Khonghucu</option>
                        </select>
                        @error('agama') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="label"><span class="label-text">Status Pernikahan</span></label>
                        <select class="select select-bordered w-full" wire:model="statusPernikahan">
                            <option value="">Pilih Status</option>
                            <option>Belum Kawin</option>
                            <option>Kawin</option>
                            <option>Cerai Hidup</option>
                            <option>Cerai Mati</option>
                        </select>
                        @error('statusPernikahan') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="label"><span class="label-text">Pendidikan Terakhir</span></label>
                        <select class="select select-bordered w-full" wire:model="pendidikan">
                            <option value="">Pilih Jenjang</option>
                            <option>SD</option>
                            <option>SMP</option>
                            <option>SMA/SMK</option>
                            <option>D3</option>
                            <option>S1</option>
                            <option>S2</option>
                            <option>S3</option>
                        </select>
                        @error('pendidikan') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div x-data="{ berijazah: $wire.entangle('berijazah') }" class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" class="checkbox checkbox-primary checkbox-sm" x-model="berijazah" true-value="Ya" false-value="Tidak" />
                            <span class="label-text font-medium">Berijazah</span>
                        </label>
                    </div>
                </div>
            </div>
        </x-card>

        {{-- Section 2: Kepegawaian --}}
        <x-card>
            <div class="flex items-center gap-2 border-b border-base-300 pb-4 mb-6">
                <x-icon name="o-briefcase" class="w-5 h-5 text-primary" />
                <h3 class="font-bold text-lg">Kepegawaian</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="label"><span class="label-text">Jabatan</span></label>
                    <select class="select select-bordered w-full" wire:model="jabatanId">
                        <option value="">Pilih Jabatan</option>
                        @foreach (\App\Models\Jabatan::where('is_active', true)->orderBy('nama_jabatan')->get() as $j)
                            <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                        @endforeach
                    </select>
                    @error('jabatanId') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="label"><span class="label-text">Status Karyawan</span></label>
                    <select class="select select-bordered w-full" wire:model="statusId">
                        <option value="">Pilih Status</option>
                        @foreach (\App\Models\Status::where('is_active', true)->orderBy('nama_status')->get() as $s)
                            <option value="{{ $s->id }}">{{ $s->nama_status }}</option>
                        @endforeach
                    </select>
                    @error('statusId') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <x-input wire:model="tanggalMasuk" label="Tanggal Masuk" type="date" />

                <x-input wire:model="pinMesin" label="PIN Mesin Absensi" type="number" placeholder="4-6 digit angka" />

                <x-input wire:model="npwp" label="NPWP" placeholder="Nomor NPWP" />

                <x-input wire:model="rekening" label="No. Rekening (Gaji)" placeholder="Nomor rekening" />
            </div>
        </x-card>

        {{-- Section 3: Kontak & Alamat --}}
        <x-card>
            <div class="flex items-center gap-2 border-b border-base-300 pb-4 mb-6">
                <x-icon name="o-phone" class="w-5 h-5 text-primary" />
                <h3 class="font-bold text-lg">Kontak &amp; Alamat</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input wire:model="telp" label="Nomor Telepon/WA" placeholder="08xxxxxxxxxx" />

                <x-input wire:model="email" label="Email Karyawan" type="email" placeholder="contoh@perusahaan.com" />

                <div class="md:col-span-2">
                    <x-textarea wire:model="alamat" label="Alamat Lengkap" placeholder="Nama Jalan, RT/RW, Kecamatan, Kota, Kode Pos" rows="3" />
                </div>
            </div>
        </x-card>

    </div>

    {{-- Actions --}}
    <div class="flex justify-end gap-3 pt-6 border-t border-base-300">
        <a href="{{ route('karyawan.index') }}" wire:navigate>
            <x-button label="Batal" icon="o-x-mark" />
        </a>
        <x-button label="Simpan Karyawan" icon="o-check" class="btn-primary" wire:click="save" spinner="save" />
    </div>
</div>
