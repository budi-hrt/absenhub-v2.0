<?php
use App\Models\Absen;
use App\Models\Karyawan;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
?>

<div>
    <?php if (isset($component)) { $__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d = $attributes; } ?>
<?php $component = Mary\View\Components\Header::resolve(['title' => 'Rekap Tahunan','separator' => true,'progressIndicator' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Header::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('actions', null, []); ?> 
            <a href="<?php echo e(route('absen.rekap-tahunan.pdf', ['tahun' => $tahun, 'search' => $search])); ?>" target="_blank" class="btn btn-error btn-sm text-white">
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

    
    <section class="flex flex-wrap items-end gap-3 mb-2 p-3 bg-base-200 rounded-xl">
        <fieldset class="fieldset flex-1 min-w-[200px]">
            <legend class="fieldset-legend text-xs">Cari Karyawan</legend>
            <input type="text" class="input input-bordered input-sm w-full" placeholder="Cari nama/NIK..."
                wire:model.live.debounce="search" />
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Tahun</legend>
            <select class="select select-bordered select-sm w-28" wire:model.live="tahun">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $listTahun; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($t['id']); ?>"><?php echo e($t['name']); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </fieldset>
    </section>

    
    <div class="bg-base-100 border rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-xs" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'rekap-tahunan-'.e($tahun).''; ?>wire:key="rekap-tahunan-<?php echo e($tahun); ?>">
                <thead>
                    <tr class="bg-base-200 border-b">
                        <th class="px-2 py-2 text-left font-semibold text-base-content/70 border-r w-[120px]">NAMA</th>
                        <th class="px-2 py-2 text-left font-semibold text-base-content/70 border-r w-[60px]">JABATAN</th>
                        <th class="px-2 py-2 text-center font-semibold text-base-content/70 border-r w-12" title="Hari Kerja">HK</th>
                        <th class="px-2 py-2 text-center font-semibold text-emerald-700 bg-emerald-50 border-r w-12">Hadir</th>
                        <th class="px-2 py-2 text-center font-semibold text-sky-700 bg-sky-100 border-r w-12">DL</th>
                        <th class="px-2 py-2 text-center font-semibold text-green-700 bg-green-100 border-r w-12">Cuti</th>
                        <th class="px-2 py-2 text-center font-semibold border-r w-12 bg-yellow-100">Sakit</th>
                        <th class="px-2 py-2 text-center font-semibold text-orange-700 bg-orange-100 border-r w-12">Izin</th>
                        <th class="px-2 py-2 text-center font-semibold text-red-700 bg-red-100 border-r w-12">Alpa</th>
                        <th class="px-2 py-2 text-center font-semibold text-gray-600 bg-gray-200 border-r w-10">Off</th>
                        <th class="px-2 py-2 text-center font-semibold text-gray-500 bg-gray-100 border-r w-12">Libur</th>
                        <th class="px-2 py-2 text-center font-semibold text-blue-700 bg-blue-100 border-r w-12">Lain</th>
                        <th class="px-2 py-2 text-center font-semibold text-base-content/70 w-12">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $karyawans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $allRecords = $absenRecords[$k->id] ?? collect();
                            $hk = $allRecords->count();
                            $hadir = $allRecords->where('keterangan', 'Hadir')->count();
                            $dn = $allRecords->where('keterangan', 'Dinas Luar')->count();
                            $cuti = $allRecords->where('keterangan', 'Cuti')->count();
                            $sakit = $allRecords->where('keterangan', 'Sakit')->count();
                            $izin = $allRecords->where('keterangan', 'Izin')->count();
                            $alpa = $allRecords->where('keterangan', 'Alpa')->count();
                            $off = $allRecords->where('keterangan', 'Off')->count();
                            $libur = $allRecords->where('keterangan', 'Libur')->count();
                            $lainnya = $allRecords->where('keterangan', 'Lainnya')->count();
                            $persen = $totalHari > 0 ? round((($hadir + $dn + $cuti + $off + $libur) / $totalHari) * 100) : 0;
                            $isAlumni = !$k->is_active;
                        ?>
                        <tr class="hover:bg-base-200/50 transition-colors">
                            <td class="px-2 py-1.5 font-medium border-r whitespace-nowrap overflow-hidden text-ellipsis max-w-[120px]" title="<?php echo e($k->nama_karyawan); ?>">
                                <?php echo e($k->nama_karyawan); ?>

                                <?php if($isAlumni): ?>
                                    <span class="badge badge-xs badge-ghost ml-1">Alumni</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-2 py-1.5 text-base-content/60 border-r whitespace-nowrap overflow-hidden text-ellipsis max-w-[60px]" title="<?php echo e($k->jabatan?->nama_jabatan ?? '-'); ?>"><?php echo e($k->jabatan?->nama_jabatan ?? '-'); ?></td>
                            <td class="px-2 py-1.5 text-center font-medium border-r"><?php echo e($hk ?: '-'); ?></td>
                            <td class="px-2 py-1.5 text-center font-medium text-emerald-700 bg-emerald-50 border-r"><?php echo e($hadir ?: '-'); ?></td>
                            <td class="px-2 py-1.5 text-center font-medium text-sky-700 bg-sky-100 border-r"><?php echo e($dn ?: '-'); ?></td>
                            <td class="px-2 py-1.5 text-center font-medium text-green-700 bg-green-100 border-r"><?php echo e($cuti ?: '-'); ?></td>
                            <td class="px-2 py-1.5 text-center font-medium border-r bg-yellow-100"><?php echo e($sakit ?: '-'); ?></td>
                            <td class="px-2 py-1.5 text-center font-medium text-orange-700 bg-orange-100 border-r"><?php echo e($izin ?: '-'); ?></td>
                            <td class="px-2 py-1.5 text-center font-medium text-red-700 bg-red-100 border-r"><?php echo e($alpa ?: '-'); ?></td>
                            <td class="px-2 py-1.5 text-center font-medium text-gray-600 bg-gray-200 border-r"><?php echo e($off ?: '-'); ?></td>
                            <td class="px-2 py-1.5 text-center font-medium text-gray-500 bg-gray-100 border-r"><?php echo e($libur ?: '-'); ?></td>
                            <td class="px-2 py-1.5 text-center font-medium text-blue-700 bg-blue-100 border-r"><?php echo e($lainnya ?: '-'); ?></td>
                            <td class="px-2 py-1.5 text-center font-medium"><?php echo e($totalHari ? $persen . '%' : '-'); ?></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="13" class="text-center py-8 text-base-content/40">
                                Tidak ada data untuk periode ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="flex items-center justify-between px-4 py-3 border-t">
            <div class="flex items-center gap-2 text-sm text-base-content/60">
                <span>Tampilkan</span>
                <select class="select select-bordered select-xs" wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>baris</span>
            </div>
            <?php if($karyawans->hasPages()): ?>
                <?php echo e($karyawans->links('livewire::tailwind')); ?>

            <?php endif; ?>
        </div>
    </div>
</div><?php /**PATH C:\laragon\www\absenhub-v2.0\storage\framework\views/livewire/views/a7d9f309.blade.php ENDPATH**/ ?>