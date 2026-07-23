<?php

use App\Imports\AbsenImport;
use App\Models\Absen;
use App\Models\Karyawan;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Mary\Traits\Toast;

new #[Lazy] class extends Component
{
    use Toast, WithFileUploads, WithPagination;

    public string $search = '';

    public string $filterTanggal = '';

    public string $filterKeterangan = '';

    public bool $collectiveModal = false;

    public array $selectedRows = [];

    public string $collectiveKeterangan = 'Hadir';

    public string $collectiveCatatan = '';

    public bool $detailModal = false;

    public ?int $detailAbsenId = null;

    public bool $importModal = false;

    public $importFile;

    public int $importSuccess = 0;

    public int $importDuplicate = 0;

    public array $importErrors = [];

    public bool $importDone = false;

    public array $keteranganOptions = [];

    public bool $showNoDataNotice = false;

    public function placeholder()
    {
        return <<<'HTML'
        <div class="flex justify-center items-center min-h-[32rem] bg-base-100/50 backdrop-blur-[1px] rounded-xl">
            <div class="flex flex-col items-center gap-2">
                <span class="loading loading-spinner loading-lg text-primary"></span>
                <span class="text-xs font-bold text-primary tracking-wider uppercase animate-pulse">Memuat halaman...</span>
            </div>
        </div>
        HTML;
    }

    public function boot(): void
    {
        if (empty($this->filterTanggal)) {
            $this->filterTanggal = now()->subDay()->format('Y-m-d');
        }

        $this->keteranganOptions = collect(['Hadir', 'Dinas Luar', 'Cuti', 'Sakit', 'Izin', 'Tidak Absen', 'Alpa', 'Off', 'Libur', 'Lainnya'])
            ->map(fn ($k) => ['id' => $k, 'name' => $k])
            ->toArray();

        $this->checkDataAvailability();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTanggal(): void
    {
        $this->resetPage();
    }

    public function updatedFilterTanggal(): void
    {
        $this->checkDataAvailability();
    }

    public function updatingFilterKeterangan(): void
    {
        $this->resetPage();
    }

    public function checkDataAvailability(): void
    {
        $hasData = Absen::where('tanggal_absen', $this->filterTanggal)->where(fn ($q) => $q->whereNotNull('scan_in')->orWhereNotNull('scan_out'))->exists();

        $this->showNoDataNotice = ! $hasData;
    }

    public function dismissNotice(): void
    {
        $this->showNoDataNotice = false;
    }

    public function openImportFromNotice(): void
    {
        $this->showNoDataNotice = false;
        $this->importModal = true;
    }

    protected function getFilteredKaryawanQuery()
    {
        $date = $this->filterTanggal;

        return Karyawan::with(['jabatan', 'user'])
            ->where('karyawans.is_active', true)
            ->where('karyawans.tanggal_masuk', '<=', $date)
            ->whereDoesntHave('nonaktifs', function ($q) use ($date) {
                $q->where('tanggal_nonaktif', '<=', $date)
                    ->where(function ($sub) use ($date) {
                        $sub->whereNull('tanggal_aktif')
                            ->orWhere('tanggal_aktif', '>', $date);
                    });
            })
            ->leftJoin('absens', function ($j) use ($date) {
                $j->on('absens.karyawan_id', '=', 'karyawans.id')->where('absens.tanggal_absen', $date);
            })
            ->select('karyawans.*', 'absens.id as absen_id', 'absens.tanggal_absen', 'absens.keterangan', 'absens.scan_in', 'absens.scan_out', 'absens.foto_in', 'absens.foto_out')
            ->when($this->filterKeterangan, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('absens.keterangan', $this->filterKeterangan)->orWhereNull('absens.keterangan');
                });
            })
            ->when($this->search, function ($q) {
                $term = trim($this->search);
                $q->where(function ($sub) use ($term) {
                    $sub->where('karyawans.nama_karyawan', 'like', "%{$term}%")->orWhere('karyawans.nik', 'like', "%{$term}%");
                });
            })
            ->orderBy('karyawans.nama_karyawan');
    }

    #[Computed]
    public function absens()
    {
        return $this->getFilteredKaryawanQuery()->paginate(10);
    }

    #[Computed]
    public function filteredKaryawanIds(): array
    {
        return $this->getFilteredKaryawanQuery()->pluck('karyawans.id')->toArray();
    }

    public function headers(): array
    {
        return [['key' => 'no', 'label' => '#', 'class' => 'w-1'], ['key' => 'karyawan', 'label' => 'KARYAWAN', 'sortable' => false], ['key' => 'tanggal', 'label' => 'TANGGAL', 'sortable' => false], ['key' => 'keterangan', 'label' => 'KETERANGAN', 'class' => 'w-48'], ['key' => 'scan_in', 'label' => 'CHECK IN', 'sortable' => false], ['key' => 'scan_out', 'label' => 'CHECK OUT', 'sortable' => false], ['key' => 'actions', 'label' => 'AKSI', 'class' => 'w-16']];
    }

    public function with(): array
    {
        $absens = $this->absens;
        $start = $absens->firstItem();
        $absens->getCollection()->transform(fn ($item, $i) => tap($item)->setAttribute('row_no', $start + $i));

        return [
            'absens' => $absens,
            'headers' => $this->headers(),
        ];
    }

    public function setKeterangan(int $karyawanId, string $value): void
    {
        Absen::updateOrCreate(['karyawan_id' => $karyawanId, 'tanggal_absen' => $this->filterTanggal], ['keterangan' => $value]);
    }

    public function setScanIn(int $karyawanId, ?string $value): void
    {
        $value = trim($value) === '' ? null : trim($value);

        $absen = Absen::where('karyawan_id', $karyawanId)
            ->where('tanggal_absen', $this->filterTanggal)
            ->first();

        $keterangan = $absen?->keterangan;
        if (! $keterangan) {
            $keterangan = $value ? 'Hadir' : 'Tidak Absen';
        }

        Absen::updateOrCreate(
            ['karyawan_id' => $karyawanId, 'tanggal_absen' => $this->filterTanggal],
            [
                'scan_in' => $value,
                'keterangan' => $keterangan,
            ],
        );

        $this->checkDataAvailability();
    }

    public function setScanOut(int $karyawanId, ?string $value): void
    {
        $value = trim($value) === '' ? null : trim($value);

        $absen = Absen::where('karyawan_id', $karyawanId)
            ->where('tanggal_absen', $this->filterTanggal)
            ->first();

        $keterangan = $absen?->keterangan;
        if (! $keterangan) {
            $keterangan = $value ? 'Hadir' : 'Tidak Absen';
        }

        Absen::updateOrCreate(
            ['karyawan_id' => $karyawanId, 'tanggal_absen' => $this->filterTanggal],
            [
                'scan_out' => $value,
                'keterangan' => $keterangan,
            ],
        );

        $this->checkDataAvailability();
    }

    public function toggleSelect(int $id): void
    {
        if (in_array($id, $this->selectedRows)) {
            $this->selectedRows = array_values(array_diff($this->selectedRows, [$id]));
        } else {
            $this->selectedRows[] = $id;
        }
    }

    public function selectAll(): void
    {
        $allIds = $this->filteredKaryawanIds;
        if (count($this->selectedRows) === count($allIds)) {
            $this->selectedRows = [];
        } else {
            $this->selectedRows = $allIds;
        }
    }

    public function isSelected(int $id): bool
    {
        return in_array($id, $this->selectedRows);
    }

    public function allSelected(): bool
    {
        $allIds = $this->filteredKaryawanIds;

        return count($allIds) > 0 && count($this->selectedRows) === count($allIds);
    }

    public function openCollective(): void
    {
        $this->collectiveKeterangan = 'Hadir';
        $this->collectiveCatatan = '';
        $this->collectiveModal = true;
    }

    public function saveCollective(): void
    {
        $this->validate([
            'collectiveKeterangan' => 'required|string|in:Hadir,Dinas Luar,Cuti,Sakit,Izin,Tidak Absen,Alpa,Off,Libur,Lainnya',
        ]);

        foreach ($this->selectedRows as $karyawanId) {
            $this->setKeterangan($karyawanId, $this->collectiveKeterangan);
        }

        $count = count($this->selectedRows);
        $this->success("Keterangan {$count} absensi diperbarui.", position: 'toast-top toast-end');
        $this->selectedRows = [];
        $this->collectiveModal = false;
    }

    public function detailAbsen()
    {
        if (! $this->detailAbsenId) {
            return null;
        }

        $absen = Absen::with('karyawan.jabatan')->where('karyawan_id', $this->detailAbsenId)->where('tanggal_absen', $this->filterTanggal)->first();

        if (! $absen) {
            $karyawan = Karyawan::with('jabatan')->find($this->detailAbsenId);
            if ($karyawan) {
                return (object) [
                    'id' => null,
                    'karyawan_id' => $karyawan->id,
                    'karyawan' => $karyawan,
                    'tanggal_absen' => Carbon::parse($this->filterTanggal),
                    'keterangan' => null,
                    'mode' => null,
                    'scan_in' => null,
                    'scan_out' => null,
                    'foto_in' => null,
                    'foto_out' => null,
                    'lat_in' => null,
                    'long_in' => null,
                    'lat_out' => null,
                    'long_out' => null,
                    'created_at' => null,
                    'updated_at' => null,
                ];
            }

            return null;
        }

        return $absen;
    }

    public function openDetail(int $id): void
    {
        $this->detailAbsenId = $id;
        $this->detailModal = true;
    }

    public function import(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,xlsx,xls|max:5120',
        ]);

        $import = new AbsenImport;
        Excel::import($import, $this->importFile->getRealPath());

        $this->importSuccess = $import->successCount;
        $this->importDuplicate = $import->duplicateCount;
        $this->importErrors = $import->errors;
        $this->importDone = true;

        $this->resetPage();

        if ($this->importSuccess > 0) {
            $this->success("{$this->importSuccess} data absensi berhasil diimport.", position: 'toast-top toast-end');
        }
    }

    public function resetImport(): void
    {
        $this->reset(['importFile', 'importSuccess', 'importDuplicate', 'importErrors', 'importDone', 'importModal']);
        $this->dispatch('reset-import');
    }

    public function closeModal(): void
    {
        $this->collectiveModal = false;
        $this->detailModal = false;
        $this->detailAbsenId = null;
        $this->selectedRows = [];
    }

    public static function getKeteranganColor(string $keterangan): string
    {
        return match ($keterangan) {
            'Hadir' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'Dinas Luar' => 'bg-sky-100 text-sky-700 border-sky-200',
            'Cuti' => 'bg-violet-100 text-violet-700 border-violet-200',
            'Sakit' => 'bg-amber-100 text-amber-700 border-amber-200',
            'Izin' => 'bg-slate-100 text-slate-600 border-slate-200',
            'Tidak Absen' => 'bg-orange-100 text-orange-700 border-orange-200',
            'Alpa' => 'bg-red-100 text-red-700 border-red-200',
            'Off' => 'bg-gray-100 text-gray-500 border-gray-200',
            'Libur' => 'bg-cyan-100 text-cyan-700 border-cyan-200',
            'Lainnya' => 'bg-pink-100 text-pink-700 border-pink-200',
            default => 'bg-gray-100 text-gray-500 border-gray-200',
        };
    }
};
?>

