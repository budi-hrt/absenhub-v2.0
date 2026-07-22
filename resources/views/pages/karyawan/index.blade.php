<?php

use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Status;
use App\Models\Nonaktif;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use Toast, WithPagination, WithFileUploads;

    public string $search = '';
    public string $filterJabatan = '';
    public string $filterStatus = 'aktif';
    public string $filterAgama = '';
    public string $filterKerja = '';

    public bool $detailModal = false;
    public bool $nonaktifModal = false;
    public bool $aktifModal = false;
    public bool $fotoModal = false;
    public ?int $karyawanId = null;
    public ?int $detailKaryawanId = null;
    public ?int $fotoKaryawanId = null;
    public $fotoUpload = null;
    public ?string $existingFoto = null;

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

    protected function getFilteredQuery()
    {
        return Karyawan::with(['jabatan', 'status'])
            ->when($this->search, fn($q) => $q->where('nama_karyawan', 'like', "%{$this->search}%")
                ->orWhere('nik', 'like', "%{$this->search}%"))
            ->when($this->filterJabatan, fn($q) => $q->where('jabatan_id', $this->filterJabatan))
            ->when($this->filterStatus === 'aktif', fn($q) => $q->where('is_active', true))
            ->when($this->filterStatus === 'nonaktif', fn($q) => $q->where('is_active', false))
            ->when($this->filterAgama, fn($q) => $q->where('agama_karyawan', $this->filterAgama))
            ->when($this->filterKerja, fn($q) => $q->where('status_id', $this->filterKerja))
            ->orderBy('nama_karyawan');
    }

    #[Computed]
    public function karyawans()
    {
        return $this->getFilteredQuery()->paginate(10);
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
        $karyawans = $this->karyawans;
        $start = $karyawans->firstItem();
        $karyawans->getCollection()->transform(fn($item, $i) => tap($item)->setAttribute('row_no', $start + $i));

        return [
            'karyawans' => $karyawans,
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

    public function editFoto(int $id): void
    {
        $k = Karyawan::findOrFail($id);
        $this->fotoKaryawanId = $id;
        $this->existingFoto = $k->foto_karyawan;
        $this->fotoUpload = null;
        $this->fotoModal = true;
    }

    public function saveFoto(): void
    {
        $this->validate(['fotoUpload' => 'required|image|max:2048']);

        $k = Karyawan::findOrFail($this->fotoKaryawanId);

        if ($k->foto_karyawan && Storage::disk('public')->exists($k->foto_karyawan)) {
            Storage::disk('public')->delete($k->foto_karyawan);
        }

        $k->update(['foto_karyawan' => $this->fotoUpload->store('karyawan', 'public')]);

        $this->success('Foto berhasil diperbarui.', position: 'toast-top toast-end');
        $this->fotoModal = false;
        $this->fotoKaryawanId = null;
        $this->fotoUpload = null;
        $this->existingFoto = null;
    }

    public function closeModal(): void
    {
        $this->detailModal = false;
        $this->nonaktifModal = false;
        $this->aktifModal = false;
        $this->fotoModal = false;
        $this->detailKaryawanId = null;
        $this->karyawanId = null;
        $this->fotoKaryawanId = null;
        $this->fotoUpload = null;
        $this->existingFoto = null;
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
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Jabatan</legend>
            <select class="select select-bordered select-sm w-48" wire:model.live="filterJabatan">
                <option value="">Semua Jabatan</option>
                @foreach ($jabatans as $j)
                    <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                @endforeach
            </select>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Status Aktif</legend>
            <select class="select select-bordered select-sm w-40" wire:model.live="filterStatus">
                <option value="">Semua</option>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Status Kerja</legend>
            <select class="select select-bordered select-sm w-48" wire:model.live="filterKerja">
                <option value="">Semua Status</option>
                @foreach ($statuses as $s)
                    <option value="{{ $s->id }}">{{ $s->nama_status }}</option>
                @endforeach
            </select>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Agama</legend>
            <select class="select select-bordered select-sm w-48" wire:model.live="filterAgama">
                <option value="">Semua Agama</option>
                @foreach (\App\Models\Karyawan::where('is_active', true)->whereNotNull('agama_karyawan')->distinct()->pluck('agama_karyawan')->sort() as $a)
                    <option value="{{ $a }}">{{ $a }}</option>
                @endforeach
            </select>
        </fieldset>

        {{-- Export Buttons --}}
        <div class="flex items-end gap-2 ml-auto">
            <a x-bind:href="'/karyawan/export/excel?search=' + encodeURIComponent($wire.search)
                + '&filterJabatan=' + encodeURIComponent($wire.filterJabatan)
                + '&filterStatus=' + encodeURIComponent($wire.filterStatus)
                + '&filterAgama=' + encodeURIComponent($wire.filterAgama)
                + '&filterKerja=' + encodeURIComponent($wire.filterKerja)"
                class="btn btn-success btn-outline btn-sm" target="_blank">
                <x-icon name="o-table-cells" class="w-4 h-4" />
                Export Excel
            </a>
            <a x-bind:href="'/karyawan/export/pdf?search=' + encodeURIComponent($wire.search)
                + '&filterJabatan=' + encodeURIComponent($wire.filterJabatan)
                + '&filterStatus=' + encodeURIComponent($wire.filterStatus)
                + '&filterAgama=' + encodeURIComponent($wire.filterAgama)
                + '&filterKerja=' + encodeURIComponent($wire.filterKerja)"
                class="btn btn-error btn-outline btn-sm" target="_blank">
                <x-icon name="o-document" class="w-4 h-4" />
                Export PDF
            </a>
        </div>
    </div>

    {{-- Table --}}
    <x-card shadow>
        <div class="relative min-h-[30rem]">
            {{-- Loading Overlay --}}
            <div wire:loading wire:target="search, filterJabatan, filterStatus, filterKerja, filterAgama, gotoPage, nextPage, previousPage" class="absolute inset-0 bg-base-100/30 backdrop-blur-[1px] z-50 rounded-xl transition-all duration-150">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center gap-2">
                    <span class="loading loading-spinner loading-lg text-emerald-500"></span>
                    <span class="text-xs font-bold text-emerald-600/70 tracking-wider uppercase animate-pulse">Memuat...</span>
                </div>
            </div>

            <div wire:loading.class="opacity-25 pointer-events-none" wire:target="search, filterJabatan, filterStatus, filterKerja, filterAgama, gotoPage, nextPage, previousPage" class="transition-opacity duration-150">
                <x-table :headers="$headers" :rows="$karyawans" with-pagination>
                @scope('cell_no', $row)
                    <span class="text-sm text-base-content/50">{{ $row->row_no }}</span>
                @endscope

                @scope('cell_karyawan', $row)
                    <div class="flex items-center gap-3">
                        <div class="avatar cursor-pointer {{ !$row->foto_karyawan ? 'placeholder' : '' }}" wire:click="editFoto({{ $row->id }})" title="Klik untuk ganti foto">
                            <div class="mask mask-squircle w-10 h-10 {{ !$row->foto_karyawan ? 'bg-gradient-to-br from-primary/20 to-primary/10 text-primary border border-primary/20 flex items-center justify-center font-bold text-xs' : '' }}">
                                @if ($row->foto_karyawan)
                                    <img src="{{ Storage::url($row->foto_karyawan) }}" alt="{{ $row->nama_karyawan }}" />
                                @else
                                    <span>{{ strtoupper(substr($row->nama_karyawan, 0, 2)) }}</span>
                                @endif
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
        </div>
    </div>
</x-card>

    {{-- Modal Detail --}}
    @php
        $detail = $detailKaryawanId ? \App\Models\Karyawan::with(['jabatan', 'status'])->find($detailKaryawanId) : null;
        $lamaKerja = '-';
        $riwayats = collect();

        if ($detail) {
            if ($detail->is_active && $detail->tanggal_masuk) {
                $lamaKerja = \Carbon\Carbon::parse($detail->tanggal_masuk)->locale('id')->diffForHumans(['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]);
            } elseif (!$detail->is_active) {
                $lastNonaktif = \App\Models\Nonaktif::where('karyawan_id', $detail->id)
                    ->latest('tanggal_nonaktif')->first();
                if ($lastNonaktif && $lastNonaktif->tanggal_aktif) {
                    $lamaKerja = \Carbon\Carbon::parse($lastNonaktif->tanggal_aktif)
                        ->locale('id')->diffForHumans($lastNonaktif->tanggal_nonaktif, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]);
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
    <x-modal wire:model="detailModal" box-class="!max-w-xl !p-0">
        @if ($detail)
            {{-- Header Gradient dengan Foto --}}
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-6 py-5 rounded-t-xl">
                <div class="flex items-center gap-5">
                    <div class="avatar {{ !$detail->foto_karyawan ? 'placeholder' : '' }}">
                        <div class="w-20 h-20 rounded-full ring-2 ring-white/30 ring-offset-0 {{ !$detail->foto_karyawan ? 'bg-white/20 text-white flex items-center justify-center font-bold text-lg' : '' }}">
                            @if ($detail->foto_karyawan)
                                <img src="{{ Storage::url($detail->foto_karyawan) }}" />
                            @else
                                <span>{{ strtoupper(substr($detail->nama_karyawan, 0, 2)) }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">{{ $detail->nama_karyawan }}</h3>
                        <p class="text-sm text-white/70">{{ $detail->jabatan?->nama_jabatan ?? '-' }}</p>
                        <div class="flex items-center gap-2 mt-2 flex-wrap">
                            <x-badge :value="$detail->is_active ? 'Aktif' : 'Nonaktif'"
                                :class="$detail->is_active ? 'badge-success badge-sm' : 'badge-error badge-sm'" />
                            <x-badge :value="$detail->status?->nama_status ?? '-'" class="badge-info badge-sm" />
                            <span class="text-xs text-white/60">
                                Lama Kerja: <span class="font-semibold text-white">{{ $lamaKerja }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6">
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
        </div>
        @endif

        <x-slot:actions>
            <div class="px-6 pb-6">
                <x-button label="Tutup" wire:click="closeModal" />
            </div>
        </x-slot:actions>
    </x-modal>

    {{-- Modal Nonaktif --}}
    @php $nk = $karyawanId ? \App\Models\Karyawan::find($karyawanId) : null; @endphp
    <x-modal wire:model="nonaktifModal" box-class="!max-w-xl !p-0">
        @if ($nk)
            <div class="bg-gradient-to-r from-red-500 to-rose-700 text-white px-6 py-5 rounded-t-xl">
                <div class="flex items-center gap-4">
                    <div class="avatar {{ !$nk->foto_karyawan ? 'placeholder' : '' }}">
                        <div class="w-16 h-16 rounded-full ring-2 ring-white/30 ring-offset-0 {{ !$nk->foto_karyawan ? 'bg-white/20 text-white flex items-center justify-center font-bold text-base' : '' }}">
                            @if ($nk->foto_karyawan)
                                <img src="{{ Storage::url($nk->foto_karyawan) }}" />
                            @else
                                <span>{{ strtoupper(substr($nk->nama_karyawan, 0, 2)) }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="font-bold text-lg">{{ $nk->nama_karyawan }}</div>
                        <div class="text-xs text-white/70">NIK: {{ $nk->nik }}</div>
                    </div>
                </div>
            </div>
        @endif
        <div class="p-6">
            <x-form wire:submit.prevent="nonaktifkan">
                <x-input wire:model="tanggalNonaktif" label="Tanggal Nonaktif" type="date" required />
                <x-input wire:model="alasanNonaktif" label="Alasan" placeholder="Alasan nonaktif" required />
                <x-slot:actions>
                    <x-button label="Batal" wire:click="closeModal" type="button" />
                    <x-button label="Nonaktifkan" class="btn-error" type="submit" />
                </x-slot:actions>
            </x-form>
        </div>
    </x-modal>

    {{-- Modal Aktifkan --}}
    <x-modal wire:model="aktifModal" box-class="!max-w-xl !p-0">
        @if ($nk)
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-6 py-5 rounded-t-xl">
                <div class="flex items-center gap-4">
                    <div class="avatar {{ !$nk->foto_karyawan ? 'placeholder' : '' }}">
                        <div class="w-16 h-16 rounded-full ring-2 ring-white/30 ring-offset-0 {{ !$nk->foto_karyawan ? 'bg-white/20 text-white flex items-center justify-center font-bold text-base' : '' }}">
                            @if ($nk->foto_karyawan)
                                <img src="{{ Storage::url($nk->foto_karyawan) }}" />
                            @else
                                <span>{{ strtoupper(substr($nk->nama_karyawan, 0, 2)) }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="font-bold text-lg">{{ $nk->nama_karyawan }}</div>
                        <div class="text-xs text-white/70">NIK: {{ $nk->nik }}</div>
                    </div>
                </div>
            </div>
        @endif
        <div class="p-6">
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
        </div>
    </x-modal>

    {{-- Modal Edit Foto --}}
    @php $fotoK = $fotoKaryawanId ? \App\Models\Karyawan::find($fotoKaryawanId) : null; @endphp
    <x-modal wire:model="fotoModal" title="Edit Foto Karyawan"
        subtitle="{{ $fotoK?->nama_karyawan ?? '' }}" box-class="!max-w-sm">
        @if ($fotoK)
            <div class="flex flex-col items-center gap-4">
                <div class="w-40 h-40 rounded-xl border-2 border-dashed border-base-300 overflow-hidden bg-base-200 flex flex-col items-center justify-center relative group">
                    @if ($fotoUpload)
                        <img src="{{ $fotoUpload->temporaryUrl() }}"
                            class="absolute inset-0 w-full h-full object-cover" />
                    @elseif ($fotoK->foto_karyawan)
                        <img src="{{ Storage::url($fotoK->foto_karyawan) }}"
                            class="absolute inset-0 w-full h-full object-cover" />
                    @else
                        <div class="flex flex-col items-center text-base-content/50">
                            <x-icon name="o-camera" class="w-10 h-10 mb-1" />
                            <span class="text-xs text-center px-2">Belum ada foto</span>
                        </div>
                    @endif
                    <label
                        class="absolute inset-0 cursor-pointer opacity-0 group-hover:opacity-100 bg-primary/20 transition-opacity flex items-center justify-center rounded-xl">
                        <span class="badge badge-soft badge-primary px-4 py-3 text-xs font-bold shadow-md">Pilih Foto</span>
                        <input type="file" accept="image/*" class="hidden" wire:model="fotoUpload" />
                    </label>
                </div>
                @error('fotoUpload')
                    <span class="text-error text-xs">{{ $message }}</span>
                @enderror
                <p class="text-xs text-base-content/50 text-center">Klik area foto untuk memilih file. Maksimal 2MB.</p>
            </div>
        @endif
        <x-slot:actions>
            <x-button label="Batal" wire:click="closeModal" />
            <x-button label="Simpan" class="btn-primary" wire:click="saveFoto" spinner />
        </x-slot:actions>
    </x-modal>
</div>
