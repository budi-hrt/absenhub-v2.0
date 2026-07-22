<?php
use App\Models\PengajuanAbsen;
use App\Models\JatahCuti;
use App\Models\Karyawan;
use App\Models\Absen;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;
?>

<div>
    <?php if (isset($component)) { $__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d = $attributes; } ?>
<?php $component = Mary\View\Components\Header::resolve(['title' => 'Kelola Pengajuan','separator' => true,'progressIndicator' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Header::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('actions', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => 'Buat Pengajuan','icon' => 'o-plus'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'openCreateModal','class' => 'btn-primary shadow-sm']); ?>
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
<?php if (isset($__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d)): ?>
<?php $attributes = $__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d; ?>
<?php unset($__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d)): ?>
<?php $component = $__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d; ?>
<?php unset($__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d); ?>
<?php endif; ?>

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-base-100 p-5 rounded-2xl border border-base-300 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-warning/10 flex items-center justify-center shrink-0">
                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-clock'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-6 h-6 text-warning']); ?>
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
                <div>
                    <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Menunggu</p>
                    <p class="text-2xl font-bold text-warning"><?php echo e($menungguCount); ?></p>
                </div>
            </div>
        </div>

        <div class="bg-base-100 p-5 rounded-2xl border border-base-300 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-success/10 flex items-center justify-center shrink-0">
                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-check-circle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-6 h-6 text-success']); ?>
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
                <div>
                    <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Disetujui</p>
                    <p class="text-2xl font-bold text-success"><?php echo e($disetujuiCount); ?></p>
                </div>
            </div>
        </div>

        <div class="bg-base-100 p-5 rounded-2xl border border-base-300 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-error/10 flex items-center justify-center shrink-0">
                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-x-circle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-6 h-6 text-error']); ?>
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
                <div>
                    <p class="text-xs text-base-content/60 font-semibold uppercase tracking-wider">Ditolak</p>
                    <p class="text-2xl font-bold text-error"><?php echo e($ditolakCount); ?></p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <?php if (isset($component)) { $__componentOriginalf51438a7488970badd535e5f203e0c1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf51438a7488970badd535e5f203e0c1b = $attributes; } ?>
<?php $component = Mary\View\Components\Input::resolve(['label' => 'Cari Nama Karyawan','icon' => 'o-magnifying-glass','clearable' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Input::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.300ms' => 'search']); ?>
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
        <?php if (isset($component)) { $__componentOriginald64144c2287634503c73cd4803d6e578 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald64144c2287634503c73cd4803d6e578 = $attributes; } ?>
<?php $component = Mary\View\Components\Select::resolve(['label' => 'Filter Status','options' => $statusOptions,'optionValue' => 'id','optionLabel' => 'name'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Select::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'filterStatus']); ?>
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
    </div>

    
    <?php if (isset($component)) { $__componentOriginal7f194736b6f6432dc38786f292496c34 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7f194736b6f6432dc38786f292496c34 = $attributes; } ?>
<?php $component = Mary\View\Components\Card::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Card::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'border border-base-300 shadow-sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Durasi</th>
                        <th>Status</th>
                        <th>Diajukan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $pengajuans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $statusBadge = match($p->status) {
                                'Menunggu' => 'badge-warning',
                                'Disetujui' => 'badge-success',
                                'Ditolak' => 'badge-error',
                                default => 'badge-ghost',
                            };
                            $jenisBadge = match($p->jenis) {
                                'Cuti' => 'badge-primary',
                                'Izin' => 'badge-info',
                                'Sakit' => 'badge-error badge-outline',
                                default => 'badge-ghost',
                            };
                        ?>
                        <tr class="hover <?php echo e($p->status === 'Menunggu' ? 'bg-warning/5' : ''); ?>">
                            <td>
                                <div class="font-bold"><?php echo e($p->karyawan?->nama_karyawan ?? '-'); ?></div>
                                <div class="text-xs text-base-content/50"><?php echo e($p->karyawan?->jabatan?->nama_jabatan ?? '-'); ?></div>
                            </td>
                            <td><span class="badge <?php echo e($jenisBadge); ?> badge-sm"><?php echo e($p->jenis); ?></span></td>
                            <td class="text-sm max-w-xs truncate">
                                <?php if(is_array($p->tanggal) && count($p->tanggal) > 0): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $p->tanggal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $tgl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <?php echo e(Carbon::parse($tgl)->locale('id')->isoFormat('D MMM Y')); ?><?php if(!$loop->last): ?>, <?php endif; ?>
                                        <?php if($idx == 2 && count($p->tanggal) > 3): ?> <span class="text-xs opacity-50">(+<?php echo e(count($p->tanggal) - 3); ?> hari)</span> <?php break; ?> <?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="font-bold"><?php echo e($p->jumlah_hari); ?> hari</td>
                            <td><span class="badge <?php echo e($statusBadge); ?> badge-sm"><?php echo e($p->status); ?></span></td>
                            <td class="text-xs text-base-content/50">
                                <?php echo e(Carbon::parse($p->created_at)->locale('id')->isoFormat('D MMM Y')); ?>

                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-eye','spinner' => true,'tooltip' => 'Detail'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'lihatDetail('.e($p->id).')','class' => 'btn-sm btn-ghost text-primary']); ?>
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
                                    <?php if($p->status !== 'Disetujui'): ?>
                                        <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-trash','spinner' => true,'tooltip' => 'Hapus'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'hapus('.e($p->id).')','wire:confirm' => 'Yakin ingin menghapus pengajuan ini?','class' => 'btn-sm btn-ghost text-error']); ?>
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
                                </div>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="7" class="text-center py-8 text-base-content/50">
                                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-inbox'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-10 h-10 mx-auto mb-2 opacity-30']); ?>
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
                                Belum ada pengajuan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($pengajuans->links()); ?>

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

    
    <?php if (isset($component)) { $__componentOriginal89a573612f1f1cb2dd9fc072235d4356 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356 = $attributes; } ?>