<div>
    <?php if (isset($component)) { $__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d = $attributes; } ?>
<?php $component = Mary\View\Components\Header::resolve(['title' => 'Kelola Absensi','separator' => true,'progressIndicator' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Header::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('middle', null, ['class' => '!justify-end']); ?> 
            <?php if (isset($component)) { $__componentOriginalf51438a7488970badd535e5f203e0c1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf51438a7488970badd535e5f203e0c1b = $attributes; } ?>
<?php $component = Mary\View\Components\Input::resolve(['clearable' => true,'icon' => 'o-magnifying-glass'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Input::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['placeholder' => 'Cari nama/NIK...','wire:model.live.debounce' => 'search']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf51438a7488970badd535e5f203e0c1b)): ?>
<?php $attributes = $__attributesOriginalf51438a7488970badd535e5f203e0c1b; ?>
<?php unset($__attributesOriginalf51438a7488970badd535e5f203e0c1b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf51438a7488970badd535e5f203e0c1b)): ?>
<?php $component = $__componentOriginalf51438a7488970badd535e5f203e0c1b; ?>
<?php unset($__componentOriginalf51438a7488970badd535e5f203e0c1b); ?>
<?php endif; ?>
         <?php $__env->endSlot(); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <?php if(\App\Models\FeatureFlag::isEnabled('collective_keterangan') && count($this->selectedRows) >= 2): ?>
                <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => 'Keterangan Kolektif ('.e(count($this->selectedRows)).')','icon' => 'o-users','spinner' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'btn-primary','wire:click' => 'openCollective']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
            <?php endif; ?>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d)): ?>
