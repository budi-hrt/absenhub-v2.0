<?php

use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Status;
use App\Models\Nonaktif;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use Toast, WithPagination;

    public string $search = '';
    public string $filterJabatan = '';
    public string $filterStatus = 'aktif';
    public string $filterAgama = '';
    public string $filterKerja = '';

    public bool $detailModal = false;
    public bool $nonaktifModal = false;
    public bool $aktifModal = false;
    public ?int $karyawanId = null;
    public ?int $detailKaryawanId = null;

    public string $alasanNonaktif = '';
    public string $tanggalNonaktif = '';
    public string $tanggalAktif = '';
    public ?int $statusIdAktif = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterJabatan(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterAgama(): void
    {
        $this->resetPage();
    }

    public function updatingFilterKerja(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function karyawans()
    {
        return Karyawan::with(['jabatan', 'status'])
            ->when($this->search, fn($q) => $q->where('nama_karyawan', 'like', "%{$this->search}%")
                ->orWhere('nik', 'like', "%{$this->search}%"))
            ->when($this->filterJabatan, fn($q) => $q->where('jabatan_id', $this->filterJabatan))
            ->when($this->filterStatus === 'aktif', fn($q) => $q->where('is_active', true))
            ->when($this->filterStatus === 'nonaktif', fn($q) => $q->where('is_active', false))
            ->when($this->filterAgama, fn($q) => $q->where('agama_karyawan', $this->filterAgama))
            ->when($this->filterKerja, fn($q) => $q->where('status_id', $this->filterKerja))
            ->orderBy('nama_karyawan')
            ->paginate(10);
    }

    public function headers(): array
    {
        return [
            ['key' => 'no', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'karyawan', 'label' => 'Karyawan', 'sortable' => false],
            ['key' => 'nik', 'label' => 'NIK'],
            ['key' => 'kontak', 'label' => 'Kontak', 'sortable' => false],
            ['key' => 'is_active', 'label' => 'Status', 'class' => 'w-36'],
            ['key' => 'agama', 'label' => 'Agama', 'sortable' => false],
        ];
    }

    public function with(): array
    {
        return [
            'karyawans' => $this->karyawans,
            'headers' => $this->headers(),
            'jabatans' => Jabatan::where('is_active', true)->orderBy('nama_jabatan')->get(),
            'statuses' => Status::where('is_active', true)->where('id', '!=', 3)->orderBy('nama_status')->get(),
        ];
    }

    public function detailKaryawan(int $id): void
    {
        $this->detailKaryawanId = $id;
        $this->detailModal = true;
    }

    public function confirmAktif(int $id): void
    {
        $this->karyawanId = $id;
        $this->tanggalAktif = now()->format('Y-m-d');
        $this->statusIdAktif = 2;
        $this->aktifModal = true;
    }

    public function aktifkan(): void
    {
        $this->validate([
            'tanggalAktif' => 'required|date',
            'statusIdAktif' => 'required|exists:statuses,id',
        ]);

        $k = Karyawan::findOrFail($this->karyawanId);
        $k->update([
            'tanggal_masuk' => $this->tanggalAktif,
            'status_id' => $this->statusIdAktif,
            'is_active' => true,
        ]);

        $this->success("{$k->nama_karyawan} berhasil diaktifkan.", position: 'toast-top toast-end');
        $this->aktifModal = false;
        $this->karyawanId = null;
    }

    public function confirmNonaktif(int $id): void
    {
        $this->karyawanId = $id;
        $this->alasanNonaktif = '';
        $this->tanggalNonaktif = now()->format('Y-m-d');
        $this->nonaktifModal = true;
    }

    public function nonaktifkan(): void
    {
        $this->validate([
            'tanggalNonaktif' => 'required|date',
            'alasanNonaktif' => 'required|string|max:255',
        ]);

        $k = Karyawan::findOrFail($this->karyawanId);

        Nonaktif::create([
            'karyawan_id' => $k->id,
            'tanggal_aktif' => $k->tanggal_masuk,
            'tanggal_nonaktif' => $this->tanggalNonaktif,
            'alasan' => $this->alasanNonaktif,
        ]);

        $k->update(['is_active' => false, 'status_id' => 3]);

        $this->success("{$k->nama_karyawan} berhasil dinonaktifkan.", position: 'toast-top toast-end');
        $this->nonaktifModal = false;
        $this->karyawanId = null;
    }

    public function closeModal(): void
    {
        $this->detailModal = false;
        $this->nonaktifModal = false;
        $this->aktifModal = false;
        $this->detailKaryawanId = null;
        $this->karyawanId = null;
    }
};
?>

<div>
    <x-header title="Data Karyawan" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Cari nama/NIK..." wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <a href="{{ route('karyawan.create') }}" wire:navigate>
                <x-button label="Tambah Karyawan" icon="o-plus" class="btn-primary" />
            </a>
        </x-slot:actions>
    </x-header>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3 mb-4">
        <div class="form-control">
            <label class="label py-1"><span class="label-text text-xs">Jabatan</span></label>
            <select class="select select-bordered select-sm w-48" wire:model.live="filterJabatan">
                <option value="">Semua Jabatan</option>
                @foreach ($jabatans as $j)
                    <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-control">
            <label class="label py-1"><span class="label-text text-xs">Status Aktif</span></label>
            <select class="select select-bordered select-sm w-40" wire:model.live="filterStatus">
                <option value="">Semua</option>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
        </div>
        <div class="form-control">
            <label class="label py-1"><span class="label-text text-xs">Status Kerja</span></label>
            <select class="select select-bordered select-sm w-48" wire:model.live="filterKerja">
                <option value="">Semua Status</option>
                @foreach ($statuses as $s)
                    <option value="{{ $s->id }}">{{ $s->nama_status }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-control">
            <label class="label py-1"><span class="label-text text-xs">Agama</span></label>
            <select class="select select-bordered select-sm w-48" wire:model.live="filterAgama">
                <option value="">Semua Agama</option>
                @foreach (\App\Models\Karyawan::where('is_active', true)->whereNotNull('agama_karyawan')->distinct()->pluck('agama_karyawan')->sort() as $a)
                    <option value="{{ $a }}">{{ $a }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Table --}}
    <x-card shadow>
        <x-table :headers="$headers" :rows="$karyawans" with-pagination>
            @scope('cell_no', $row)
                <span class="text-sm text-base-content/50">{{ $loop->iteration }}</span>
            @endscope

            @scope('cell_karyawan', $row)
                <div class="flex items-center gap-3">
                    <div class="avatar">
                        <div class="mask mask-squircle w-10 h-10">
                            <img src="{{ $row->foto_karyawan ? Storage::url($row->foto_karyawan) : 'https://i.pravatar.cc/150?u=' . $row->nik }}" alt="{{ $row->nama_karyawan }}" />
                        </div>
                    </div>
                    <div>
                        <div class="font-bold text-sm">{{ $row->nama_karyawan }}</div>
                        <div class="text-xs text-base-content/50">{{ $row->jabatan?->nama_jabatan ?? '-' }}</div>
                    </div>
                </div>
            @endscope

            @scope('cell_kontak', $row)
                <div class="text-sm">
                    <div>{{ $row->telp_karyawan }}</div>
                    <div class="text-xs text-base-content/50">{{ $row->email_karyawan }}</div>
                </div>
            @endscope

            @scope('cell_is_active', $row)
                <div class="text-sm">{{ $row->status?->nama_status ?? '-' }}</div>
                <div class="text-xs {{ $row->is_active ? 'text-success' : 'text-error' }}">{{ $row->is_active ? 'Aktif' : 'Nonaktif' }}</div>
            @endscope

            @scope('cell_agama', $row)
                <x-badge :value="$row->agama_karyawan ?? '-'" class="badge-info badge-soft" />
            @endscope

            @scope('actions', $row)
                <div class="flex gap-1">
                    <x-button icon="o-eye" wire:click="detailKaryawan({{ $row->id }})"
                        class="btn-ghost btn-sm text-info" spinner />
                    <a href="{{ route('karyawan.edit', $row->id) }}" wire:navigate>
                        <x-button icon="o-pencil" class="btn-ghost btn-sm text-primary" />
                    </a>
                    @if ($row->is_active)
                        <x-button icon="o-no-symbol" wire:click="confirmNonaktif({{ $row->id }})"
                            class="btn-ghost btn-sm text-error" spinner />
                    @else
                        <x-button icon="o-check-badge" wire:click="confirmAktif({{ $row->id }})"
                            class="btn-ghost btn-sm text-success" spinner />
                    @endif
                </div>
            @endscope
        </x-table>
    </x-card>

    {{-- Modal Detail --}}
    @php
        $detail = $detailKaryawanId ? \App\Models\Karyawan::with(['jabatan', 'status'])->find($detailKaryawanId) : null;
        $lamaKerja = '-';
        $riwayats = collect();

        if ($detail) {
            if ($detail->is_active && $detail->tanggal_masuk) {
                $lamaKerja = \Carbon\Carbon::parse($detail->tanggal_masuk)->diffForHumans(['parts' => 2]);
            } elseif (!$detail->is_active) {
                $lastNonaktif = \App\Models\Nonaktif::where('karyawan_id', $detail->id)
                    ->latest('tanggal_nonaktif')->first();
                if ($lastNonaktif && $lastNonaktif->tanggal_aktif) {
                    $lamaKerja = \Carbon\Carbon::parse($lastNonaktif->tanggal_aktif)
                        ->diffForHumans($lastNonaktif->tanggal_nonaktif, ['parts' => 2]);
                }
            }

            $nonaktifs = \App\Models\Nonaktif::where('karyawan_id', $detail->id)
                ->orderBy('tanggal_aktif')->get();

            foreach ($nonaktifs as $n) {
                $riwayats->push(['tanggal' => $n->tanggal_aktif, 'jenis' => 'Masuk', 'keterangan' => '']);
                $riwayats->push(['tanggal' => $n->tanggal_nonaktif, 'jenis' => 'Keluar', 'keterangan' => $n->alasan]);
            }

            if ($detail->is_active) {
                $riwayats->push(['tanggal' => $detail->tanggal_masuk, 'jenis' => 'Masuk', 'keterangan' => 'Saat ini aktif']);
            }

            $riwayats = $riwayats->sortBy('tanggal')->values();
        }
    @endphp
    <x-modal wire:model="detailModal" title="Detail Karyawan" subtitle="{{ $detail?->nama_karyawan ?? '' }}"
        class="!max-w-3xl">
        @if ($detail)
            {{-- Header: Foto + Nama + Jabatan --}}
            <div class="flex items-center gap-5 mb-5">
                <div class="avatar">
                    <div class="w-20 h-20 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                        <img src="{{ $detail->foto_karyawan ? Storage::url($detail->foto_karyawan) : 'https://i.pravatar.cc/150?u=' . $detail->nik }}" />
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold">{{ $detail->nama_karyawan }}</h3>
                    <p class="text-sm text-base-content/50">{{ $detail->jabatan?->nama_jabatan ?? '-' }}</p>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        <x-badge :value="$detail->is_active ? 'Aktif' : 'Nonaktif'" :class="$detail->is_active ? 'badge-success badge-sm' : 'badge-error badge-sm'" />
                        <x-badge :value="$detail->status?->nama_status ?? '-'" class="badge-info badge-sm" />
                        <span class="text-xs text-base-content/50">
                            Lama Kerja: <span class="font-semibold text-base-content">{{ $lamaKerja }}</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Data Penting --}}
            <div class="rounded-xl overflow-hidden text-sm">
                @php
                    $fields = [
                        ['label' => 'NIK', 'value' => $detail->nik],
                        ['label' => 'Jenis Kelamin', 'value' => $detail->jk_karyawan === 'L' ? 'Laki-laki' : 'Perempuan'],
                        ['label' => 'Tanggal Lahir', 'value' => ($detail->tempat_lahir ? $detail->tempat_lahir . ', ' : '') . ($detail->tanggal_lahir?->format('d/m/Y') ?? '-')],
                        ['label' => 'Agama', 'value' => $detail->agama_karyawan],
                        ['label' => 'Telepon', 'value' => $detail->telp_karyawan],
                        ['label' => 'Email', 'value' => $detail->email_karyawan],
                        ['label' => 'Tanggal Masuk', 'value' => $detail->tanggal_masuk?->format('d/m/Y') ?? '-'],
                        ['label' => 'Alamat', 'value' => $detail->alamat_karyawan],
                    ];
                @endphp
                @foreach ($fields as $i => $field)
                    <div class="flex items-start gap-0 px-4 py-3 {{ $i % 2 === 0 ? 'bg-base-200/40' : '' }}">
                        <span class="w-40 shrink-0 text-base-content/60">{{ $field['label'] }}</span>
                        <span class="w-5 shrink-0 text-base-content/30 text-center">:</span>
                        <span class="font-semibold text-base-content">{{ $field['value'] ?? '-' }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Riwayat Keluar/Masuk --}}
            @if ($riwayats->count())
                <div class="mt-5 pt-4 border-t border-base-200/60">
                    <h4 class="font-bold text-sm mb-3">Riwayat Keluar/Masuk</h4>
                    <div class="rounded-xl overflow-hidden text-sm">
                        <table class="table table-sm">
                            <thead>
                                <tr class="bg-base-200/60">
                                    <th class="font-semibold text-xs uppercase tracking-wide">Tanggal</th>
                                    <th class="font-semibold text-xs uppercase tracking-wide">Jenis</th>
                                    <th class="font-semibold text-xs uppercase tracking-wide">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($riwayats as $r)
                                    <tr class="{{ $loop->even ? 'bg-base-200/40' : '' }}">
                                        <td>{{ \Carbon\Carbon::parse($r['tanggal'])->format('d/m/Y') }}</td>
                                        <td>
                                            <x-badge :value="$r['jenis']"
                                                :class="$r['jenis'] === 'Masuk' ? 'badge-success badge-soft badge-sm' : 'badge-error badge-soft badge-sm'" />
                                        </td>
                                        <td class="text-base-content/60">{{ $r['keterangan'] ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif

        <x-slot:actions>
            <x-button label="Tutup" wire:click="closeModal" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal Nonaktif --}}
    @php $nk = $karyawanId ? \App\Models\Karyawan::find($karyawanId) : null; @endphp
    <x-modal wire:model="nonaktifModal" title="Nonaktifkan Karyawan" subtitle="Pastikan data sudah benar">
        @if ($nk)
            <div class="flex flex-col items-center gap-2 mb-5">
                <div class="avatar">
                    <div class="w-16 h-16 rounded-full ring ring-error ring-offset-base-100 ring-offset-2">
                        <img src="{{ $nk->foto_karyawan ? Storage::url($nk->foto_karyawan) : 'https://i.pravatar.cc/150?u=' . $nk->nik }}" />
                    </div>
                </div>
                <div class="text-center">
                    <div class="font-bold">{{ $nk->nama_karyawan }}</div>
                    <div class="text-xs text-base-content/50">NIK: {{ $nk->nik }}</div>
                </div>
            </div>
        @endif
        <x-form wire:submit.prevent="nonaktifkan">
            <x-input wire:model="tanggalNonaktif" label="Tanggal Nonaktif" type="date" required />
            <x-input wire:model="alasanNonaktif" label="Alasan" placeholder="Alasan nonaktif" required />
            <x-slot:actions>
                <x-button label="Batal" wire:click="closeModal" type="button" />
                <x-button label="Nonaktifkan" class="btn-error" type="submit" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- Modal Aktifkan --}}
    <x-modal wire:model="aktifModal" title="Aktifkan Karyawan" subtitle="Masukkan tanggal masuk (kontrak kerja)">
        @if ($nk)
            <div class="flex flex-col items-center gap-2 mb-5">
                <div class="avatar">
                    <div class="w-16 h-16 rounded-full ring ring-success ring-offset-base-100 ring-offset-2">
                        <img src="{{ $nk->foto_karyawan ? Storage::url($nk->foto_karyawan) : 'https://i.pravatar.cc/150?u=' . $nk->nik }}" />
                    </div>
                </div>
                <div class="text-center">
                    <div class="font-bold">{{ $nk->nama_karyawan }}</div>
                    <div class="text-xs text-base-content/50">NIK: {{ $nk->nik }}</div>
                </div>
            </div>
        @endif
        <x-form wire:submit.prevent="aktifkan">
            <x-input wire:model="tanggalAktif" label="Tanggal Masuk (Kontrak Kerja)" type="date" required />
            <div class="form-control">
                <label class="label"><span class="label-text">Status Kepegawaian</span></label>
                <select class="select select-bordered" wire:model="statusIdAktif">
                    @foreach (\App\Models\Status::where('is_active', true)->where('id', '!=', 3)->orderBy('nama_status')->get() as $s)
                        <option value="{{ $s->id }}">{{ $s->nama_status }}</option>
                    @endforeach
                </select>
            </div>
            <x-slot:actions>
                <x-button label="Batal" wire:click="closeModal" type="button" />
                <x-button label="Aktifkan" class="btn-success" type="submit" />
            </x-slot:actions>
        </x-form>
    </x-modal>
</div>
