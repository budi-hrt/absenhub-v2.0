<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi Karyawan</title>
    <style>
        @page { 
            margin: 10mm; 
        }
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            font-size: 9px; 
            color: #333; 
            line-height: 1.4;
        }
        h2 { 
            text-align: center; 
            margin-bottom: 2px; 
            font-size: 14px; 
            color: #111827;
        }
        .sub { 
            text-align: center; 
            font-size: 10px; 
            color: #4b5563; 
            margin-bottom: 12px; 
            font-weight: bold;
        }
        .filter-info { 
            margin-bottom: 12px; 
            font-size: 9px; 
            background-color: #f3f4f6; 
            padding: 8px 12px; 
            border-radius: 6px; 
            border: 1px solid #e5e7eb;
        }
        .filter-info table {
            width: 100%;
            border: none;
        }
        .filter-info td {
            border: none;
            padding: 2px 0;
            text-align: left;
            font-size: 9px;
        }
        .filter-info span { 
            font-weight: bold; 
            color: #374151;
        }
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 5px;
        }
        table.data-table th, table.data-table td { 
            border: 1px solid #d1d5db; 
            padding: 5px 6px; 
            text-align: center; 
        }
        table.data-table th { 
            background-color: #f3f4f6; 
            font-weight: bold; 
            font-size: 8px; 
            text-transform: uppercase; 
            color: #1f2937;
        }
        table.data-table td { 
            font-size: 9px; 
        }
        .text-left { 
            text-align: left; 
        }
        .text-right { 
            text-align: right; 
        }
        .bg-hadir { background-color: #d1fae5; }
        .bg-sakit { background-color: #fef3c7; }
        .bg-izin { background-color: #f1f5f9; }
        .bg-cuti { background-color: #ede9fe; }
        .bg-alpa { background-color: #fee2e2; }
        .bg-dinas { background-color: #e0f2fe; }
        .bg-terlambat { 
            background-color: #fee2e2; 
            color: #dc2626; 
            font-weight: bold; 
        }
        .rekap-container { 
            margin-top: 15px; 
            margin-bottom: 10px;
        }
        .rekap-title {
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 4px;
            color: #374151;
        }
        .rekap-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .rekap-grid td {
            border: 1px solid #e5e7eb;
            padding: 6px 10px;
            text-align: left;
            font-size: 9px;
        }
        .rekap-value {
            font-weight: bold;
            font-size: 11px;
        }
        .badge {
            display: inline-block;
            padding: 1px 4px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h2>Laporan Absensi Karyawan</h2>
    <div class="sub">Periode: <?php echo e($formatTanggalAwal); ?> s.d <?php echo e($formatTanggalAkhir); ?></div>

    <div class="filter-info">
        <table>
            <tr>
                <td style="width: 50%">
                    <span>Status Absen:</span> <?php echo e($keterangan ?: 'Semua Status'); ?>

                </td>
                <td style="width: 50%">
                    <span>Pencarian Kata Kunci:</span> <?php echo e($search ?: '-'); ?>

                </td>
            </tr>
            <tr>
                <td>
                    <span>Total Baris Data:</span> <?php echo e($absens->count()); ?>

                </td>
                <td>
                    <span>Tanggal Unduh:</span> <?php echo e(now()->locale('id')->translatedFormat('d F Y H:i')); ?>

                </td>
            </tr>
        </table>
    </div>

    
    <div class="rekap-container">
        <div class="rekap-title">Ringkasan Ketidakhadiran (Alpa/Sakit/Cuti/Izin):</div>
        <table class="rekap-grid">
            <tr>
                <td style="background-color: #fee2e2; color: #991b1b; width: 25%">
                    Alpa: <span class="rekap-value"><?php echo e($rekap['alpa']); ?></span>
                </td>
                <td style="background-color: #fef3c7; color: #92400e; width: 25%">
                    Sakit: <span class="rekap-value"><?php echo e($rekap['sakit']); ?></span>
                </td>
                <td style="background-color: #ede9fe; color: #5b21b6; width: 25%">
                    Cuti: <span class="rekap-value"><?php echo e($rekap['cuti']); ?></span>
                </td>
                <td style="background-color: #f1f5f9; color: #475569; width: 25%">
                    Izin: <span class="rekap-value"><?php echo e($rekap['izin']); ?></span>
                </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 22%" class="text-left">Karyawan</th>
                <th style="width: 18%" class="text-left">Jabatan</th>
                <th style="width: 12%">Tanggal</th>
                <th style="width: 13%">Keterangan</th>
                <th style="width: 10%">Check In</th>
                <th style="width: 10%">Check Out</th>
                <th style="width: 10%">Terlambat</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $absens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php 
                    $menit = \App\Services\LatenessCalculator::getMinutesLate($row->scan_in, $row->tanggal_absen->format('Y-m-d')); 
                    $bgColor = match ($row->keterangan) {
                        'Hadir' => 'bg-hadir',
                        'Dinas Luar' => 'bg-dinas',
                        'Cuti' => 'bg-cuti',
                        'Sakit' => 'bg-sakit',
                        'Izin' => 'bg-izin',
                        'Alpa' => 'bg-alpa',
                        default => '',
                    };
                ?>
                <tr>
                    <td><?php echo e($i + 1); ?></td>
                    <td class="text-left">
                        <strong><?php echo e($row->karyawan->nama_karyawan); ?></strong>
                    </td>
                    <td class="text-left">
                        <?php echo e($row->karyawan->jabatan?->nama_jabatan ?? '-'); ?>

                    </td>
                    <td><?php echo e($row->tanggal_absen->format('d/m/Y')); ?></td>
                    <td class="<?php echo e($bgColor); ?>">
                        <?php echo e($row->keterangan); ?>

                    </td>
                    <td style="font-family: monospace;"><?php echo e($row->scan_in ?? '-'); ?></td>
                    <td style="font-family: monospace;"><?php echo e($row->scan_out ?? '-'); ?></td>
                    <td class="<?php echo e($menit ? 'bg-terlambat' : ''); ?>">
                        <?php echo e($menit ? $menit . ' min' : '-'); ?>

                    </td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #666; padding: 15px;">Tidak ada data ditemukan untuk periode ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\laragon\www\absenhub-v2.0\resources\views\exports\lihat-absen-pdf.blade.php ENDPATH**/ ?>