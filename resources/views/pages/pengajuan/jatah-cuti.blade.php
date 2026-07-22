<?php

use App\Models\JatahCuti;
use App\Models\Karyawan;
use App\Models\PengajuanAbsen;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

new #[Layout('layouts.app')] #[Title('Jatah & Sisa Cuti')] class extends Component {
    use Toast;

    // Tahun yang dipilih untuk ditampilkan
    public int $selectedTahun;

    // Form edit jatah
    public bool $showEditModal = false;
    public ?int $editId = null;
    public int $editTahun;
    public int $editJatahCuti = 12;

    // Form tambah tahun baru
    public bool $showCreateModal = false;
    public int $createTahun;
    public int $createJatahCuti = 12;

    // Search karyawan
    public string $search = '';

    public function mount()
    {
        $this->selectedTahun = now()->year;
        $this->createTahun = now()->year;
        $this->editTahun = now()->year;

        // Pastikan record tahun ini ada
        JatahCuti::getTahun($this->selectedTahun);
    }

    public function updatedSelectedTahun()
    {
        // Pastikan record untuk tahun yang dipilih ada
        JatahCuti::getTahun($this->selectedTahun);
    }

    // --- Edit Jatah Cuti ---
    public function openEditModal()
    {
        $jatah = JatahCuti::getTahun($this->selectedTahun);
        $this->editId = $jatah->id;
        $this->editTahun = $jatah->tahun;
        $this->editJatahCuti = $jatah->jatah_cuti;
        $this->showEditModal = true;
    }

    public function updateJatahCuti()
    {
        $this->validate([
            'editJatahCuti' => 'required|integer|min:0|max:365',
        ], [
            'editJatahCuti.required' => 'Jatah cuti harus diisi.',
            'editJatahCuti.min' => 'Jatah cuti minimal 0 hari.',
            'editJatahCuti.max' => 'Jatah cuti maksimal 365 hari.',
        ]);

        $jatah = JatahCuti::findOrFail($this->editId);
        $jatah->update(['jatah_cuti' => $this->editJatahCuti]);

        $this->showEditModal = false;
        $this->success("Jatah cuti tahun {$this->editTahun} berhasil diperbarui menjadi {$this->editJatahCuti} hari.");
    }

    // --- Tambah Tahun Baru ---
    public function openCreateModal()
    {
        $this->createTahun = now()->year + 1;
        $this->createJatahCuti = 12;
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    public function createJatahCuti()
    {
        $this->validate([
            'createTahun' => 'required|integer|min:2020|max:2099|unique:jatah_cutis,tahun',
            'createJatahCuti' => 'required|integer|min:0|max:365',
        ], [
            'createTahun.required' => 'Tahun harus diisi.',
            'createTahun.unique' => 'Jatah cuti untuk tahun ini sudah ada.',
            'createJatahCuti.required' => 'Jatah cuti harus diisi.',
        ]);

        JatahCuti::create([
            'tahun' => $this->createTahun,
            'jatah_cuti' => $this->createJatahCuti,
        ]);

        $this->showCreateModal = false;
        $this->selectedTahun = $this->createTahun;
        $this->success("Jatah cuti tahun {$this->createTahun} berhasil ditambahkan.");
    }

    public function with(): array
    {
        $jatah = JatahCuti::getTahun($this->selectedTahun);

        // Daftar tahun yang tersedia
        $tahunOptions = JatahCuti::orderByDesc('tahun')->pluck('tahun')->map(fn($t) => [
            'id' => $t,
            'name' => (string) $t,
        ])->toArray();

        // Data karyawan aktif beserta pemakaian cuti
        $query = Karyawan::where('is_active', true)
            ->with('jabatan')
            ->orderBy('nama_karyawan');

        if ($this->search) {
            $query->where('nama_karyawan', 'like', "%{$this->search}%");
        }

        $karyawans = $query->get()->map(function ($k) use ($jatah) {
            $terpakai = JatahCuti::terpakaiByKaryawan($k->id, $this->selectedTahun);
            // Hitung pengajuan menunggu
            $menunggu = PengajuanAbsen::where('karyawan_id', $k->id)
                ->where('jenis', 'Cuti')
                ->where('status', 'Menunggu')
                ->get()
                ->sum(function ($p) use ($jatah) {
                    $tanggalArray = $p->tanggal ?? [];
                    return collect($tanggalArray)
                        ->filter(fn($tgl) => str_starts_with($tgl, (string) $this->selectedTahun))
                        ->count();
                });

            $sisa = max(0, $jatah->jatah_cuti - $terpakai - $menunggu);

            return (object) [
                'id' => $k->id,
                'nama_karyawan' => $k->nama_karyawan,
                'jabatan' => $k->jabatan?->nama_jabatan ?? '-',
                'jatah' => $jatah->jatah_cuti,
                'terpakai' => $terpakai,
                'menunggu' => $menunggu,
                'sisa' => $sisa,
            ];
        });

        // Statistik
        $totalKaryawan = $karyawans->count();
        $totalTerpakai = $karyawans->sum('terpakai');
        $sudahHabis = $karyawans->where('sisa', 0)->count();

        return [
            'jatah' => $jatah,
            'tahunOptions' => $tahunOptions,
            'karyawans' => $karyawans,
            'totalKaryawan' => $totalKaryawan,
            'totalTerpakai' => $totalTerpakai,
            'sudahHabis' => $sudahHabis,
        ];
    }
}; ?>

