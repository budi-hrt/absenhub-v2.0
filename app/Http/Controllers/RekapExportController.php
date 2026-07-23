<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\Karyawan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapExportController extends Controller
{
    public function bulanan(Request $request)
    {
        $bulan = $request->query('bulan', now()->format('m'));
        $tahun = $request->query('tahun', now()->format('Y'));
        $search = $request->query('search', '');

        $lastDay = Carbon::create((int) $tahun, (int) $bulan)->endOfMonth();

        $karyawans = Karyawan::with('jabatan')
            ->where('tanggal_masuk', '<=', $lastDay)
            ->where(function ($q) use ($tahun, $bulan) {
                $q->where('is_active', true)
                    ->orWhereHas('absens', function ($aq) use ($tahun, $bulan) {
                        $aq->whereYear('tanggal_absen', $tahun)
                            ->whereMonth('tanggal_absen', $bulan);
                    });
            })
            ->when($search, function ($q) use ($search) {
                $term = trim($search);
                $q->where(function ($sub) use ($term) {
                    $sub->where('nama_karyawan', 'like', "%{$term}%")
                        ->orWhere('nik', 'like', "%{$term}%");
                });
            })
            ->orderBy('nama_karyawan')
            ->get();

        $absenRecords = Absen::whereIn('karyawan_id', $karyawans->pluck('id'))
            ->whereYear('tanggal_absen', (int) $tahun)
            ->whereMonth('tanggal_absen', (int) $bulan)
            ->get()
            ->groupBy('karyawan_id');

        $totalHari = Carbon::create((int) $tahun, (int) $bulan)->daysInMonth;

        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];
        $namaBulan = $months[$bulan] ?? $bulan;

        $pdf = Pdf::loadView('exports.rekap-bulanan-pdf', [
            'karyawans' => $karyawans,
            'absenRecords' => $absenRecords,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $namaBulan,
            'totalHari' => $totalHari,
            'search' => $search,
        ]);

        $pdf->setPaper('folio', 'portrait');

        return $pdf->download("rekap-bulanan-{$bulan}-{$tahun}.pdf");
    }

    public function tahunan(Request $request)
    {
        $tahun = $request->query('tahun', now()->format('Y'));
        $search = $request->query('search', '');

        $lastDay = Carbon::create((int) $tahun, 12)->endOfYear();

        $karyawans = Karyawan::with('jabatan')
            ->where('tanggal_masuk', '<=', $lastDay)
            ->where(function ($q) use ($tahun) {
                $q->where('is_active', true)
                    ->orWhereHas('absens', function ($aq) use ($tahun) {
                        $aq->whereYear('tanggal_absen', $tahun);
                    });
            })
            ->when($search, function ($q) use ($search) {
                $term = trim($search);
                $q->where(function ($sub) use ($term) {
                    $sub->where('nama_karyawan', 'like', "%{$term}%")
                        ->orWhere('nik', 'like', "%{$term}%");
                });
            })
            ->orderBy('nama_karyawan')
            ->get();

        $absenRecords = Absen::whereIn('karyawan_id', $karyawans->pluck('id'))
            ->whereYear('tanggal_absen', (int) $tahun)
            ->get()
            ->groupBy('karyawan_id');

        $totalHari = Carbon::create((int) $tahun, 12)->endOfYear()->dayOfYear;

        $pdf = Pdf::loadView('exports.rekap-tahunan-pdf', [
            'karyawans' => $karyawans,
            'absenRecords' => $absenRecords,
            'tahun' => $tahun,
            'totalHari' => $totalHari,
            'search' => $search,
        ]);

        $pdf->setPaper('folio', 'portrait');

        return $pdf->download("rekap-tahunan-{$tahun}.pdf");
    }

    public function performa(Request $request)
    {
        $tahun = $request->query('tahun', now()->format('Y'));
        $search = $request->query('search', '');
        $filterStatus = $request->query('filterStatus', 'tetap');

        $karyawans = Karyawan::with('jabatan')
            ->where('is_active', true)
            ->when($filterStatus === 'tetap', function ($q) {
                $q->where('status_id', 1);
            })
            ->when($filterStatus === 'kontrak', function ($q) {
                $q->where('status_id', 2);
            })
            ->when($search, function ($q) use ($search) {
                $term = trim($search);
                $q->where(function ($sub) use ($term) {
                    $sub->where('nama_karyawan', 'like', "%{$term}%")
                        ->orWhere('nik', 'like', "%{$term}%");
                });
            })
            ->get();

        $karyawanIds = $karyawans->pluck('id');

        $absenStats = DB::table('absens')
            ->whereIn('karyawan_id', $karyawanIds)
            ->whereYear('tanggal_absen', (int) $tahun)
            ->select('karyawan_id', 'keterangan', DB::raw('count(*) as total'))
            ->groupBy('karyawan_id', 'keterangan')
            ->get()
            ->groupBy('karyawan_id');

        $performanceList = $karyawans->map(function ($k) use ($absenStats) {
            $stats = $absenStats[$k->id] ?? collect();
            $hk = $stats->sum('total');
            $hadir = $stats->where('keterangan', 'Hadir')->sum('total');
            $dn = $stats->where('keterangan', 'Dinas Luar')->sum('total');
            $cuti = $stats->where('keterangan', 'Cuti')->sum('total');
            $sakit = $stats->where('keterangan', 'Sakit')->sum('total');
            $izin = $stats->where('keterangan', 'Izin')->sum('total');
            $alpa = $stats->where('keterangan', 'Alpa')->sum('total');
            $off = $stats->where('keterangan', 'Off')->sum('total');
            $libur = $stats->where('keterangan', 'Libur')->sum('total');
            $lainnya = $stats->where('keterangan', 'Lainnya')->sum('total');
            $persen = $hk > 0 ? max(0, round(100 - ($alpa * 3) - ($izin * 2) - ($sakit * 1) - ($lainnya * 0.5), 1)) : 0;

            return [
                'karyawan' => $k,
                'hk' => $hk,
                'hadir' => $hadir,
                'dn' => $dn,
                'cuti' => $cuti,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpa' => $alpa,
                'off' => $off,
                'libur' => $libur,
                'lainnya' => $lainnya,
                'rate' => $persen,
            ];
        })->sort(function ($a, $b) {
            if ($a['rate'] === $b['rate']) {
                return strcmp($a['karyawan']->nama_karyawan, $b['karyawan']->nama_karyawan);
            }

            return $b['rate'] <=> $a['rate'];
        })->values();

        $pdf = Pdf::loadView('exports.performa-pdf', [
            'performanceList' => $performanceList,
            'tahun' => $tahun,
            'filterStatus' => $filterStatus,
            'search' => $search,
        ]);

        $pdf->setPaper('folio', 'portrait');

        $statusLabel = $filterStatus === 'tetap' ? 'Karyawan-Tetap' : 'Karyawan-Kontrak';

        return $pdf->download("performa-absensi-{$statusLabel}-{$tahun}.pdf");
    }
}
