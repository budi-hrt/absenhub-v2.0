<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbsenTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'No. ID',
            'Tanggal',
            'Scan Masuk',
            'Scan Pulang',
        ];
    }

    public function array(): array
    {
        return [
            ['1', '12/02/2026', '07:48', '17:20'],
            ['2', '12/02/2026', '08:05', '17:15'],
            ['3', '13/02/2026', '', ''],
        ];
    }
}
