<?php

namespace App\Http\Controllers;

use App\Models\Kontrak;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KontrakExportController extends Controller
{
    public function pdf(int $id)
    {
        $kontrak = Kontrak::with(['karyawan', 'karyawan.jabatan', 'karyawan.status', 'masaKontrak', 'penandatangan'])->findOrFail($id);

        $pdf = Pdf::loadView('exports.kontrak-pdf', compact('kontrak'));
        
        // Atur ukuran kertas ke A4 (Portrait by default)
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('Kontrak_Kerja_' . str_replace(' ', '_', $kontrak->karyawan->nama_karyawan) . '_' . date('Ymd') . '.pdf');
    }
}
