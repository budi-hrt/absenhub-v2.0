<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Harian - <?php echo e($namaKaryawan); ?> - <?php echo e($namaBulan); ?> <?php echo e($tahun); ?></title>
    <style>
        @page { margin: 10mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #333; }
        h2 { text-align: center; margin-bottom: 2px; font-size: 14px; }
        .sub { text-align: center; font-size: 9px; color: #666; margin-bottom: 8px; }
        .info { margin-bottom: 8px; font-size: 9px; }
        .info span { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 4px 6px; text-align: center; }
        th { background-color: #f3f4f6; font-weight: bold; font-size: 8px; text-transform: uppercase; }
        td { font-size: 9px; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .bg-hadir { background-color: #d1fae5; }
        .bg-sakit { background-color: #fef3c7; }
        .bg-izin { background-color: #f1f5f9; }
        .bg-cuti { background-color: #ede9fe; }
        .bg-alpa { background-color: #fee2e2; }
        .bg-terlambat { background-color: #fee2e2; color: #dc2626; font-weight: bold; }
        .rekap { margin-top: 10px; }
        .rekap table { width: auto; }
        .rekap td { padding: 2px 10px; font-size: 9px; }
    </style>
</head>
<body>
    <h2>Detail Absensi Harian Karyawan</h2>
    <div class="sub"><?php echo e($namaBulan); ?> <?php echo e($tahun); ?></div>

    <div class="info">
        <p><span>Nama:</span> <?php echo e($namaKaryawan); ?></p>
        <p><span>NIK:</span> <?php echo e($nik ?? '-'); ?> &nbsp;&nbsp; <span>Jabatan:</span> <?php echo e($jabatan ?? '-'); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 15%">Tanggal</th>
                <th style="width: 12%">Hari</th>
                <th style="width: 12%">Check In</th>
                <th style="width: 12%">Check Out</th>
                <th style="width: 12%">Terlambat</th>
                <th style="width: 15%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $absens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php $menit = \App\Services\LatenessCalculator::getMinutesLate($a->scan_in, $a->tanggal_absen->format('Y-m-d')); ?>
                <tr>
                    <td><?php echo e($i + 1); ?></td>
                    <td class="text-left"><?php echo e($a->tanggal_absen->format('d/m/Y')); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($a->tanggal_absen)->locale('id')->translatedFormat('l')); ?></td>
                    <td><?php echo e($a->scan_in ?? '-'); ?></td>
                    <td><?php echo e($a->scan_out ?? '-'); ?></td>
                    <td class="<?php echo e($menit ? 'bg-terlambat' : ''); ?>">
                        <?php echo e($menit ? $menit . ' min' : '-'); ?>

                    </td>
                    <td class="text-left"><?php echo e($a->keterangan); ?></td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </tbody>
    </table>

    <?php if($rekap): ?>
        <div class="rekap">
            <table>
                <tr>
                    <td><strong>Rekap:</strong></td>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rekap; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php if($count > 0): ?>
                            <td><?php echo e($label); ?>: <?php echo e($count); ?></td>
                        <?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </tr>
            </table>
        </div>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\laragon\www\absenhub-v2.0\resources\views\exports\detail-harian-pdf.blade.php ENDPATH**/ ?>