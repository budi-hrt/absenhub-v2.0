<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detail Harian - {{ $namaKaryawan }} - {{ $namaBulan }} {{ $tahun }}</title>
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
    <div class="sub">{{ $namaBulan }} {{ $tahun }}</div>

    <div class="info">
        <p><span>Nama:</span> {{ $namaKaryawan }}</p>
        <p><span>NIK:</span> {{ $nik ?? '-' }} &nbsp;&nbsp; <span>Jabatan:</span> {{ $jabatan ?? '-' }}</p>
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
            @foreach ($absens as $i => $a)
                @php $menit = \App\Services\LatenessCalculator::getMinutesLate($a->scan_in, $a->tanggal_absen->format('Y-m-d')); @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="text-left">{{ $a->tanggal_absen->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($a->tanggal_absen)->locale('id')->translatedFormat('l') }}</td>
                    <td>{{ $a->scan_in ?? '-' }}</td>
                    <td>{{ $a->scan_out ?? '-' }}</td>
                    <td class="{{ $menit ? 'bg-terlambat' : '' }}">
                        {{ $menit ? $menit . ' min' : '-' }}
                    </td>
                    <td class="text-left">{{ $a->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($rekap)
        <div class="rekap">
            <table>
                <tr>
                    <td><strong>Rekap:</strong></td>
                    @foreach ($rekap as $label => $count)
                        @if ($count > 0)
                            <td>{{ $label }}: {{ $count }}</td>
                        @endif
                    @endforeach
                </tr>
            </table>
        </div>
    @endif
</body>
</html>