<?php $attributes = $__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d; ?>
<?php unset($__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d)): ?>
<?php $component = $__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d; ?>
<?php unset($__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d); ?>
<?php endif; ?>

    
    <div class="flex flex-wrap gap-3 mb-4">
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Tanggal</legend>
            <input type="date" class="input input-bordered input-sm w-44"
                wire:model.live="filterTanggal" />
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Keterangan</legend>
            <select class="select select-bordered select-sm w-48"
                wire:model.live="filterKeterangan">
                <option value="">Semua Keterangan</option>
                <option value="Hadir">Hadir</option>
                <option value="Dinas Luar">Dinas Luar</option>
                <option value="Cuti">Cuti</option>
                <option value="Sakit">Sakit</option>
                <option value="Izin">Izin</option>
                <option value="Tidak Absen">Tidak Absen</option>
                <option value="Alpa">Alpa</option>
                <option value="Off">Off</option>
                <option value="Libur">Libur</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </fieldset>
        <div class="flex items-end gap-2 ml-auto">
            <button class="btn btn-ghost btn-sm"
                onclick="window.location.href='<?php echo e(route('absen.template')); ?>'">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-document-arrow-down'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                Template
            </button>
            <?php if(\App\Models\FeatureFlag::isEnabled('import_absen')): ?>
            <button class="btn btn-outline btn-sm btn-warning"
                wire:click="$set('importModal', true)" spinner>
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-arrow-up-tray'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                Import
            </button>
            <?php endif; ?>
            <button class="btn btn-primary btn-sm"
                wire:click="$set('filterTanggal', '<?php echo e(now()->format('Y-m-d')); ?>')">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-calendar-days'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                Hari Ini
            </button>
            <button class="btn btn-secondary btn-sm"
                wire:click="$set('filterTanggal', '<?php echo e(now()->subDay()->format('Y-m-d')); ?>')">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-arrow-uturn-left'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                Kemarin
            </button>
        </div>
    </div>

    
    <?php if($showNoDataNotice): ?>
        <div class="alert alert-warning shadow-sm mb-4">
            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-exclamation-triangle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-6 h-6']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
            <div>
                <p class="font-bold">Belum ada data absensi</p>
                <p class="text-sm">
                    Tanggal <?php echo e(\Carbon\Carbon::parse($this->filterTanggal)->format('d M Y')); ?>

                    belum ada transaksi absen dari mesin.
                </p>
            </div>
            <div class="flex gap-2">
                <button class="btn btn-indigo btn-sm" wire:click="openImportFromNotice">
                    Import Sekarang
                </button>
                <button class="btn btn-ghost btn-sm" wire:click="dismissNotice">
                    Abai
                </button>
            </div>
        </div>
    <?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginal7f194736b6f6432dc38786f292496c34 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7f194736b6f6432dc38786f292496c34 = $attributes; } ?>
