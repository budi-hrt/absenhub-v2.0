<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LihatAbsenExportController extends Controller
{
    public function pdf(Request $request)
    {
        $tanggalAwal = $request->query('tanggal_awal');
        $tanggalAkhir = $request->query('tanggal_akhir');
        $keterangan = $request->query('keterangan');
        $search = $request->query('search');

        if (empty($tanggalAwal) || empty($tanggalAkhir)) {
            abort(400, 'Tanggal awal dan akhir wajib diisi.');
        }

        $absens = Absen::with('karyawan.jabatan')
            ->whereBetween('tanggal_absen', [$tanggalAwal, $tanggalAkhir])
            ->when($keterangan, fn ($q) => $q->where('keterangan', $keterangan))
            ->when($search, function ($q) use ($search) {
                $term = trim($search);
                $q->whereHas('karyawan', function ($kq) use ($term) {
                    $kq->where(function ($sub) use ($term) {
                        $sub->where('nama_karyawan', 'like', "%{$term}%")
                            ->orWhere('nik', 'like', "%{$term}%");
                    });
                });
            })
            ->orderBy('tanggal_absen', 'desc')
            ->orderBy('karyawan_id')
            ->get();

        $rekap = [
            'alpa' => $absens->where('keterangan', 'Alpa')->count(),
            'sakit' => $absens->where('keterangan', 'Sakit')->count(),
            'cuti' => $absens->where('keterangan', 'Cuti')->count(),
            'izin' => $absens->where('keterangan', 'Izin')->count(),
        ];

        $formatTanggalAwal = Carbon::parse($tanggalAwal)->locale('id')->translatedFormat('d M Y');
        $formatTanggalAkhir = Carbon::parse($tanggalAkhir)->locale('id')->translatedFormat('d M Y');

        $pdf = Pdf::loadView('exports.lihat-absen-pdf', [
            'absens' => $absens,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'formatTanggalAwal' => $formatTanggalAwal,
            'formatTanggalAkhir' => $formatTanggalAkhir,
            'keterangan' => $keterangan,
            'search' => $search,
            'rekap' => $rekap,
        ]);

        // Kertas F4 (folio) portrait
        $pdf->setPaper('folio', 'portrait');

        return $pdf->download("laporan-absensi-{$tanggalAwal}-to-{$tanggalAkhir}.pdf");
    }
}
