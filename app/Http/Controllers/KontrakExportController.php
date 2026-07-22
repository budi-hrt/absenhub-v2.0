<?php

namespace App\Http\Controllers;

use App\Models\Kontrak;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KontrakExportController extends Controller
{
    public function pdf($id)
    {
        $kontrak = Kontrak::with(['karyawan.jabatan', 'masaKontrak', 'penandatangan.jabatan'])->findOrFail($id);
        
        // Terbilang helper untuk angka nominal atau durasi
        $terbilang = function ($angka) use (&$terbilang) {
            $angka = floatval($angka);
            $bilangan = array('', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas');
            
            if ($angka < 12) {
                return $bilangan[$angka];
            } else if ($angka < 20) {
                return $terbilang($angka - 10) . ' Belas';
            } else if ($angka < 100) {
                return $terbilang(floor($angka / 10)) . ' Puluh ' . $terbilang($angka % 10);
            } else if ($angka < 200) {
                return 'Seratus ' . $terbilang($angka % 100);
            } else if ($angka < 1000) {
                return $terbilang(floor($angka / 100)) . ' Ratus ' . $terbilang($angka % 100);
            } else if ($angka < 2000) {
                return 'Seribu ' . $terbilang($angka % 1000);
            } else if ($angka < 1000000) {
                return $terbilang(floor($angka / 1000)) . ' Ribu ' . $terbilang($angka % 1000);
            } else if ($angka < 1000000000) {
                return $terbilang(floor($angka / 1000000)) . ' Juta ' . $terbilang($angka % 1000000);
            }
            return '';
        };

        // Format tanggal dalam bahasa Indonesia
        Carbon::setLocale('id');
        $tglSurat = Carbon::parse($kontrak->tanggal_surat);
        $tglMulai = Carbon::parse($kontrak->tanggal_mulai);
        $tglAkhir = Carbon::parse($kontrak->tanggal_akhir);

        $bulanIndo = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $tanggalSuratFormat = $tglSurat->day . ' ' . $bulanIndo[$tglSurat->month] . ' ' . $tglSurat->year;
        $tanggalMulaiFormat = $tglMulai->day . ' ' . $bulanIndo[$tglMulai->month] . ' ' . $tglMulai->year;
        $tanggalAkhirFormat = $tglAkhir->day . ' ' . $bulanIndo[$tglAkhir->month] . ' ' . $tglAkhir->year;

        $pdf = Pdf::loadView('exports.kontrak-pdf', [
            'kontrak' => $kontrak,
            'terbilang' => $terbilang,
            'tanggalSuratFormat' => $tanggalSuratFormat,
            'tanggalMulaiFormat' => $tanggalMulaiFormat,
            'tanggalAkhirFormat' => $tanggalAkhirFormat,
        ]);

        // Menggunakan ukuran F4 / Folio presisi: 215mm x 330mm (609.45 pt x 935.43 pt)
        $pdf->setPaper([0, 0, 609.45, 935.43], 'portrait');

        $filename = 'Kontrak_' . str_replace('/', '-', $kontrak->nomor) . '_' . str_replace(' ', '_', $kontrak->karyawan->nama_karyawan) . '.pdf';

        return $pdf->stream($filename);
    }
}