<?php $component = Mary\View\Components\Card::resolve(['shadow' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Card::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="relative min-h-[32rem]">
            
            <div wire:loading wire:target="search, filterTanggal, filterKeterangan, gotoPage, nextPage, previousPage" class="absolute inset-0 bg-base-100/30 backdrop-blur-[1px] z-50 rounded-xl transition-all duration-150">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center gap-2">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                    <span class="text-xs font-bold text-primary tracking-wider uppercase animate-pulse">Memuat...</span>
                </div>
            </div>

            <div wire:loading.class="opacity-25 pointer-events-none" wire:target="search, filterTanggal, filterKeterangan, gotoPage, nextPage, previousPage" class="transition-opacity duration-150">
                <?php if (isset($component)) { $__componentOriginal8fbd727209323874b055feef49197909 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8fbd727209323874b055feef49197909 = $attributes; } ?>
<?php $component = Mary\View\Components\Table::resolve(['headers' => $headers,'rows' => $absens,'withPagination' => true,'showEmptyText' => true,'emptyText' => 'Tidak ada data ditemukan'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Table::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('header_no', function($header) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="checkbox checkbox-sm checkbox-primary"
                            wire:click="selectAll()" <?php echo e($this->allSelected() ? 'checked' : ''); ?> />
                        <span class="text-xs"><?php echo e($header['label']); ?></span>
                    </label>
                <?php }); ?>

                <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_no', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="checkbox checkbox-sm checkbox-primary"
                            wire:change="toggleSelect(<?php echo e($row->id); ?>)"
                            <?php echo e(in_array($row->id, $this->selectedRows) ? 'checked' : ''); ?> />
                        <span class="text-sm text-base-content/50"><?php echo e($row->row_no); ?></span>
                    </label>
                <?php }); ?>

                <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_karyawan', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                    <div class="flex items-center gap-3">
                        <div class="avatar <?php echo e(!$row->foto_karyawan ? 'placeholder' : ''); ?>">
                            <div class="mask mask-squircle w-10 h-10 <?php echo e(!$row->foto_karyawan ? 'bg-gradient-to-br from-primary/20 to-primary/10 text-primary border border-primary/20 flex items-center justify-center font-bold text-xs' : ''); ?>">
                                <?php if($row->foto_karyawan): ?>
                                    <img src="<?php echo e(Storage::url($row->foto_karyawan)); ?>" alt="<?php echo e($row->nama_karyawan); ?>" />
                                <?php else: ?>
                                    <span><?php echo e(strtoupper(substr($row->nama_karyawan, 0, 2))); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <div class="font-bold text-sm"><?php echo e($row->nama_karyawan); ?></div>
                            <div class="text-xs text-base-content/50">
                                <?php echo e($row->jabatan?->nama_jabatan ?? '-'); ?></div>
                        </div>
                    </div>
                <?php }); ?>

                <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_tanggal', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                    <span
                        class="text-sm"><?php echo e(\Carbon\Carbon::parse($row->tanggal_absen ?? $this->filterTanggal)->format('d M Y')); ?></span>
                <?php }); ?>

                <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_keterangan', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                    <div x-data="{ val: '<?php echo e($row->keterangan ?? ''); ?>' }" class="inline-block">
                        <select x-model="val" class="select select-sm w-40 border cursor-pointer"
                            :class="{
                                'bg-gray-100 text-gray-400 border-gray-200': val === '',
                                'bg-emerald-100 text-emerald-700 border-emerald-200': val === 'Hadir',
                                'bg-sky-100 text-sky-700 border-sky-200': val === 'Dinas Luar',
                                'bg-violet-100 text-violet-700 border-violet-200': val === 'Cuti',
                                'bg-amber-100 text-amber-700 border-amber-200': val === 'Sakit',
                                'bg-slate-100 text-slate-600 border-slate-200': val === 'Izin',
                                'bg-orange-100 text-orange-700 border-orange-200': val === 'Tidak Absen',
                                'bg-red-100 text-red-700 border-red-200': val === 'Alpa',
                                'bg-gray-100 text-gray-500 border-gray-200': val === 'Off',
                                'bg-cyan-100 text-cyan-700 border-cyan-200': val === 'Libur',
                                'bg-pink-100 text-pink-700 border-pink-200': val === 'Lainnya'
                            }"
                            wire:ignore.self
                            @change="$wire.setKeterangan(<?php echo e($row->id); ?>, $event.target.value)">
                            <option value="">Pilih Keterangan</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['Hadir', 'Dinas Luar', 'Cuti', 'Sakit', 'Izin', 'Tidak Absen', 'Alpa', 'Off', 'Libur', 'Lainnya']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($opt); ?>"><?php echo e($opt); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    </div>
                <?php }); ?>

                <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_scan_in', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                    <?php if(\App\Models\FeatureFlag::isEnabled('manual_checkin_edit')): ?>
                        <div x-data="timeCell(<?php echo e($row->id); ?>, 'in', '<?php echo e($row->scan_in ? substr($row->scan_in, 0, 5) : ''); ?>')"
                             class="flex items-center gap-1.5 group relative">
                            <input type="time" x-model="val"
                                class="input input-sm border font-mono w-28 transition-all"
                                :class="{
                                    'border-primary ring-1 ring-primary/30 bg-base-100': focused,
                                    'border-success bg-success/5': status === 'saved' && !focused,
                                    'border-error bg-error/5': status === 'error' && !focused,
                                    'border-base-200 bg-base-100/30 hover:bg-base-100': status !== 'saved' && status !== 'error' && !focused
                                }"
                                @focus="focused = true"
                                @blur="focused = false; save()"
                                @input.debounce.600ms="save()"
                                @keydown.enter.prevent="save(); $el.blur(); $el.closest('tr')?.querySelector('[data-scan-out] input')?.focus()"
                                @keydown.tab="save()" />
                            
                            <div class="w-4 h-4 flex items-center justify-center">
                                <template x-if="status === 'saving'">
                                    <span class="loading loading-spinner loading-xs text-primary"></span>
                                </template>
                                <template x-if="status === 'saved'">
                                    <svg class="w-4 h-4 text-success animate-in fade-in" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                </template>
                                <template x-if="status === 'error'">
                                    <svg class="w-4 h-4 text-error cursor-pointer" @click="save()" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" title="Gagal menyimpan, klik untuk retry">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                </template>
                            </div>
                        </div>
                    <?php else: ?>
                        <span class="font-mono text-sm pl-3 text-base-content/75"><?php echo e($row->scan_in ? substr($row->scan_in, 0, 5) : '-'); ?></span>
                    <?php endif; ?>
                <?php }); ?>

                <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_scan_out', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                    <?php if(\App\Models\FeatureFlag::isEnabled('manual_checkout_edit')): ?>
                        <div x-data="timeCell(<?php echo e($row->id); ?>, 'out', '<?php echo e($row->scan_out ? substr($row->scan_out, 0, 5) : ''); ?>')"
                             data-scan-out
                             class="flex items-center gap-1.5 group relative">
                            <input type="time" x-model="val"
                                class="input input-sm border font-mono w-28 transition-all"
                                :class="{
                                    'border-primary ring-1 ring-primary/30 bg-base-100': focused,
                                    'border-success bg-success/5': status === 'saved' && !focused,
                                    'border-error bg-error/5': status === 'error' && !focused,
                                    'border-base-200 bg-base-100/30 hover:bg-base-100': status !== 'saved' && status !== 'error' && !focused
                                }"
                                @focus="focused = true"
                                @blur="focused = false; save()"
                                @input.debounce.600ms="save()"
                                @keydown.enter.prevent="save(); $el.blur()"
                                @keydown.tab="save()" />
                            
                            <div class="w-4 h-4 flex items-center justify-center">
                                <template x-if="status === 'saving'">
                                    <span class="loading loading-spinner loading-xs text-primary"></span>
                                </template>
                                <template x-if="status === 'saved'">
                                    <svg class="w-4 h-4 text-success animate-in fade-in" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                    </svg>
                                </template>
                                <template x-if="status === 'error'">
                                    <svg class="w-4 h-4 text-error cursor-pointer" @click="save()" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" title="Gagal menyimpan, klik untuk retry">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                </template>
                            </div>
                        </div>
                    <?php else: ?>
                        <span class="font-mono text-sm pl-3 text-base-content/75"><?php echo e($row->scan_out ? substr($row->scan_out, 0, 5) : '-'); ?></span>
                    <?php endif; ?>
                <?php }); ?>

                <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('actions', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                    <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-eye','spinner' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'openDetail('.e($row->id).')','class' => 'btn-ghost btn-sm text-primary']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
                <?php }); ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8fbd727209323874b055feef49197909)): ?>
