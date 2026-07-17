<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan - <?php echo e($namaBulan); ?> <?php echo e($tahun); ?></title>
    <style>
        @page { margin: 8mm; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 7px; color: #333; }
        h2 { text-align: center; margin-bottom: 2px; font-size: 12px; }
        .sub { text-align: center; font-size: 7px; color: #666; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 1px 2px; text-align: center; }
        th { background-color: #f3f4f6; font-weight: bold; font-size: 6px; text-transform: uppercase; }
        .text-left { text-align: left; }
        .bg-white { background-color: #ffffff; }
        .bg-green { background-color: #16a34a; color: #fff; }
        .bg-yellow { background-color: #facc15; }
        .bg-orange { background-color: #fb923c; color: #fff; }
        .bg-red { background-color: #dc2626; color: #fff; }
        .bg-gray { background-color: #e5e7eb; }
        .bg-alt { background-color: #f0edef; }
    </style>
</head>
<body>
    <h2>Laporan Absensi Karyawan</h2>
    <div class="sub">Bulan <?php echo e($namaBulan); ?> <?php echo e($tahun); ?></div>

    <table>
        <thead>
            <tr>
                <th class="text-left" style="width:90px;" rowspan="2">NAMA</th>
                <th class="text-left" style="width:60px;" rowspan="2">JABATAN</th>
                <th colspan="<?php echo e($totalHari); ?>">Hari Kerja (HK) di Bulan <?php echo e($namaBulan); ?> <?php echo e($tahun); ?></th>
                <th style="width:14px;" rowspan="2">HK</th>
                <th style="width:14px;" rowspan="2">C</th>
                <th style="width:14px;" rowspan="2">S</th>
                <th style="width:14px;" rowspan="2">I</th>
                <th style="width:14px;" rowspan="2">A</th>
                <th style="width:16px;" rowspan="2">%</th>
            </tr>
            <tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($d = 1; $d <= $totalHari; $d++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <th style="width:12px;"><?php echo e($d); ?></th>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $karyawans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
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
                    $persen = $totalHari > 0 ? round((($hadir + $dn + $cuti + $off + $libur) / $totalHari) * 100) : 0;
                    $isAlumni = !$k->is_active;
                ?>
                <tr>
                    <td class="text-left" style="font-weight:600;">
                        <?php echo e($k->nama_karyawan); ?>

                        <?php if($isAlumni): ?>
                            [Alumni]
                        <?php endif; ?>
                    </td>
                    <td class="text-left" style="color:#666;"><?php echo e($k->jabatan?->nama_jabatan ?? '-'); ?></td>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($d = 1; $d <= $totalHari; $d++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php
                            $absen = $records->get($d);
                            $altBg = $d % 2 === 0 ? 'background-color:#f0edef;' : '';
                            $cellStyle = '';
                            $letter = '';
                            if (!$absen) { $cellStyle = $altBg; }
                            else {
                                switch ($absen->keterangan) {
                                    case 'Hadir': $cellStyle = ''; break;
                                    case 'Dinas Luar': $cellStyle = ''; $letter = '}'; break;
                                    case 'Cuti': $cellStyle = 'background-color:#16a34a;color:#fff;font-weight:bold;'; $letter = 'C'; break;
                                    case 'Sakit': $cellStyle = 'background-color:#facc15;font-weight:bold;'; $letter = 'S'; break;
                                    case 'Izin': $cellStyle = 'background-color:#fb923c;color:#fff;font-weight:bold;'; $letter = 'I'; break;
                                    case 'Alpa': $cellStyle = 'background-color:#dc2626;color:#fff;font-weight:bold;'; $letter = 'A'; break;
                                    case 'Libur': $cellStyle = 'background-color:#e5e7eb;'; $letter = 'L'; break;
                                    case 'Off': $cellStyle = 'background-color:#9ca3af;font-weight:bold;'; $letter = 'O'; break;
                                    case 'Lainnya': $cellStyle = 'background-color:#bfdbfe;font-weight:bold;'; $letter = 'X'; break;
                                    default: $cellStyle = $altBg;
                                }
                            }
                        ?>
                        <td style="<?php echo e($cellStyle); ?>"><?php echo e($letter); ?></td>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <td><?php echo e($hk ?: '-'); ?></td>
                    <td style="background-color:#16a34a;color:#fff;font-weight:bold;"><?php echo e($cuti ?: '-'); ?></td>
                    <td style="background-color:#facc15;font-weight:bold;"><?php echo e($sakit ?: '-'); ?></td>
                    <td style="background-color:#fb923c;color:#fff;font-weight:bold;"><?php echo e($izin ?: '-'); ?></td>
                    <td style="background-color:#dc2626;color:#fff;font-weight:bold;"><?php echo e($alpa ?: '-'); ?></td>
                    <td><?php echo e($totalHari ? $persen . '%' : '-'); ?></td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </tbody>
    </table>

    <?php if($karyawans->isEmpty()): ?>
        <p style="text-align:center;padding:20px;color:#999;">Tidak ada data untuk periode ini.</p>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\laragon\www\absenhub-v2.0\resources\views/exports/laporan-bulanan-pdf.blade.php ENDPATH**/ ?>