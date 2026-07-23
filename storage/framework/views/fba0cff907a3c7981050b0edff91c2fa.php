<?php
use App\Models\Absen;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
?>

<div class="space-y-6">
    <!-- Header Section -->
    <?php if (isset($component)) { $__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d = $attributes; } ?>
<?php $component = Mary\View\Components\Header::resolve(['title' => 'Performa Absensi Karyawan','subtitle' => 'Manajemen Absensi > Performa Karyawan','separator' => true,'progressIndicator' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Header::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('actions', null, []); ?> 
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-base-content/70">Tahun:</span>
                <select wire:model.live="tahun" class="select select-bordered select-sm text-xs font-semibold">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->listTahun; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($y); ?>">Tahun <?php echo e($y); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
            </div>
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

    <!-- Filter Tabs Karyawan Tetap / Kontrak -->
    <div class="flex items-center justify-between gap-4">
        <div class="inline-flex bg-base-200 p-1 rounded-xl border border-base-300 gap-1">
            <button wire:click="$set('filterStatus', 'tetap')" class="px-4 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer <?php echo e($filterStatus === 'tetap' ? 'bg-primary text-primary-content shadow-xs' : 'text-base-content/70 hover:bg-base-300/50'); ?>">
                <span>Karyawan Tetap</span>
            </button>
            <button wire:click="$set('filterStatus', 'kontrak')" class="px-4 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer <?php echo e($filterStatus === 'kontrak' ? 'bg-primary text-primary-content shadow-xs' : 'text-base-content/70 hover:bg-base-300/50'); ?>">
                <span>Karyawan Kontrak</span>
            </button>
        </div>
    </div>

    <!-- Key Metrics Cards (Minimalist 4 Cards) -->
    <section wire:loading.class="opacity-50 pointer-events-none" wire:target="tahun" class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 transition-opacity duration-150">

        <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Total Karyawan</p>
                <h3 class="text-xl md:text-2xl font-bold text-base-content mt-0.5"><?php echo e(number_format($this->keyMetrics['totalAktif'])); ?></h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-users'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
            </div>
        </div>

        <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Rata-rata Performa</p>
                <h3 class="text-xl md:text-2xl font-bold text-primary mt-0.5"><?php echo e($this->keyMetrics['avgRate']); ?>%</h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-success/10 text-success flex items-center justify-center">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-chart-bar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
            </div>
        </div>

        <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Performa Baik (≥95%)</p>
                <h3 class="text-xl md:text-2xl font-bold text-success mt-0.5"><?php echo e(number_format($this->keyMetrics['excellentCount'])); ?></h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-info/10 text-info flex items-center justify-center">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-check-badge'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
            </div>
        </div>

        <div class="bg-base-100 border border-base-300 p-4 rounded-xl shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-base-content/60 uppercase tracking-wider">Perhatian (&lt;85%)</p>
                <h3 class="text-xl md:text-2xl font-bold text-error mt-0.5"><?php echo e(number_format($this->keyMetrics['needAttentionCount'])); ?></h3>
            </div>
            <div class="w-9 h-9 rounded-lg bg-error/10 text-error flex items-center justify-center">
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
            </div>
        </div>
    </section>

    <!-- Performance Table Section -->
    <section class="bg-base-100 border border-base-300 rounded-2xl overflow-hidden shadow-sm relative min-h-[20rem]">
        <div wire:loading wire:target="tahun, filterStatus, search, perPage, gotoPage, nextPage, previousPage" class="absolute inset-0 bg-base-100/30 backdrop-blur-[1px] z-50 transition-all duration-150">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 flex flex-col items-center gap-2">
                <span class="loading loading-spinner loading-lg text-primary"></span>
                <span class="text-xs font-bold text-primary/70 tracking-wider uppercase animate-pulse">Memuat...</span>
            </div>
        </div>
        <div wire:loading.class="opacity-25 pointer-events-none" wire:target="tahun, filterStatus, search, perPage, gotoPage, nextPage, previousPage" class="transition-opacity duration-150">
            <div class="p-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-b border-base-300 bg-base-200/40">
            <h3 class="text-base font-bold text-base-content">Daftar Performa Absensi (<?php echo e($filterStatus === 'tetap' ? 'Karyawan Tetap' : 'Karyawan Kontrak'); ?>)</h3>
            
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-magnifying-glass'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-base-content/50']); ?>
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
                    <input wire:model.live.debounce.300ms="search" type="text" class="input input-bordered input-sm w-full pl-9 text-xs" placeholder="Cari nama atau NIK..." />
                </div>
                
                <a href="<?php echo e(route('absen.performa.pdf', ['tahun' => $tahun, 'search' => $search, 'filterStatus' => $filterStatus])); ?>" target="_blank" class="btn btn-primary btn-sm text-xs gap-1.5">
                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-arrow-down-tray'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                    Export PDF
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-base-200/80 border-b border-base-300 text-xs font-semibold text-base-content/70 uppercase tracking-wider">
                        <th class="px-5 py-3">Nama Karyawan</th>
                        <th class="px-5 py-3">Lama Kerja</th>
                        <th class="px-5 py-3 text-center">HK</th>
                        <th class="px-5 py-3 text-center">Izin/Sakit</th>
                        <th class="px-5 py-3 text-center">Alpa</th>
                        <th class="px-5 py-3">Performa %</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-300/60 text-sm">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->performanceData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $k = $item['karyawan'];
                            $rate = $item['rate'];
                            $lamaKerja = $k->tanggal_masuk 
                                ? \Carbon\Carbon::parse($k->tanggal_masuk)->locale('id')->diffForHumans(now(), ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) 
                                : '-';
                        ?>
                        <tr class="hover:bg-base-200/40 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="avatar <?php echo e(!$k->foto_karyawan ? 'placeholder' : ''); ?>">
                                        <div class="mask mask-squircle w-10 h-10 <?php echo e(!$k->foto_karyawan ? 'bg-primary/10 text-primary border border-primary/20 flex items-center justify-center font-bold text-xs' : ''); ?>">
                                            <?php if($k->foto_karyawan): ?>
                                                <img src="<?php echo e(Storage::url($k->foto_karyawan)); ?>" alt="<?php echo e($k->nama_karyawan); ?>" />
                                            <?php else: ?>
                                                <span><?php echo e(strtoupper(substr($k->nama_karyawan, 0, 2))); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-base-content text-sm"><?php echo e($k->nama_karyawan); ?></p>
                                        <p class="text-xs text-base-content/60"><?php echo e($k->jabatan?->nama_jabatan ?? '-'); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-base-content/70 text-xs font-medium">
                                <?php echo e($lamaKerja); ?>

                            </td>
                            <td class="px-5 py-3 text-center text-xs font-medium"><?php echo e($item['hk'] ?: '-'); ?></td>
                            <td class="px-5 py-3 text-center text-xs font-semibold text-amber-700"><?php echo e(($item['izin'] + $item['sakit']) ?: '-'); ?></td>
                            <td class="px-5 py-3 text-center text-xs font-semibold text-rose-700"><?php echo e($item['alpa'] ?: '-'); ?></td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-20 h-1.5 bg-base-200 rounded-full overflow-hidden">
                                        <div class="h-full <?php echo e($rate >= 90 ? 'bg-success' : ($rate >= 80 ? 'bg-warning' : 'bg-error')); ?>" style="width: <?php echo e($rate); ?>%"></div>
                                    </div>
                                    <span class="font-bold text-xs text-base-content"><?php echo e($rate); ?>%</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <button wire:click="showDetail(<?php echo e($k->id); ?>)" class="btn btn-ghost btn-xs text-primary gap-1" title="Lihat Rekapan">
                                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-eye'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                                    <span>Rekap</span>
                                </button>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-base-content/50">
                                Tidak ada data performa karyawan ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-base-300 flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-base-content/60">
                <span>Tampilkan</span>
                <select wire:model.live="perPage" class="select select-bordered select-xs">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                </select>
                <span>baris</span>
            </div>
            <?php if($this->performanceData->hasPages()): ?>
                <?php echo e($this->performanceData->links('livewire::tailwind')); ?>

            <?php endif; ?>
        </div>
        </div>
    </section>

    <!-- Modal Rekapan Performa Detail -->
    <?php if (isset($component)) { $__componentOriginal89a573612f1f1cb2dd9fc072235d4356 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356 = $attributes; } ?>