<?php $component = Mary\View\Components\Modal::resolve(['title' => 'Buat Pengajuan Baru','subtitle' => 'Ajukan cuti/izin/sakit atas nama karyawan','boxClass' => 'max-w-xl'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Modal::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'showCreateModal']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <form wire:submit="submitPengajuan" class="space-y-4">
            
            <?php if (isset($component)) { $__componentOriginald64144c2287634503c73cd4803d6e578 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald64144c2287634503c73cd4803d6e578 = $attributes; } ?>
<?php $component = Mary\View\Components\Select::resolve(['label' => 'Karyawan','options' => $karyawanList,'optionValue' => 'id','optionLabel' => 'name','placeholder' => 'Pilih karyawan...','icon' => 'o-user'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Select::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'karyawan_id','searchable' => true]); ?>
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

            
            <?php if($karyawan_id && $selectedKaryawanSisaCuti !== null): ?>
                <div class="bg-info/10 text-info p-3 rounded-xl text-sm flex items-center gap-2">
                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-information-circle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 shrink-0']); ?>
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
                    <span>Sisa cuti karyawan ini: <strong><?php echo e($selectedKaryawanSisaCuti); ?> hari</strong> (tahun <?php echo e(now()->year); ?>)</span>
                </div>
            <?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginald64144c2287634503c73cd4803d6e578 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald64144c2287634503c73cd4803d6e578 = $attributes; } ?>
<?php $component = Mary\View\Components\Select::resolve(['label' => 'Jenis Pengajuan','options' => $jenisOptions,'optionValue' => 'id','optionLabel' => 'name','placeholder' => 'Pilih jenis...','icon' => 'o-document-text'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Select::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'jenis']); ?>
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

            
            <div>
                <label class="label"><span class="label-text font-semibold">Daftar Tanggal <span class="text-error">*</span></span></label>
                <div class="space-y-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tanggal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $tgl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="flex gap-2 items-start">
                            <div class="flex-1">
                                <?php if (isset($component)) { $__componentOriginalf51438a7488970badd535e5f203e0c1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf51438a7488970badd535e5f203e0c1b = $attributes; } ?>
<?php $component = Mary\View\Components\Input::resolve(['icon' => 'o-calendar'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Input::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'tanggal.'.e($index).'','type' => 'date']); ?>
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
                            </div>
                            <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-trash','tooltip' => 'Hapus'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'hapusTanggal('.e($index).')','class' => 'btn-error btn-outline']); ?>
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
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="text-sm text-base-content/50 italic mb-2">Belum ada tanggal dipilih.</div>
                    <?php endif; ?>
                </div>
                <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => 'Tambah Tanggal','icon' => 'o-plus'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'tambahTanggal','class' => 'btn-sm btn-ghost mt-2']); ?>
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
                <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-error text-sm mt-1 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <?php if (isset($component)) { $__componentOriginaleda28cbc945270b2059ee861cf34a6bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaleda28cbc945270b2059ee861cf34a6bc = $attributes; } ?>
