<?php
use App\Models\Absen;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Mary\Traits\Toast;
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
            <legend class="fieldset-legend text-xs">Tanggal Awal</legend>
            <input type="date" class="input input-bordered input-sm w-44" wire:model.live="filterTanggalAwal" />
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Tanggal Akhir</legend>
            <input type="date" class="input input-bordered input-sm w-44" wire:model.live="filterTanggalAkhir" />
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Keterangan</legend>
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
            <button class="btn btn-primary btn-sm" wire:click="$set('filterTanggalAwal', '<?php echo e(now()->format('Y-m-d')); ?>'); $set('filterTanggalAkhir', '<?php echo e(now()->format('Y-m-d')); ?>')">
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
            <button class="btn btn-secondary btn-sm" wire:click="$set('filterTanggalAwal', '<?php echo e(now()->subDay()->format('Y-m-d')); ?>'); $set('filterTanggalAkhir', '<?php echo e(now()->subDay()->format('Y-m-d')); ?>')">
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
        <div class="flex items-center gap-2 mb-4 px-4 py-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
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
</div><?php /**PATH C:\laragon\www\absenhub-v2.0\storage\framework\views/livewire/views/d3dd9fb0.blade.php ENDPATH**/ ?>