<?php

namespace App\Exports;

use App\Models\Absen;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DetailHarianExport implements FromCollection, WithHeadings, WithMapping
{
    protected int $karyawanId;

    protected string $bulan;

    protected string $tahun;

    public function __construct(int $karyawanId, string $bulan, string $tahun)
    {
        $this->karyawanId = $karyawanId;
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        return Absen::where('karyawan_id', $this->karyawanId)
            ->whereYear('tanggal_absen', $this->tahun)
            ->whereMonth('tanggal_absen', $this->bulan)
            ->orderBy('tanggal_absen')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Scan In',
            'Scan Out',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        return [
            $row->tanggal_absen->format('d/m/Y'),
            $row->scan_in ?? '-',
            $row->scan_out ?? '-',
            $row->keterangan,
        ];
    }
}