<div>
    <x-header title="Jatah & Sisa Cuti" separator progress-indicator>
        <x-slot:actions>
            <x-button wire:click="openCreateModal" label="Tambah Tahun" icon="o-plus" class="btn-primary shadow-sm" />
        </x-slot:actions>
    </x-header>

    {{-- Pilih Tahun & Info Jatah --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        {{-- Selector Tahun --}}
        <div class="bg-base-100 p-5 rounded-2xl border border-base-300 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                    <x-icon name="o-calendar" class="w-6 h-6 text-primary" />
                </div>
                <div class="flex-1">
                    <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider mb-1">Tahun</p>
                    <x-select wire:model.live="selectedTahun" :options="$tahunOptions" option-value="id" option-label="name" class="select-sm" />
                </div>
            </div>
        </div>

        {{-- Jatah Cuti --}}
        <div class="bg-base-100 p-5 rounded-2xl border border-base-300 shadow-sm cursor-pointer hover:border-primary/30 transition-colors" wire:click="openEditModal">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-success/10 flex items-center justify-center shrink-0">
                    <x-icon name="o-calendar-days" class="w-6 h-6 text-success" />
                </div>
                <div>
                    <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Jatah / Orang</p>
                    <p class="text-2xl font-bold text-success">{{ $jatah->jatah_cuti }} <span class="text-sm font-normal text-base-content/60">hari</span></p>
                </div>
                <x-icon name="o-pencil-square" class="w-4 h-4 text-base-content/30 ml-auto" />
            </div>
        </div>

        {{-- Total Terpakai --}}
        <div class="bg-base-100 p-5 rounded-2xl border border-base-300 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-warning/10 flex items-center justify-center shrink-0">
                    <x-icon name="o-clock" class="w-6 h-6 text-warning" />
                </div>
                <div>
                    <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Total Terpakai</p>
                    <p class="text-2xl font-bold text-warning">{{ $totalTerpakai }} <span class="text-sm font-normal text-base-content/60">hari</span></p>
                </div>
            </div>
        </div>

        {{-- Cuti Habis --}}
        <div class="bg-base-100 p-5 rounded-2xl border border-base-300 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-error/10 flex items-center justify-center shrink-0">
                    <x-icon name="o-exclamation-triangle" class="w-6 h-6 text-error" />
                </div>
                <div>
                    <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Cuti Habis</p>
                    <p class="text-2xl font-bold text-error">{{ $sudahHabis }} <span class="text-sm font-normal text-base-content/60">orang</span></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="mb-6">
        <x-input wire:model.live.debounce.300ms="search" label="Cari Karyawan" icon="o-magnifying-glass" placeholder="Nama karyawan..." clearable />
    </div>

    {{-- Tabel Karyawan & Cuti --}}
    <x-card class="border border-base-300 shadow-sm">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Karyawan</th>
                        <th class="text-center">Jatah</th>
                        <th class="text-center">Terpakai</th>
                        <th class="text-center">Menunggu</th>
                        <th class="text-center">Sisa</th>
                        <th class="text-center">Progress</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($karyawans as $idx => $k)
                        @php
                            $persen = $k->jatah > 0 ? round(($k->terpakai / $k->jatah) * 100) : 0;
                            $progressColor = match(true) {
                                $persen >= 100 => 'progress-error',
                                $persen >= 75 => 'progress-warning',
                                $persen >= 50 => 'progress-info',
                                default => 'progress-success',
                            };
                        @endphp
                        <tr class="hover {{ $k->sisa === 0 ? 'bg-error/5' : '' }}">
                            <td class="text-base-content/50">{{ $idx + 1 }}</td>
                            <td>
                                <div class="font-bold">{{ $k->nama_karyawan }}</div>
                                <div class="text-xs text-base-content/50">{{ $k->jabatan }}</div>
                            </td>
                            <td class="text-center font-bold">{{ $k->jatah }}</td>
                            <td class="text-center">
                                <span class="font-bold {{ $k->terpakai > 0 ? 'text-warning' : '' }}">{{ $k->terpakai }}</span>
                            </td>
                            <td class="text-center">
                                @if($k->menunggu > 0)
                                    <span class="badge badge-warning badge-sm font-bold">{{ $k->menunggu }}</span>
                                @else
                                    <span class="text-base-content/30">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="font-bold {{ $k->sisa === 0 ? 'text-error' : 'text-success' }}">{{ $k->sisa }}</span>
                            </td>
                            <td class="text-center min-w-32">
                                <div class="flex items-center gap-2">
                                    <progress class="progress {{ $progressColor }} w-full" value="{{ $persen }}" max="100"></progress>
                                    <span class="text-xs text-base-content/50 whitespace-nowrap">{{ $persen }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-base-content/50">
                                <x-icon name="o-users" class="w-10 h-10 mx-auto mb-2 opacity-30" />
                                Tidak ada karyawan aktif ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Modal Edit Jatah Cuti --}}
    <x-modal wire:model="showEditModal" title="Edit Jatah Cuti" subtitle="Tahun {{ $editTahun }}" box-class="max-w-sm">
        <form wire:submit="updateJatahCuti" class="space-y-4">
            <x-input
                label="Jatah Cuti (hari)"
                wire:model="editJatahCuti"
                type="number"
                min="0"
                max="365"
                icon="o-calendar-days"
                hint="Jumlah hari cuti per karyawan per tahun"
            />

            <x-slot:actions>
                <x-button label="Batal" @click="$wire.showEditModal = false" />
                <x-button type="submit" label="Simpan" icon="o-check" class="btn-primary" spinner="updateJatahCuti" />
            </x-slot:actions>
        </form>
    </x-modal>

    {{-- Modal Tambah Tahun --}}
    <x-modal wire:model="showCreateModal" title="Tambah Jatah Cuti Tahun Baru" box-class="max-w-sm">
        <form wire:submit="createJatahCuti" class="space-y-4">
            <x-input
                label="Tahun"
                wire:model="createTahun"
                type="number"
                min="2020"
                max="2099"
                icon="o-calendar"
            />

            <x-input
                label="Jatah Cuti (hari)"
                wire:model="createJatahCuti"
                type="number"
                min="0"
                max="365"
                icon="o-calendar-days"
                hint="Jumlah hari cuti per karyawan per tahun"
            />

            <x-slot:actions>
                <x-button label="Batal" @click="$wire.showCreateModal = false" />
                <x-button type="submit" label="Tambah" icon="o-plus" class="btn-primary" spinner="createJatahCuti" />
            </x-slot:actions>
        </form>
    </x-modal>
</div>
