<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Performa Absensi Karyawan - <?php echo e($tahun); ?></title>
    <style>
        @page { margin: 10mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 8px; color: #333; line-height: 1.4; }
        h2 { text-align: center; margin-bottom: 2px; font-size: 14px; color: #111827; }
        .sub { text-align: center; font-size: 10px; color: #4b5563; margin-bottom: 12px; font-weight: bold; }
        .info { margin-bottom: 12px; font-size: 9px; background-color: #f3f4f6; padding: 8px 12px; border-radius: 6px; border: 1px solid #e5e7eb; }
        .info table { width: 100%; border: none; }
        .info td { border: none; padding: 2px 0; text-align: left; font-size: 9px; }
        .info span { font-weight: bold; color: #374151; }
        .legend { margin-top: 4px; font-size: 7px; color: #6b7280; line-height: 1.3; border-top: 1px dashed #d1d5db; padding-top: 4px; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table.data-table th, table.data-table td { border: 1px solid #d1d5db; padding: 5px 4px; text-align: center; }
        table.data-table th { background-color: #f3f4f6; font-weight: bold; font-size: 8px; text-transform: uppercase; color: #1f2937; }
        table.data-table td { font-size: 8px; }
        .text-left { text-align: left; }
        .bg-hadir { background-color: #f0fdf4; color: #166534; }
        .bg-dinas { background-color: #f0f9ff; color: #075985; }
        .bg-cuti { background-color: #f5f3ff; color: #5b21b6; }
        .bg-sakit { background-color: #fffbeb; color: #854d0e; }
        .bg-izin { background-color: #f8fafc; color: #475569; }
        .bg-alpa { background-color: #fef2f2; color: #991b1b; }
        .bg-off { background-color: #f1f5f9; color: #334155; }
        .bg-libur { background-color: #ecfeff; color: #155e75; }
        .bg-lain { background-color: #fdf2f8; color: #9d174d; }
    </style>
</head>
<body>
    <h2>Laporan Performa Absensi Karyawan</h2>
    <div class="sub">Kategori: <?php echo e($filterStatus === 'tetap' ? 'Karyawan Tetap' : 'Karyawan Kontrak'); ?> — Tahun <?php echo e($tahun); ?></div>

    <div class="info">
        <table>
            <tr>
                <td style="width: 50%">
                    <span>Periode:</span> Tahun <?php echo e($tahun); ?>

                </td>
                <td style="width: 50%">
                    <span>Kategori:</span> <?php echo e($filterStatus === 'tetap' ? 'Karyawan Tetap' : 'Karyawan Kontrak'); ?>

                </td>
            </tr>
            <tr>
                <td>
                    <span>Total Karyawan:</span> <?php echo e(count($performanceList)); ?> Orang
                </td>
                <td>
                    <span>Tanggal Unduh:</span> <?php echo e(now()->locale('id')->translatedFormat('d F Y H:i')); ?>

                </td>
            </tr>
            <?php if($search): ?>
            <tr>
                <td colspan="2">
                    <span>Filter Pencarian:</span> "<?php echo e($search); ?>"
                </td>
            </tr>
            <?php endif; ?>
        </table>
        <div class="legend">
            <strong>Keterangan Kolom:</strong> HK = Hari Kerja | H = Hadir | DL = Dinas Luar | C = Cuti | S = Sakit | I = Izin | A = Alpa | Off = Off | L = Libur | Ln = Lainnya | % = Persentase Performa
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th class="text-left" style="width: 25%">Nama Karyawan</th>
                <th class="text-left" style="width: 18%">Lama Kerja</th>
                <th style="width: 5%">HK</th>
                <th class="bg-hadir" style="width: 5%">H</th>
                <th class="bg-dinas" style="width: 5%">DL</th>
                <th class="bg-cuti" style="width: 5%">C</th>
                <th class="bg-sakit" style="width: 5%">S</th>
                <th class="bg-izin" style="width: 5%">I</th>
                <th class="bg-alpa" style="width: 5%">A</th>
                <th class="bg-off" style="width: 4%">Off</th>
                <th class="bg-libur" style="width: 4%">L</th>
                <th class="bg-lain" style="width: 4%">Ln</th>
                <th style="width: 9%">Performa</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $performanceList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php
                    $k = $item['karyawan'];
                    $rate = $item['rate'];
                    $lamaKerja = $k->tanggal_masuk 
                        ? \Carbon\Carbon::parse($k->tanggal_masuk)->locale('id')->diffForHumans(now(), ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) 
                        : '-';
                ?>
                <tr>
                    <td><?php echo e($i + 1); ?></td>
                    <td class="text-left">
                        <strong><?php echo e($k->nama_karyawan); ?></strong>
                        <div style="color: #666; font-size: 7.5px;"><?php echo e($k->jabatan?->nama_jabatan ?? '-'); ?></div>
                    </td>
                    <td class="text-left"><?php echo e($lamaKerja); ?></td>
                    <td><?php echo e($item['hk'] ?: '-'); ?></td>
                    <td class="bg-hadir"><?php echo e($item['hadir'] ?: '-'); ?></td>
                    <td class="bg-dinas"><?php echo e($item['dn'] ?: '-'); ?></td>
                    <td class="bg-cuti"><?php echo e($item['cuti'] ?: '-'); ?></td>
                    <td class="bg-sakit"><?php echo e($item['sakit'] ?: '-'); ?></td>
                    <td class="bg-izin"><?php echo e($item['izin'] ?: '-'); ?></td>
                    <td class="bg-alpa"><?php echo e($item['alpa'] ?: '-'); ?></td>
                    <td class="bg-off"><?php echo e($item['off'] ?: '-'); ?></td>
                    <td class="bg-libur"><?php echo e($item['libur'] ?: '-'); ?></td>
                    <td class="bg-lain"><?php echo e($item['lainnya'] ?: '-'); ?></td>
                    <td style="font-weight: bold; color: <?php echo e($rate >= 90 ? '#166534' : ($rate >= 80 ? '#854d0e' : '#991b1b')); ?>;">
                        <?php echo e($rate); ?>%
                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="14" style="text-align: center; color: #666; padding: 15px;">Tidak ada data performa ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\laragon\www\absenhub-v2.0\resources\views\exports\performa-pdf.blade.php ENDPATH**/ ?>