<?php $component = Mary\View\Components\Textarea::resolve(['label' => 'Keterangan / Alasan'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Textarea::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'keterangan','placeholder' => 'Tulis alasan pengajuan...','rows' => '3']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaleda28cbc945270b2059ee861cf34a6bc)): ?>
<?php $attributes = $__attributesOriginaleda28cbc945270b2059ee861cf34a6bc; ?>
<?php unset($__attributesOriginaleda28cbc945270b2059ee861cf34a6bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaleda28cbc945270b2059ee861cf34a6bc)): ?>
<?php $component = $__componentOriginaleda28cbc945270b2059ee861cf34a6bc; ?>
<?php unset($__componentOriginaleda28cbc945270b2059ee861cf34a6bc); ?>
<?php endif; ?>

            
            <?php if (isset($component)) { $__componentOriginal34d3cad3f3cac9fafaaed454c83e534d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal34d3cad3f3cac9fafaaed454c83e534d = $attributes; } ?>
<?php $component = Mary\View\Components\File::resolve(['label' => 'Lampiran (Opsional)','hint' => 'Maks 2MB. Format: JPG, PNG.'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('file'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\File::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'lampiran','accept' => 'image/*']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal34d3cad3f3cac9fafaaed454c83e534d)): ?>
<?php $attributes = $__attributesOriginal34d3cad3f3cac9fafaaed454c83e534d; ?>
<?php unset($__attributesOriginal34d3cad3f3cac9fafaaed454c83e534d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal34d3cad3f3cac9fafaaed454c83e534d)): ?>
<?php $component = $__componentOriginal34d3cad3f3cac9fafaaed454c83e534d; ?>
<?php unset($__componentOriginal34d3cad3f3cac9fafaaed454c83e534d); ?>
<?php endif; ?>

            
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
<?php $component->withAttributes(['@click' => '$wire.showCreateModal = false']); ?>
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
<?php $component = Mary\View\Components\Button::resolve(['label' => 'Buat Pengajuan','icon' => 'o-paper-airplane','spinner' => 'submitPengajuan'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','class' => 'btn-primary']); ?>
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
        </form>
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

    
    <?php if (isset($component)) { $__componentOriginal89a573612f1f1cb2dd9fc072235d4356 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356 = $attributes; } ?>
<?php $component = Mary\View\Components\Modal::resolve(['title' => 'Detail Pengajuan','boxClass' => 'max-w-xl'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Modal::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'showModal']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <?php if($selectedPengajuan): ?>
            <div class="space-y-4">
                
                <div class="bg-base-200/50 p-4 rounded-xl">
                    <p class="text-sm font-bold text-base-content"><?php echo e($selectedPengajuan->karyawan?->nama_karyawan); ?></p>
                    <p class="text-xs text-base-content/60"><?php echo e($selectedPengajuan->karyawan?->jabatan?->nama_jabatan ?? '-'); ?></p>
                </div>

                
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-base-200/50 p-3 rounded-xl">
                        <p class="text-[10px] uppercase text-base-content/50 font-semibold mb-0.5">Jenis</p>
                        <p class="font-bold"><?php echo e($selectedPengajuan->jenis); ?></p>
                    </div>
                    <div class="bg-base-200/50 p-3 rounded-xl">
                        <p class="text-[10px] uppercase text-base-content/50 font-semibold mb-0.5">Durasi</p>
                        <p class="font-bold"><?php echo e($selectedPengajuan->jumlah_hari); ?> hari</p>
                    </div>
                    <div class="bg-base-200/50 p-3 rounded-xl col-span-2">
                        <p class="text-[10px] uppercase text-base-content/50 font-semibold mb-0.5">Daftar Tanggal</p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            <?php if(is_array($selectedPengajuan->tanggal)): ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $selectedPengajuan->tanggal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tgl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <span class="badge badge-neutral"><?php echo e(Carbon::parse($tgl)->locale('id')->isoFormat('D MMM Y')); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if($selectedPengajuan->keterangan): ?>
                    <div class="bg-base-200/50 p-3 rounded-xl">
                        <p class="text-[10px] uppercase text-base-content/50 font-semibold mb-1">Alasan</p>
                        <p class="text-sm"><?php echo e($selectedPengajuan->keterangan); ?></p>
                    </div>
                <?php endif; ?>

                <?php if($selectedPengajuan->lampiran): ?>
                    <div class="bg-base-200/50 p-3 rounded-xl">
                        <p class="text-[10px] uppercase text-base-content/50 font-semibold mb-2">Lampiran</p>
                        <img src="<?php echo e(Storage::url($selectedPengajuan->lampiran)); ?>" alt="Lampiran" class="rounded-lg max-h-64 object-contain" />
                    </div>
                <?php endif; ?>

                
                <?php if($selectedPengajuan->jenis === 'Cuti'): ?>
                    <?php
                        $sisaCuti = \App\Models\JatahCuti::sisaByKaryawan($selectedPengajuan->karyawan_id);
                    ?>
                    <div class="bg-info/10 text-info p-3 rounded-xl text-sm">
                        <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-information-circle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 inline']); ?>
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
                        Sisa cuti karyawan ini: <strong><?php echo e($sisaCuti); ?> hari</strong>
                    </div>
                <?php endif; ?>

                
                <?php if($selectedPengajuan->status === 'Menunggu'): ?>
                    <?php if (isset($component)) { $__componentOriginaleda28cbc945270b2059ee861cf34a6bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaleda28cbc945270b2059ee861cf34a6bc = $attributes; } ?>
<?php $component = Mary\View\Components\Textarea::resolve(['label' => 'Alasan Penolakan (opsional)'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Textarea::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'alasan_tolak','placeholder' => 'Isi jika ingin menolak...','rows' => '2']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaleda28cbc945270b2059ee861cf34a6bc)): ?>
<?php $attributes = $__attributesOriginaleda28cbc945270b2059ee861cf34a6bc; ?>
<?php unset($__attributesOriginaleda28cbc945270b2059ee861cf34a6bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaleda28cbc945270b2059ee861cf34a6bc)): ?>
<?php $component = $__componentOriginaleda28cbc945270b2059ee861cf34a6bc; ?>
<?php unset($__componentOriginaleda28cbc945270b2059ee861cf34a6bc); ?>
<?php endif; ?>

                    <div class="flex gap-3 pt-2">
                        <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => 'Setujui','icon' => 'o-check','spinner' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'setujui('.e($selectedPengajuan->id).')','wire:confirm' => 'Yakin menyetujui pengajuan ini?','class' => 'btn-success flex-1']); ?>
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
<?php $component = Mary\View\Components\Button::resolve(['label' => 'Tolak','icon' => 'o-x-mark','spinner' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'tolak('.e($selectedPengajuan->id).')','wire:confirm' => 'Yakin menolak pengajuan ini?','class' => 'btn-error btn-outline flex-1']); ?>
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
                    </div>
                <?php else: ?>
                    <div class="bg-base-200/50 p-3 rounded-xl">
                        <p class="text-[10px] uppercase text-base-content/50 font-semibold mb-1">Status</p>
                        <?php
                            $sBadge = match($selectedPengajuan->status) {
                                'Disetujui' => 'badge-success',
                                'Ditolak' => 'badge-error',
                                default => 'badge-ghost',
                            };
                        ?>
                        <span class="badge <?php echo e($sBadge); ?>"><?php echo e($selectedPengajuan->status); ?></span>
                        <?php if($selectedPengajuan->admin): ?>
                            <span class="text-xs text-base-content/50 ml-2">oleh <?php echo e($selectedPengajuan->admin->name); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if($selectedPengajuan->status === 'Ditolak' && $selectedPengajuan->alasan_tolak): ?>
                        <div class="bg-error/10 text-error p-3 rounded-xl text-sm">
                            <span class="font-semibold">Alasan ditolak:</span> <?php echo e($selectedPengajuan->alasan_tolak); ?>

                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
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
</div><?php /**PATH C:\laragon\www\absenhub-v2.0\storage\framework\views/livewire/views/d05dd8ae.blade.php ENDPATH**/ ?>