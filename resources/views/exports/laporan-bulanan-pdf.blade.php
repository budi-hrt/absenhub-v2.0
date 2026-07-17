<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan - {{ $namaBulan }} {{ $tahun }}</title>
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
    <div class="sub">Bulan {{ $namaBulan }} {{ $tahun }}</div>

    <table>
        <thead>
            <tr>
                <th class="text-left" style="width:90px;" rowspan="2">NAMA</th>
                <th class="text-left" style="width:60px;" rowspan="2">JABATAN</th>
                <th colspan="{{ $totalHari }}">Hari Kerja (HK) di Bulan {{ $namaBulan }} {{ $tahun }}</th>
                <th style="width:14px;" rowspan="2">HK</th>
                <th style="width:14px;" rowspan="2">C</th>
                <th style="width:14px;" rowspan="2">S</th>
                <th style="width:14px;" rowspan="2">I</th>
                <th style="width:14px;" rowspan="2">A</th>
                <th style="width:16px;" rowspan="2">%</th>
            </tr>
            <tr>
                @for ($d = 1; $d <= $totalHari; $d++)
                    <th style="width:12px;">{{ $d }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ($karyawans as $k)
                @php
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
                @endphp
                <tr>
                    <td class="text-left" style="font-weight:600;">
                        {{ $k->nama_karyawan }}
                        @if ($isAlumni)
                            [Alumni]
                        @endif
                    </td>
                    <td class="text-left" style="color:#666;">{{ $k->jabatan?->nama_jabatan ?? '-' }}</td>
                    @for ($d = 1; $d <= $totalHari; $d++)
                        @php
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
                        @endphp
                        <td style="{{ $cellStyle }}">{{ $letter }}</td>
                    @endfor
                    <td>{{ $hk ?: '-' }}</td>
                    <td style="background-color:#16a34a;color:#fff;font-weight:bold;">{{ $cuti ?: '-' }}</td>
                    <td style="background-color:#facc15;font-weight:bold;">{{ $sakit ?: '-' }}</td>
                    <td style="background-color:#fb923c;color:#fff;font-weight:bold;">{{ $izin ?: '-' }}</td>
                    <td style="background-color:#dc2626;color:#fff;font-weight:bold;">{{ $alpa ?: '-' }}</td>
                    <td>{{ $totalHari ? $persen . '%' : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($karyawans->isEmpty())
        <p style="text-align:center;padding:20px;color:#999;">Tidak ada data untuk periode ini.</p>
    @endif
</body>
</html>
