<?php
use App\Models\Absen;
use App\Models\Karyawan;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Mary\Traits\Toast;
?>

<div>
    <?php if (isset($component)) { $__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d = $attributes; } ?>
<?php $component = Mary\View\Components\Header::resolve(['title' => 'Laporan Absensi Karyawan','separator' => true,'progressIndicator' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Header::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

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
            <legend class="fieldset-legend text-xs">Bulan</legend>
            <select class="select select-bordered select-sm w-36" wire:model.live="bulan">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $listBulan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($b['id']); ?>"><?php echo e($b['name']); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </fieldset>
        <fieldset class="fieldset">
            <legend class="fieldset-legend text-xs">Tahun</legend>
            <select class="select select-bordered select-sm w-28" wire:model.live="tahun">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $listTahun; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($t['id']); ?>"><?php echo e($t['name']); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </fieldset>
        <a href="<?php echo e(route('absen.laporan-bulanan.pdf', ['bulan' => $bulan, 'tahun' => $tahun, 'search' => $search])); ?>"
            class="btn btn-outline btn-sm btn-error">
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
            Export PDF
        </a>
    </section>

    
    <div class="flex flex-wrap gap-4 px-4 py-2 mb-2 bg-base-200/50 rounded-lg text-xs">
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-base-100 border border-base-300"></span> Hadir
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-base-100 border border-base-300 font-bold text-center text-[10px] leading-3">}</span> Dinas Luar
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-green-600"></span> Cuti
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-yellow-400"></span> Sakit
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-orange-400"></span> Izin
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-red-600"></span> Alpa
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-gray-200"></span> Libur
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-gray-400 font-bold text-center text-[10px] leading-3">O</span> Off
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-3 h-3 rounded-full bg-blue-200 font-bold text-center text-[10px] leading-3">X</span> Lainnya
        </span>
    </div>

    
    <div class="bg-base-100 border rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-xs" <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'laporan-'.e($bulan).'-'.e($tahun).''; ?>wire:key="laporan-<?php echo e($bulan); ?>-<?php echo e($tahun); ?>">
                <thead>
                    
                    <tr class="bg-base-200 border-b">
                        <th class="px-3 py-2 text-left font-semibold text-base-content/70 border-r min-w-[140px]" rowspan="2">NAMA</th>
                        <th class="px-3 py-2 text-left font-semibold text-base-content/70 border-r min-w-[90px]" rowspan="2">JABATAN</th>
                        <th class="px-3 py-2 text-center font-semibold text-base-content/70 border-r" colspan="<?php echo e($totalHari); ?>">
                            Hari Kerja (HK) di Bulan <?php echo e($namaBulan); ?> <?php echo e($tahun); ?>

                        </th>
                        <th class="px-2 py-2 text-center font-semibold text-base-content/70 border-r" rowspan="2">HK</th>
                        <th class="px-2 py-2 text-center font-semibold text-white bg-green-600 border-r" rowspan="2">C</th>
                        <th class="px-2 py-2 text-center font-semibold text-black bg-yellow-400 border-r" rowspan="2">S</th>
                        <th class="px-2 py-2 text-center font-semibold text-white bg-orange-400 border-r" rowspan="2">I</th>
                        <th class="px-2 py-2 text-center font-semibold text-white bg-red-600 border-r" rowspan="2">A</th>
                        <th class="px-2 py-2 text-center font-semibold text-base-content/70" rowspan="2">%</th>
                    </tr>
                    
                    <tr class="bg-base-200 border-b">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($d = 1; $d <= $totalHari; $d++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <th class="w-7 py-1.5 text-center font-medium text-[10px] text-base-content/50 border-r <?php echo e($d % 2 === 0 ? 'bg-base-200' : 'bg-base-100'); ?>">
                                <?php echo e($d); ?>

                            </th>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $karyawans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $records = ($absenRecords[$k->id] ?? collect())->keyBy(fn($a) => (int) $a->tanggal_absen->format('d'));
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
                            $persen = $hk > 0 ? max(0, round(100 - ($alpa * 3) - ($izin * 2) - ($sakit * 1) - ($lainnya * 0.5), 1)) : 0;
                            $isAlumni = !$k->is_active;
                        ?>
                        <tr class="hover:bg-base-200/50 transition-colors">
                            <td class="px-3 py-1.5 font-medium border-r whitespace-nowrap">
                                <?php echo e($k->nama_karyawan); ?>

                                <?php if($isAlumni): ?>
                                    <span class="badge badge-xs badge-ghost ml-1">Alumni</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-1.5 text-base-content/60 border-r whitespace-nowrap"><?php echo e($k->jabatan?->nama_jabatan ?? '-'); ?></td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($d = 1; $d <= $totalHari; $d++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php $info = $this->cellInfo($records->get($d), $d); ?>
                                <td class="w-7 py-1.5 text-center border-r <?php echo e($info['class']); ?>">
                                    <span class="text-[11px] <?php echo e($info['letter'] ? 'font-bold' : ''); ?>"><?php echo e($info['letter']); ?></span>
                                </td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <td class="px-2 py-1.5 text-center font-medium border-r"><?php echo e($hk ?: '-'); ?></td>
                            <td class="px-2 py-1.5 text-center font-bold text-white bg-green-600 border-r"><?php echo e($cuti ?: '-'); ?></td>
                            <td class="px-2 py-1.5 text-center font-bold bg-yellow-400 border-r"><?php echo e($sakit ?: '-'); ?></td>
                            <td class="px-2 py-1.5 text-center font-bold text-white bg-orange-400 border-r"><?php echo e($izin ?: '-'); ?></td>
                            <td class="px-2 py-1.5 text-center font-bold text-white bg-red-600 border-r"><?php echo e($alpa ?: '-'); ?></td>
                            <td class="px-2 py-1.5 text-center font-medium"><?php echo e($hk ? $persen . '%' : '-'); ?></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="<?php echo e($totalHari + 8); ?>" class="text-center py-8 text-base-content/40">
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
</div><?php /**PATH C:\laragon\www\absenhub-v2.0\storage\framework\views/livewire/views/c03df511.blade.php ENDPATH**/ ?>