<?php $attributes = $__attributesOriginal8fbd727209323874b055feef49197909; ?>
<?php unset($__attributesOriginal8fbd727209323874b055feef49197909); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8fbd727209323874b055feef49197909)): ?>
<?php $component = $__componentOriginal8fbd727209323874b055feef49197909; ?>
<?php unset($__componentOriginal8fbd727209323874b055feef49197909); ?>
<?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7f194736b6f6432dc38786f292496c34)): ?>
<?php $attributes = $__attributesOriginal7f194736b6f6432dc38786f292496c34; ?>
<?php unset($__attributesOriginal7f194736b6f6432dc38786f292496c34); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7f194736b6f6432dc38786f292496c34)): ?>
<?php $component = $__componentOriginal7f194736b6f6432dc38786f292496c34; ?>
<?php unset($__componentOriginal7f194736b6f6432dc38786f292496c34); ?>
<?php endif; ?>

    
    <div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'detail-modal-wrap'; ?>wire:key="detail-modal-wrap">
        <?php if (isset($component)) { $__componentOriginal89a573612f1f1cb2dd9fc072235d4356 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356 = $attributes; } ?>
<?php $component = Mary\View\Components\Modal::resolve(['title' => 'Detail Absensi','boxClass' => '!max-w-md'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Modal::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'detailModal']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php $d = $detailModal ? $this->detailAbsen() : null; ?>
            <?php if($d): ?>
                <div class="space-y-4">
                    
                    <div class="flex items-center gap-3">
                        <div class="avatar <?php echo e(!$d->karyawan->foto_karyawan ? 'placeholder' : ''); ?>">
                            <div class="mask mask-squircle w-11 h-11 <?php echo e(!$d->karyawan->foto_karyawan ? 'bg-gradient-to-br from-primary/20 to-primary/10 text-primary border border-primary/20 flex items-center justify-center font-bold text-xs' : ''); ?>">
                                <?php if($d->karyawan->foto_karyawan): ?>
                                    <img src="<?php echo e(Storage::url($d->karyawan->foto_karyawan)); ?>" alt="<?php echo e($d->karyawan->nama_karyawan); ?>" />
                                <?php else: ?>
                                    <span><?php echo e(strtoupper(substr($d->karyawan->nama_karyawan, 0, 2))); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <div class="font-bold text-sm"><?php echo e($d->karyawan->nama_karyawan); ?></div>
                            <div class="text-xs text-base-content/50"><?php echo e($d->karyawan->nik); ?> · <?php echo e($d->karyawan->jabatan?->nama_jabatan ?? '-'); ?></div>
                        </div>
                    </div>

                    <?php if(is_null($d->id)): ?>
                        <div class="alert alert-warning text-xs py-2 px-3 shadow-none">
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-exclamation-triangle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                            <span>Karyawan belum melakukan absensi pada tanggal ini.</span>
                        </div>
                    <?php endif; ?>

                    
                    <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-xs border-t border-b border-base-200 py-3">
                        <div>
                            <span class="text-base-content/40 block mb-0.5">Tanggal</span>
                            <span class="font-medium text-base-content"><?php echo e(\Carbon\Carbon::parse($d->tanggal_absen ?? $this->filterTanggal)->translatedFormat('d F Y')); ?></span>
                        </div>
                        <div>
                            <span class="text-base-content/40 block mb-0.5">Keterangan</span>
                            <?php if($d->keterangan): ?>
                                <span class="inline-block px-2 py-0.5 rounded-full font-medium border <?php echo e($this->getKeteranganColor($d->keterangan)); ?>">
                                    <?php echo e($d->keterangan); ?>

                                </span>
                            <?php else: ?>
                                <span class="inline-block px-2 py-0.5 rounded-full font-medium bg-base-200 text-base-content/55">
                                    Belum Absen
                                </span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="text-base-content/40 block mb-0.5">Check In</span>
                            <div class="flex items-center gap-1 font-mono font-semibold <?php echo e($d->scan_in ? 'text-base-content' : 'text-base-content/30'); ?>">
                                <?php echo e($d->scan_in ?? '-'); ?>

                                <?php if($d->lat_in && $d->long_in): ?>
                                    <a href="https://www.google.com/maps?q=<?php echo e($d->lat_in); ?>,<?php echo e($d->long_in); ?>" target="_blank" class="text-success hover:underline flex items-center gap-0.5 ml-1">
                                        <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-map-pin'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-3 h-3']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?> GPS
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <span class="text-base-content/40 block mb-0.5">Check Out</span>
                            <div class="flex items-center gap-1 font-mono font-semibold <?php echo e($d->scan_out ? 'text-base-content' : 'text-base-content/30'); ?>">
                                <?php echo e($d->scan_out ?? '-'); ?>

                                <?php if($d->lat_out && $d->long_out): ?>
                                    <a href="https://www.google.com/maps?q=<?php echo e($d->lat_out); ?>,<?php echo e($d->long_out); ?>" target="_blank" class="text-info hover:underline flex items-center gap-0.5 ml-1">
                                        <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-map-pin'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-3 h-3']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?> GPS
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if($d->mode): ?>
                            <div>
                                <span class="text-base-content/40 block mb-0.5">Metode</span>
                                <span class="font-medium text-base-content/70">
                                    <?php echo e($d->mode === 'face' ? 'Face Recognition' : 'Upload / Mesin'); ?>

                                </span>
                            </div>
                        <?php endif; ?>
                        <?php if($d->scan_in && $d->scan_out): ?>
                            <?php
                                $in = \Carbon\Carbon::parse($d->scan_in);
                                $out = \Carbon\Carbon::parse($d->scan_out);
                                $diff = $in->diff($out);
                                $durasi = ($diff->h > 0 ? $diff->h . ' jam ' : '') . $diff->i . ' menit';
                            ?>
                            <div>
                                <span class="text-base-content/40 block mb-0.5">Durasi Kerja</span>
                                <span class="font-medium text-base-content/80"><?php echo e($durasi); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    
                    <?php if($d->foto_in || $d->foto_out): ?>
                        <div class="space-y-1.5">
                            <span class="text-base-content/40 block">Foto Absen</span>
                            <div class="flex gap-3">
                                <?php if($d->foto_in): ?>
                                    <div class="w-1/2 space-y-1">
                                        <span class="text-[10px] text-base-content/50 block">Check In</span>
                                        <img src="<?php echo e(Storage::url($d->foto_in)); ?>" class="w-full h-28 object-cover rounded-lg border border-base-200" />
                                    </div>
                                <?php endif; ?>
                                <?php if($d->foto_out): ?>
                                    <div class="w-1/2 space-y-1">
                                        <span class="text-[10px] text-base-content/50 block">Check Out</span>
                                        <img src="<?php echo e(Storage::url($d->foto_out)); ?>" class="w-full h-28 object-cover rounded-lg border border-base-200" />
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($d->created_at): ?>
                        <div class="text-[10px] text-base-content/30 flex justify-between pt-1">
                            <span>Dibuat: <?php echo e($d->created_at->format('d/m/Y H:i')); ?></span>
                            <span>Diperbarui: <?php echo e($d->updated_at->format('d/m/Y H:i')); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
             <?php $__env->slot('actions', null, []); ?> 
                <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => 'Tutup'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'closeModal','type' => 'button']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal89a573612f1f1cb2dd9fc072235d4356)): ?>
