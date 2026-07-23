<?php

use App\Models\Absen;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component
{
    use Toast, WithPagination;

    public string $search = '';

    public string $filterTanggalAwal = '';

    public string $filterTanggalAkhir = '';

    public string $filterKeterangan = '';

    public string $dateError = '';

    public bool $showData = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterTanggalAwal(): void
    {
        $this->filterTanggalAkhir = '';
        $this->showData = false;
        $this->dateError = '';
        $this->resetPage();
    }

    public function updatedFilterTanggalAkhir(): void
    {
        $this->showData = false;
        $this->dateError = '';
        $this->resetPage();
    }

    public function submitFilter(): void
    {
        $this->dateError = '';

        if (empty($this->filterTanggalAwal)) {
            $this->dateError = 'Tanggal awal wajib diisi.';
            $this->showData = false;

            return;
        }

        if (empty($this->filterTanggalAkhir)) {
            $this->dateError = 'Tanggal akhir wajib diisi.';
            $this->showData = false;

            return;
        }

        if ($this->filterTanggalAwal > $this->filterTanggalAkhir) {
            $this->dateError = 'Tanggal akhir tidak boleh sebelum tanggal awal.';
            $this->showData = false;

            return;
        }

        if (Carbon::parse($this->filterTanggalAwal)->diffInDays($this->filterTanggalAkhir) > 31) {
            $this->dateError = 'Maksimal rentang tanggal adalah 31 hari. Silakan perkecil rentang tanggal.';
            $this->showData = false;

            return;
        }

        $this->showData = true;
        $this->resetPage();
    }

    public function setHariIni(): void
    {
        $today = now()->format('Y-m-d');
        $this->filterTanggalAwal = $today;
        $this->filterTanggalAkhir = $today;
        $this->submitFilter();
    }

    public function setKemarin(): void
    {
        $yesterday = now()->subDay()->format('Y-m-d');
        $this->filterTanggalAwal = $yesterday;
        $this->filterTanggalAkhir = $yesterday;
        $this->submitFilter();
    }

    public function updatingFilterKeterangan(): void
    {
        $this->resetPage();
    }

    protected function getFilteredQuery()
    {
        if (! $this->showData || empty($this->filterTanggalAwal) || empty($this->filterTanggalAkhir)) {
            return Absen::with('karyawan.jabatan')->whereRaw('0=1');
        }

        return Absen::with('karyawan.jabatan')
            ->whereBetween('tanggal_absen', [$this->filterTanggalAwal, $this->filterTanggalAkhir])
            ->when($this->filterKeterangan, fn ($q) => $q->where('keterangan', $this->filterKeterangan))
            ->when($this->search, function ($q) {
                $term = trim($this->search);
                $q->whereHas('karyawan', function ($kq) use ($term) {
                    $kq->where(function ($sub) use ($term) {
                        $sub->where('nama_karyawan', 'like', "%{$term}%")
                            ->orWhere('nik', 'like', "%{$term}%");
                    });
                });
            })
            ->orderBy('tanggal_absen', 'desc')
            ->orderBy('karyawan_id');
    }

    #[Computed]
    public function absens()
    {
        return $this->getFilteredQuery()->paginate(10);
    }

    #[Computed]
    public function rekap()
    {
        if (! $this->showData || empty($this->filterTanggalAwal) || empty($this->filterTanggalAkhir)) {
            return ['alpa' => 0, 'sakit' => 0, 'cuti' => 0, 'izin' => 0];
        }

        $query = Absen::whereBetween('tanggal_absen', [$this->filterTanggalAwal, $this->filterTanggalAkhir]);

        if ($this->search) {
            $term = trim($this->search);
            $query->whereHas('karyawan', function ($kq) use ($term) {
                $kq->where(function ($sub) use ($term) {
                    $sub->where('nama_karyawan', 'like', "%{$term}%")
                        ->orWhere('nik', 'like', "%{$term}%");
                });
            });
        }

        return [
            'alpa' => (clone $query)->where('keterangan', 'Alpa')->count(),
            'sakit' => (clone $query)->where('keterangan', 'Sakit')->count(),
            'cuti' => (clone $query)->where('keterangan', 'Cuti')->count(),
            'izin' => (clone $query)->where('keterangan', 'Izin')->count(),
        ];
    }

    public function headers(): array
    {
        return [
            ['key' => 'no', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'karyawan', 'label' => 'KARYAWAN', 'sortable' => false],
            ['key' => 'tanggal', 'label' => 'TANGGAL', 'sortable' => false],
            ['key' => 'keterangan', 'label' => 'KETERANGAN', 'class' => 'w-36'],
            ['key' => 'scan_in', 'label' => 'CHECK IN', 'sortable' => false],
            ['key' => 'scan_out', 'label' => 'CHECK OUT', 'sortable' => false],
            ['key' => 'terlambat', 'label' => 'TERLAMBAT', 'class' => 'w-28', 'sortable' => false],
        ];
    }

    public function with(): array
    {
        $absens = $this->absens;
        $start = $absens->firstItem() ?? 1;
        $absens->getCollection()->transform(fn ($item, $i) => tap($item)->setAttribute('row_no', $start + $i));

        return [
            'absens' => $absens,
            'headers' => $this->headers(),
            'rekap' => $this->rekap,
        ];
    }

    public static function getColor(string $keterangan): string
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
<?php $component = Mary\View\Components\Header::resolve(['title' => 'Lihat Absensi','separator' => true,'progressIndicator' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
            <?php if($showData && $filterTanggalAwal && $filterTanggalAkhir): ?>
                <a href="<?php echo e(route('absen.lihat.pdf', [
                    'tanggal_awal' => $filterTanggalAwal,
                    'tanggal_akhir' => $filterTanggalAkhir,
                    'keterangan' => $filterKeterangan,
                    'search' => $search
                ])); ?>" target="_blank" class="btn btn-error btn-sm text-white">
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
                    Export PDF (F4)
                </a>
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

    
    <div class="flex flex-wrap gap-4 items-end mb-6 bg-base-200/50 p-4 rounded-xl border border-base-200">
        <fieldset class="fieldset p-0 m-0">
            <legend class="fieldset-legend text-xs font-semibold">Tanggal Awal</legend>
            <input type="date" class="input input-bordered input-sm w-44" wire:model.live="filterTanggalAwal" />
        </fieldset>
        <fieldset class="fieldset p-0 m-0">
            <legend class="fieldset-legend text-xs font-semibold">Tanggal Akhir</legend>
            <input type="date" class="input input-bordered input-sm w-44" wire:model.live="filterTanggalAkhir" />
        </fieldset>

        <div>
            <button class="btn btn-primary btn-sm px-4" 
                wire:click="submitFilter" 
                spinner="submitFilter"
                <?php if(empty($filterTanggalAwal) || empty($filterTanggalAkhir)): ?> disabled <?php endif; ?>>
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-magnifying-glass'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                Lihat Absen
            </button>
        </div>

        <fieldset class="fieldset p-0 m-0">
            <legend class="fieldset-legend text-xs font-semibold">Keterangan</legend>
            <select class="select select-bordered select-sm w-44" wire:model.live="filterKeterangan">
                <option value="">Semua</option>
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
            <button class="btn btn-outline btn-sm" wire:click="setHariIni" spinner="setHariIni">
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
            <button class="btn btn-outline btn-sm" wire:click="setKemarin" spinner="setKemarin">
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

    <?php if($dateError): ?>
        <div class="flex items-center gap-2 mb-6 px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl">
            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-exclamation-triangle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 shrink-0']); ?>
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
            <span><?php echo e($dateError); ?></span>
        </div>
    <?php endif; ?>

    <?php if(!$showData): ?>
        
        <div class="flex flex-col items-center justify-center py-16 px-4 bg-base-100 rounded-2xl border border-base-200 shadow-sm text-center">
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-4">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-calendar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8']); ?>
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
            </div>
            <h3 class="text-base font-bold text-base-content">Tentukan Periode Absensi</h3>
            <p class="text-xs text-base-content/60 max-w-sm mt-2 leading-relaxed">
                Silakan pilih <strong>Tanggal Awal</strong> dan <strong>Tanggal Akhir</strong> di atas, kemudian klik tombol <strong>Lihat Absen</strong> untuk menampilkan data absensi karyawan.
            </p>
            <div class="flex gap-2 mt-6">
                <button class="btn btn-sm btn-outline btn-primary" wire:click="setHariIni" spinner="setHariIni">
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
                <button class="btn btn-sm btn-outline btn-secondary" wire:click="setKemarin" spinner="setKemarin">
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
    <?php else: ?>
        
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
            
            <div class="rounded-xl px-4 py-3 bg-red-50 border border-red-200">
                <div class="text-xs font-semibold text-red-600 uppercase tracking-wide">Alpa</div>
                <div class="text-2xl font-bold text-red-700 mt-1"><?php echo e($rekap['alpa']); ?></div>
            </div>
            
            <div class="rounded-xl px-4 py-3 bg-amber-50 border border-amber-200">
                <div class="text-xs font-semibold text-amber-600 uppercase tracking-wide">Sakit</div>
                <div class="text-2xl font-bold text-amber-700 mt-1"><?php echo e($rekap['sakit']); ?></div>
            </div>
            
            <div class="rounded-xl px-4 py-3 bg-violet-50 border border-violet-200">
                <div class="text-xs font-semibold text-violet-600 uppercase tracking-wide">Cuti</div>
                <div class="text-2xl font-bold text-violet-700 mt-1"><?php echo e($rekap['cuti']); ?></div>
            </div>
            
            <div class="rounded-xl px-4 py-3 bg-slate-50 border border-slate-200">
                <div class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Izin</div>
                <div class="text-2xl font-bold text-slate-700 mt-1"><?php echo e($rekap['izin']); ?></div>
            </div>
        </div>

        
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

            <div class="relative min-h-[30rem]">
                
                <div wire:loading class="absolute inset-0 bg-base-100/30 backdrop-blur-[1px] z-50 rounded-xl transition-all duration-150">
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center gap-2">
                        <span class="loading loading-spinner loading-lg text-primary"></span>
                        <span class="text-xs font-bold text-primary tracking-wider uppercase animate-pulse">Memuat...</span>
                    </div>
                </div>

                <div wire:loading.class="opacity-25 pointer-events-none" class="transition-opacity duration-150">
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

                        <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_no', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                    <span class="text-sm text-base-content/50"><?php echo e($row->row_no); ?></span>
                <?php }); ?>

                <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_karyawan', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                    <div class="flex items-center gap-3">
                        <div class="avatar">
                            <div class="mask mask-squircle w-10 h-10">
                                <img src="<?php echo e($row->karyawan->foto_karyawan ? Storage::url($row->karyawan->foto_karyawan) : 'https://i.pravatar.cc/150?u=' . $row->karyawan->nik); ?>" alt="<?php echo e($row->karyawan->nama_karyawan); ?>" />
                            </div>
                        </div>
                        <div>
                            <div class="font-bold text-sm"><?php echo e($row->karyawan->nama_karyawan); ?></div>
                            <div class="text-xs text-base-content/50"><?php echo e($row->karyawan->jabatan?->nama_jabatan ?? '-'); ?></div>
                        </div>
                    </div>
                <?php }); ?>

                <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_tanggal', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                    <span class="text-sm"><?php echo e($row->tanggal_absen->format('d M Y')); ?></span>
                <?php }); ?>

                <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_keterangan', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium border <?php echo e($this->getColor($row->keterangan)); ?>">
                        <?php echo e($row->keterangan); ?>

                    </span>
                <?php }); ?>

                <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_scan_in', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                    <span class="text-sm font-mono <?php echo e($row->scan_in ? 'text-base-content' : 'text-base-content/40'); ?>">
                        <?php echo e($row->scan_in ?? '-'); ?>

                    </span>
                <?php }); ?>

                <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_scan_out', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                    <span class="text-sm font-mono <?php echo e($row->scan_out ? 'text-base-content' : 'text-base-content/40'); ?>">
                        <?php echo e($row->scan_out ?? '-'); ?>

                    </span>
                <?php }); ?>

                <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_terlambat', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                    <?php $menit = \App\Services\LatenessCalculator::getMinutesLate($row->scan_in, $row->tanggal_absen->format('Y-m-d')); ?>
                    <?php if($menit): ?>
                        <span class="badge badge-sm badge-error badge-outline"><?php echo e($menit); ?> min</span>
                    <?php else: ?>
                        <span class="text-base-content/30">-</span>
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
    <?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\absenhub-v2.0\resources\views\pages\absen\lihat-absen.blade.php ENDPATH**/ ?>