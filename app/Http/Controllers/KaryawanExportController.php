<?php

namespace App\Http\Controllers;

use App\Exports\KaryawanExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class KaryawanExportController extends Controller
{
    public function excel(Request $request)
    {
        $export = new KaryawanExport(
            $request->query('search', ''),
            $request->query('filterJabatan', ''),
            $request->query('filterStatus', ''),
            $request->query('filterAgama', ''),
            $request->query('filterKerja', ''),
        );

        return Excel::download($export, 'data-karyawan.xlsx');
    }

    public function pdf(Request $request)
    {
        $export = new KaryawanExport(
            $request->query('search', ''),
            $request->query('filterJabatan', ''),
            $request->query('filterStatus', ''),
            $request->query('filterAgama', ''),
            $request->query('filterKerja', ''),
        );

        $data = $export->collection();

        $pdf = Pdf::loadView('exports.karyawan-pdf', [
            'data' => $data,
            'pemuat' => now()->format('d/m/Y H:i'),
        ]);

        $pdf->setPaper('folio', 'landscape');
        return $pdf->download('data-karyawan.pdf');
    }
}