<?php $attributes = $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356; ?>
<?php unset($__attributesOriginal89a573612f1f1cb2dd9fc072235d4356); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal89a573612f1f1cb2dd9fc072235d4356)): ?>
<?php $component = $__componentOriginal89a573612f1f1cb2dd9fc072235d4356; ?>
<?php unset($__componentOriginal89a573612f1f1cb2dd9fc072235d4356); ?>
<?php endif; ?>
    </div>

    
    <div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'import-modal-wrap'; ?>wire:key="import-modal-wrap" x-data="{
        fileName: '',
        selectedFile: null,
        uploading: false,
        progress: 0,
        doImport() {
            if (!this.selectedFile) {
                alert('Pilih file terlebih dahulu.');
                return;
            }
            this.uploading = true;
            this.progress = 0;
            $wire.upload(
                'importFile',
                this.selectedFile,
                () => {
                    this.uploading = false;
                    $wire.import();
                },
                () => { this.uploading = false; },
                (event) => { this.progress = event.detail.progress; }
            );
        }
    }"
        @reset-import.window="fileName = ''; selectedFile = null; uploading = false; progress = 0;"
        @do-import.window="doImport()"
        @import-file-selected.window="selectedFile = $event.detail.file; fileName = $event.detail.name;">
        <?php if (isset($component)) { $__componentOriginal89a573612f1f1cb2dd9fc072235d4356 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356 = $attributes; } ?>
