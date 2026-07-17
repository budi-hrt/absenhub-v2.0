<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\Karyawan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanBulananController extends Controller
{
    public function pdf(Request $request)
    {
        $bulan = $request->query('bulan', now()->format('m'));
        $tahun = $request->query('tahun', now()->format('Y'));
        $search = $request->query('search', '');

        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        $namaBulan = $months[$bulan] ?? $bulan;
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

        $pdf = Pdf::loadView('exports.laporan-bulanan-pdf', [
            'karyawans' => $karyawans,
            'absenRecords' => $absenRecords,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $namaBulan,
            'totalHari' => $totalHari,
        ]);

        $pdf->setPaper('folio', 'landscape');

        return $pdf->download("laporan-bulanan-{$bulan}-{$tahun}.pdf");
    }
}
