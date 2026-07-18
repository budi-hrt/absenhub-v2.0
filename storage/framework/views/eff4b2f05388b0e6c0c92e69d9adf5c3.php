<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Karyawan</title>
    <style>
        @page {
            margin: 10mm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8px;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 3px;
            font-size: 13px;
        }
        .sub {
            text-align: center;
            font-size: 8px;
            color: #666;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 2px 3px;
            text-align: left;
            word-break: break-word;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            font-size: 7px;
            text-transform: uppercase;
        }
        tr:nth-child(even) td {
            background-color: #f9fafb;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Data Karyawan</h2>
    <div class="sub">Diekspor pada: <?php echo e($pemuat); ?></div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 15px;">No</th>
                <th style="width: 60px;">NIK</th>
                <th style="width: 100px;">Nama Karyawan</th>
                <th style="width: 40px;">JK</th>
                <th style="width: 45px;">Agama</th>
                <th style="width: 60px;">Telepon</th>
                <th style="width: 90px;">Email</th>
                <th style="width: 70px;">Jabatan</th>
                <th style="width: 55px;">Status Kerja</th>
                <th style="width: 35px;">Status</th>
                <th style="width: 50px;">Tgl Masuk</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr>
                    <td class="text-center"><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($row->nik); ?></td>
                    <td><?php echo e($row->nama_karyawan); ?></td>
                    <td><?php echo e($row->jk_karyawan === 'L' ? 'Laki-laki' : 'Perempuan'); ?></td>
                    <td><?php echo e($row->agama_karyawan ?? '-'); ?></td>
                    <td><?php echo e($row->telp_karyawan); ?></td>
                    <td><?php echo e($row->email_karyawan); ?></td>
                    <td><?php echo e($row->jabatan?->nama_jabatan ?? '-'); ?></td>
                    <td><?php echo e($row->status?->nama_status ?? '-'); ?></td>
                    <td><?php echo e($row->is_active ? 'Aktif' : 'Nonaktif'); ?></td>
                    <td><?php echo e($row->tanggal_masuk?->format('d/m/Y') ?? '-'); ?></td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </tbody>
    </table>
</body>
</html>
<?php /**PATH C:\laragon\www\absenhub-v2.0\resources\views\exports\karyawan-pdf.blade.php ENDPATH**/ ?>