<?php $component = Mary\View\Components\Modal::resolve(['title' => 'Detail Rekap Performa Absensi'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Modal::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'detailModal','class' => 'backdrop-blur-xs']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <?php if($selectedKaryawan): ?>
            <div class="space-y-4">
                <div class="flex items-center gap-4 p-3 bg-base-200/50 rounded-xl border border-base-300">
                    <div class="avatar <?php echo e(!$selectedKaryawan->foto_karyawan ? 'placeholder' : ''); ?>">
                        <div class="mask mask-squircle w-12 h-12 <?php echo e(!$selectedKaryawan->foto_karyawan ? 'bg-primary/10 text-primary border border-primary/20 flex items-center justify-center font-bold text-sm' : ''); ?>">
                            <?php if($selectedKaryawan->foto_karyawan): ?>
                                <img src="<?php echo e(Storage::url($selectedKaryawan->foto_karyawan)); ?>" alt="<?php echo e($selectedKaryawan->nama_karyawan); ?>" />
                            <?php else: ?>
                                <span><?php echo e(strtoupper(substr($selectedKaryawan->nama_karyawan, 0, 2))); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-base text-base-content"><?php echo e($selectedKaryawan->nama_karyawan); ?></h4>
                        <p class="text-xs text-base-content/60">NIK: <?php echo e($selectedKaryawan->nik); ?> | Jabatan: <?php echo e($selectedKaryawan->jabatan?->nama_jabatan ?? '-'); ?></p>
                        <p class="text-xs text-primary font-medium mt-0.5">Tahun Periode: <?php echo e($tahun); ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 text-center text-xs">
                    <div class="p-2.5 bg-base-200/60 rounded-lg border border-base-300">
                        <span class="text-base-content/60 block text-[10px] uppercase font-semibold">HK (Hari Kerja)</span>
                        <span class="font-bold text-sm text-base-content"><?php echo e($selectedKaryawanRekap['hk'] ?? 0); ?></span>
                    </div>
                    <div class="p-2.5 bg-emerald-50 rounded-lg border border-emerald-200">
                        <span class="text-emerald-700 block text-[10px] uppercase font-semibold">Hadir</span>
                        <span class="font-bold text-sm text-emerald-800"><?php echo e($selectedKaryawanRekap['hadir'] ?? 0); ?></span>
                    </div>
                    <div class="p-2.5 bg-sky-50 rounded-lg border border-sky-200">
                        <span class="text-sky-700 block text-[10px] uppercase font-semibold">Dinas Luar</span>
                        <span class="font-bold text-sm text-sky-800"><?php echo e($selectedKaryawanRekap['dn'] ?? 0); ?></span>
                    </div>
                    <div class="p-2.5 bg-green-50 rounded-lg border border-green-200">
                        <span class="text-green-700 block text-[10px] uppercase font-semibold">Cuti</span>
                        <span class="font-bold text-sm text-green-800"><?php echo e($selectedKaryawanRekap['cuti'] ?? 0); ?></span>
                    </div>
                    <div class="p-2.5 bg-amber-50 rounded-lg border border-amber-200">
                        <span class="text-amber-700 block text-[10px] uppercase font-semibold">Sakit</span>
                        <span class="font-bold text-sm text-amber-800"><?php echo e($selectedKaryawanRekap['sakit'] ?? 0); ?></span>
                    </div>
                    <div class="p-2.5 bg-orange-50 rounded-lg border border-orange-200">
                        <span class="text-orange-700 block text-[10px] uppercase font-semibold">Izin</span>
                        <span class="font-bold text-sm text-orange-800"><?php echo e($selectedKaryawanRekap['izin'] ?? 0); ?></span>
                    </div>
                    <div class="p-2.5 bg-rose-50 rounded-lg border border-rose-200">
                        <span class="text-rose-700 block text-[10px] uppercase font-semibold">Alpa</span>
                        <span class="font-bold text-sm text-rose-800"><?php echo e($selectedKaryawanRekap['alpa'] ?? 0); ?></span>
                    </div>
                    <div class="p-2.5 bg-purple-50 rounded-lg border border-purple-200">
                        <span class="text-purple-700 block text-[10px] uppercase font-semibold">Performa %</span>
                        <span class="font-bold text-sm text-purple-900"><?php echo e($selectedKaryawanRekap['persen'] ?? 0); ?>%</span>
                    </div>
                </div>
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
<?php $component->withAttributes(['@click' => '$wire.detailModal = false','class' => 'btn-ghost btn-sm']); ?>
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
            <?php if($selectedKaryawan): ?>
                <a href="<?php echo e(route('absen.detail-harian', ['karyawan_id' => $selectedKaryawan->id])); ?>" wire:navigate class="btn btn-primary btn-sm">
                    Lihat Detail Harian
                </a>
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
</div><?php /**PATH C:\laragon\www\absenhub-v2.0\storage\framework\views/livewire/views/97d74b9d.blade.php ENDPATH**/ ?>