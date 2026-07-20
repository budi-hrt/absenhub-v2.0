<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Tahunan - {{ $tahun }}</title>
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
    <h2>Rekap Absensi Tahunan Karyawan</h2>
    <div class="sub">Tahun {{ $tahun }}</div>

    <div class="info">
        <table>
            <tr>
                <td style="width: 50%">
                    <span>Periode:</span> Tahun {{ $tahun }}
                </td>
                <td style="width: 50%">
                    <span>Pencarian Kata Kunci:</span> {{ $search ?: '-' }}
                </td>
            </tr>
            <tr>
                <td>
                    <span>Total Karyawan:</span> {{ $karyawans->count() }} Orang
                </td>
                <td>
                    <span>Tanggal Unduh:</span> {{ now()->locale('id')->translatedFormat('d F Y H:i') }}
                </td>
            </tr>
        </table>
        <div class="legend">
            <strong>Keterangan Kolom:</strong> HK = Hari Kerja | H = Hadir | DL = Dinas Luar | C = Cuti | S = Sakit | I = Izin | A = Alpa | Off = Off | L = Libur | Ln = Lainnya
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th class="text-left" style="width: 23%">Nama</th>
                <th class="text-left" style="width: 17%">Jabatan</th>
                <th style="width: 5%">HK</th>
                <th class="bg-hadir" style="width: 5%">H</th>
                <th class="bg-dinas" style="width: 5%">DL</th>
                <th class="bg-cuti" style="width: 5%">C</th>
                <th class="bg-sakit" style="width: 5%">S</th>
                <th class="bg-izin" style="width: 5%">I</th>
                <th class="bg-alpa" style="width: 5%">A</th>
                <th class="bg-off" style="width: 5%">Off</th>
                <th class="bg-libur" style="width: 5%">L</th>
                <th class="bg-lain" style="width: 5%">Ln</th>
                <th style="width: 10%">%</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($karyawans as $i => $k)
                @php
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
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="text-left">
                        <strong>{{ $k->nama_karyawan }}</strong>
                        @if (!$k->is_active)
                            <span style="color: #666; font-size: 7px;">(Alumni)</span>
                        @endif
                    </td>
                    <td class="text-left">{{ $k->jabatan?->nama_jabatan ?? '-' }}</td>
                    <td>{{ $hk ?: '-' }}</td>
                    <td class="bg-hadir">{{ $hadir ?: '-' }}</td>
                    <td class="bg-dinas">{{ $dn ?: '-' }}</td>
                    <td class="bg-cuti">{{ $cuti ?: '-' }}</td>
                    <td class="bg-sakit">{{ $sakit ?: '-' }}</td>
                    <td class="bg-izin">{{ $izin ?: '-' }}</td>
                    <td class="bg-alpa">{{ $alpa ?: '-' }}</td>
                    <td class="bg-off">{{ $off ?: '-' }}</td>
                    <td class="bg-libur">{{ $libur ?: '-' }}</td>
                    <td class="bg-lain">{{ $lainnya ?: '-' }}</td>
                    <td style="font-weight: bold;">{{ $totalHari ? $persen . '%' : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" style="text-align: center; color: #666; padding: 15px;">Tidak ada data ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