<?php $component = Mary\View\Components\Modal::resolve(['title' => 'Import Absensi','boxClass' => '!max-w-md'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Modal::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'importModal']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <div class="space-y-4">
                <?php if (! ($importDone)): ?>
                    <div>
                        <label class="label">
                            <span class="label-text">Pilih file CSV/Excel</span>
                        </label>

                        
                        <label
                            class="flex items-center gap-3 w-full cursor-pointer border-2 border-dashed border-base-300 rounded-xl p-4 hover:border-primary transition-colors"
                            x-data="{ localName: '' }"
                            :class="localName ? 'border-primary bg-primary/5' : ''"
                            @reset-import.window="localName = ''">
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-document-arrow-up'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8 text-primary shrink-0']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate"
                                    x-text="localName || 'Klik untuk memilih file'"></p>
                                <p class="text-xs text-base-content/40" x-show="!localName">CSV, XLSX,
                                    atau XLS — maks. 5 MB</p>
                                <p class="text-xs text-success" x-show="localName" x-cloak>File siap
                                    diimport</p>
                            </div>
                            <input type="file" accept=".csv,.xlsx,.xls" class="hidden"
                                @change="const f = $event.target.files[0]; if(f){ localName = f.name; window.dispatchEvent(new CustomEvent('import-file-selected', { detail: { file: f, name: f.name } })); } else { localName = ''; }" />
                        </label>

                        
                        <div class="mt-3" x-show="uploading" x-cloak>
                            <div class="flex justify-between text-xs text-base-content/60 mb-1">
                                <span>Mengunggah file…</span>
                                <span x-text="progress + '%'"></span>
                            </div>
                            <progress class="progress progress-primary w-full" :value="progress"
                                max="100"></progress>
                        </div>

                        <?php $__errorArgs = ['importFile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-500 text-xs mt-1 block"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                        <div class="text-xs text-base-content/50 mt-3 space-y-1">
                            <p>Format kolom: <code>No. ID</code>, <code>Tanggal</code>, <code>Scan
                                    Masuk</code>, <code>Scan Pulang</code></p>
                            <p>No. ID = PIN mesin absen</p>
                            <p>Tanggal = dd/mm/YYYY</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-success">
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-check-circle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-6 h-6']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                            <span class="font-medium"><?php echo e($importSuccess); ?> data berhasil
                                diimport</span>
                        </div>
                        <?php if($importDuplicate): ?>
                            <div class="flex items-center gap-2 text-warning">
                                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-exclamation-triangle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-6 h-6']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                                <span class="font-medium"><?php echo e($importDuplicate); ?> data duplikat
                                    dilewati</span>
                            </div>
                        <?php endif; ?>
                        <?php if(count($importErrors)): ?>
                            <div>
                                <span class="text-sm text-base-content/50 font-medium">Gagal
                                    (<?php echo e(count($importErrors)); ?>):</span>
                                <ul class="list-disc list-inside text-sm text-red-500 mt-1">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $importErrors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <li><?php echo e($err); ?></li>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
             <?php $__env->slot('actions', null, []); ?> 
                <?php if (! ($importDone)): ?>
                    <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => 'Batal'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'resetImport','type' => 'button']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
                    
                    <button class="btn btn-primary" id="btn-import-submit"
                        wire:loading.attr="disabled" wire:target="import"
                        onclick="window.dispatchEvent(new CustomEvent('do-import'))">
                        <span wire:loading.remove wire:target="import">Import</span>
                        <span wire:loading wire:target="import"
                            class="loading loading-spinner loading-sm"></span>
                        <span wire:loading wire:target="import">Memproses…</span>
                    </button>
                <?php else: ?>
                    <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => 'Tutup'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'resetImport','class' => 'btn-primary']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
                <?php endif; ?>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal89a573612f1f1cb2dd9fc072235d4356)): ?>
