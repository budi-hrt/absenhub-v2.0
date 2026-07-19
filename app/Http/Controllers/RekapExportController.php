<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\Karyawan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
}
