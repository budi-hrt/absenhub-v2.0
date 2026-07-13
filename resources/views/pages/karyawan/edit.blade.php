<?php

use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Status;
use Livewire\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use Toast, WithFileUploads;

    public int $karyawanId;
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
    public ?string $existingFoto = null;
    public string $alamat = '';
    public string $npwp = '';
    public string $pendidikan = '';
    public string $berijazah = '';
    public string $rekening = '';
    public ?int $statusId = null;
    public string $tanggalMasuk = '';
    public ?int $pinMesin = null;

    public function mount(Karyawan $karyawan): void
    {
        $this->karyawanId = $karyawan->id;
        $this->nik = $karyawan->nik ?? '';
        $this->nama = $karyawan->nama_karyawan;
        $this->jk = $karyawan->jk_karyawan;
        $this->jabatanId = $karyawan->jabatan_id;
        $this->tempatLahir = $karyawan->tempat_lahir;
        $this->tanggalLahir = $karyawan->tanggal_lahir ? $karyawan->tanggal_lahir->format('Y-m-d') : '';
        $this->agama = $karyawan->agama_karyawan;
        $this->statusPernikahan = $karyawan->status_pernikahan;
        $this->telp = $karyawan->telp_karyawan;
        $this->email = $karyawan->email_karyawan;
        $this->existingFoto = $karyawan->foto_karyawan;
        $this->alamat = $karyawan->alamat_karyawan ?? '';
        $this->npwp = $karyawan->npwp_karyawan ?? '';
        $this->pendidikan = $karyawan->pendidikan ?? '';
        $this->berijazah = $karyawan->berijazah ?? '';
        $this->rekening = $karyawan->rekening ?? '';
        $this->statusId = $karyawan->status_id;
        $this->tanggalMasuk = $karyawan->tanggal_masuk ? $karyawan->tanggal_masuk->format('Y-m-d') : '';
        $this->pinMesin = $karyawan->pin_mesin;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:255|unique:karyawans,nik,' . $this->karyawanId,
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
            'pinMesin' => 'nullable|integer',
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
            if ($this->existingFoto && Storage::disk('public')->exists($this->existingFoto)) {
                Storage::disk('public')->delete($this->existingFoto);
            }
            $data['foto_karyawan'] = $this->foto->store('karyawan', 'public');
        }

        Karyawan::findOrFail($this->karyawanId)->update($data);
        $this->success('Data karyawan berhasil diupdate.', position: 'toast-top toast-end');
        $this->redirect(route('karyawan.index'));
    }
};
?>

<div>
    <x-header title="Edit Karyawan" separator progress-indicator />

    <x-card>
        <x-form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input wire:model="nik" label="NIK" placeholder="Nomor Induk Karyawan" />
                <x-input wire:model="nama" label="Nama Lengkap" placeholder="Nama karyawan" required />

                <div class="form-control">
                    <label class="label"><span class="label-text">Jenis Kelamin</span></label>
                    <div class="flex gap-4">
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

                <div class="form-control">
                    <label class="label"><span class="label-text">Jabatan</span></label>
                    <select class="select select-bordered" wire:model="jabatanId">
                        <option value="">Pilih Jabatan</option>
                        @foreach (\App\Models\Jabatan::where('is_active', true)->orderBy('nama_jabatan')->get() as $j)
                            <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                        @endforeach
                    </select>
                </div>

                <x-input wire:model="tempatLahir" label="Tempat Lahir" placeholder="Kota kelahiran" />
                <x-input wire:model="tanggalLahir" label="Tanggal Lahir" type="date" />

                <div class="form-control">
                    <label class="label"><span class="label-text">Agama</span></label>
                    <select class="select select-bordered" wire:model="agama">
                        <option value="">Pilih</option>
                        <option>Islam</option>
                        <option>Kristen</option>
                        <option>Katolik</option>
                        <option>Hindu</option>
                        <option>Buddha</option>
                        <option>Konghucu</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Status Pernikahan</span></label>
                    <select class="select select-bordered" wire:model="statusPernikahan">
                        <option value="">Pilih</option>
                        <option>Belum Menikah</option>
                        <option>Menikah</option>
                        <option>Cerai</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Status Kepegawaian</span></label>
                    <select class="select select-bordered" wire:model="statusId">
                        <option value="">Pilih Status</option>
                        @foreach (\App\Models\Status::where('is_active', true)->orderBy('nama_status')->get() as $s)
                            <option value="{{ $s->id }}">{{ $s->nama_status }}</option>
                        @endforeach
                    </select>
                </div>

                <x-input wire:model="tanggalMasuk" label="Tanggal Masuk" type="date" />
                <x-input wire:model="pinMesin" label="PIN Mesin Absen" type="number" placeholder="PIN" />

                <div class="form-control">
                    <label class="label"><span class="label-text">Pendidikan Terakhir</span></label>
                    <select class="select select-bordered" wire:model="pendidikan">
                        <option value="">Pilih</option>
                        <option>SD</option>
                        <option>SMP</option>
                        <option>SMA/SMK</option>
                        <option>D3</option>
                        <option>S1</option>
                        <option>S2</option>
                        <option>S3</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Berijazah</span></label>
                    <select class="select select-bordered" wire:model="berijazah">
                        <option value="">Pilih</option>
                        <option>Ya</option>
                        <option>Tidak</option>
                    </select>
                </div>

                <x-input wire:model="npwp" label="NPWP" placeholder="Nomor NPWP" />
                <x-input wire:model="rekening" label="No. Rekening" placeholder="Nomor rekening" />

                <x-input wire:model="telp" label="No. Telepon" placeholder="08xxxxxxxxxx" />
                <x-input wire:model="email" label="Email" type="email" placeholder="email@domain.com" />

                <div class="form-control md:col-span-2">
                    <label class="label"><span class="label-text">Foto Karyawan</span></label>
                    <div class="flex items-center gap-4">
                        @if ($existingFoto && !$foto)
                            <div class="avatar">
                                <div class="w-16 h-16 rounded-full">
                                    <img src="{{ Storage::url($existingFoto) }}" alt="Foto" />
                                </div>
                            </div>
                        @endif
                        <input type="file" class="file-input file-input-bordered file-input-primary w-full" accept="image/*" wire:model="foto" />
                    </div>
                    @error('foto') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="form-control md:col-span-2">
                    <label class="label"><span class="label-text">Alamat</span></label>
                    <textarea class="textarea textarea-bordered h-24" wire:model="alamat" placeholder="Alamat lengkap"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <a href="{{ route('karyawan.index') }}" wire:navigate>
                    <x-button label="Batal" type="button" />
                </a>
                <x-button label="Simpan" class="btn-primary" type="submit" spinner="save" />
            </div>
        </x-form>
    </x-card>
</div>