<?php $attributes = $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356; ?>
<?php unset($__attributesOriginal89a573612f1f1cb2dd9fc072235d4356); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal89a573612f1f1cb2dd9fc072235d4356)): ?>
<?php $component = $__componentOriginal89a573612f1f1cb2dd9fc072235d4356; ?>
<?php unset($__componentOriginal89a573612f1f1cb2dd9fc072235d4356); ?>
<?php endif; ?>
    </div>

    
    <div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'collective-modal-wrap'; ?>wire:key="collective-modal-wrap">
        <?php if (isset($component)) { $__componentOriginal89a573612f1f1cb2dd9fc072235d4356 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356 = $attributes; } ?>
<?php $component = Mary\View\Components\Modal::resolve(['title' => 'Keterangan Kolektif','subtitle' => 'Ubah keterangan untuk '.e(count($this->selectedRows)).' absensi terpilih','boxClass' => '!max-w-md'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Modal::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'collectiveModal']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <div class="space-y-4">
                <?php if (isset($component)) { $__componentOriginald64144c2287634503c73cd4803d6e578 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald64144c2287634503c73cd4803d6e578 = $attributes; } ?>
<?php $component = Mary\View\Components\Select::resolve(['label' => 'Pilih Keterangan','options' => $keteranganOptions,'optionValue' => 'id','optionLabel' => 'name'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Select::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'collectiveKeterangan']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald64144c2287634503c73cd4803d6e578)): ?>
<?php $attributes = $__attributesOriginald64144c2287634503c73cd4803d6e578; ?>
<?php unset($__attributesOriginald64144c2287634503c73cd4803d6e578); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald64144c2287634503c73cd4803d6e578)): ?>
<?php $component = $__componentOriginald64144c2287634503c73cd4803d6e578; ?>
<?php unset($__componentOriginald64144c2287634503c73cd4803d6e578); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalf51438a7488970badd535e5f203e0c1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf51438a7488970badd535e5f203e0c1b = $attributes; } ?>
<?php $component = Mary\View\Components\Input::resolve(['label' => 'Catatan Tambahan (Opsional)'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Input::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'collectiveCatatan','placeholder' => 'Contoh: Penugasan proyek di luar kota...']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf51438a7488970badd535e5f203e0c1b)): ?>
<?php $attributes = $__attributesOriginalf51438a7488970badd535e5f203e0c1b; ?>
<?php unset($__attributesOriginalf51438a7488970badd535e5f203e0c1b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf51438a7488970badd535e5f203e0c1b)): ?>
<?php $component = $__componentOriginalf51438a7488970badd535e5f203e0c1b; ?>
<?php unset($__componentOriginalf51438a7488970badd535e5f203e0c1b); ?>
<?php endif; ?>
                <div class="alert alert-warning text-sm">
                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-exclamation-triangle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                    <span>Tindakan ini akan menimpa status absensi saat ini untuk semua karyawan
                        yang dipilih.</span>
                </div>
            </div>
             <?php $__env->slot('actions', null, []); ?> 
                <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => 'Batal'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'closeModal','type' => 'button']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => 'Simpan Perubahan','spinner' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'btn-primary','wire:click' => 'saveCollective']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal89a573612f1f1cb2dd9fc072235d4356)): ?>
<?php $attributes = $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356; ?>
<?php unset($__attributesOriginal89a573612f1f1cb2dd9fc072235d4356); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal89a573612f1f1cb2dd9fc072235d4356)): ?>
<?php $component = $__componentOriginal89a573612f1f1cb2dd9fc072235d4356; ?>
<?php unset($__componentOriginal89a573612f1f1cb2dd9fc072235d4356); ?>
<?php endif; ?>
    </div>

    
        <?php
        $__scriptKey = '679456713-0';
        ob_start();
    ?>
    <script>
        Alpine.data('timeCell', (karyawanId, type, initial) => ({
            val: initial,
            lastSaved: initial,
            status: 'idle',  // idle | saving | saved | error
            focused: false,
            _clearTimer: null,

            save() {
                // Skip if value hasn't changed from last saved
                if (this.val === this.lastSaved) return;

                // Clear any pending success-clear timer
                if (this._clearTimer) clearTimeout(this._clearTimer);

                this.status = 'saving';
                const method = type === 'in' ? 'setScanIn' : 'setScanOut';

                $wire.call(method, karyawanId, this.val).then(() => {
                    this.lastSaved = this.val;
                    this.status = 'saved';
                    // Auto-clear success after 2s
                    this._clearTimer = setTimeout(() => {
                        if (this.status === 'saved') this.status = 'idle';
                    }, 2000);
                }).catch(() => {
                    this.status = 'error';
                });
            },

            destroy() {
                if (this._clearTimer) clearTimeout(this._clearTimer);
            }
        }));
    </script>
        <?php
        $__output = ob_get_clean();

        \Livewire\store($this)->push('scripts', $__output, $__scriptKey)
    ?>

</div>
<?php /**PATH C:\laragon\www\absenhub-v2.0\resources\views\pages\absen\kelola-absen.blade.php ENDPATH**/ ?>