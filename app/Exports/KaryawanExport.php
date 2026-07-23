<?php

namespace App\Exports;

use App\Models\Karyawan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KaryawanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $search;

    protected $filterJabatan;

    protected $filterStatus;

    protected $filterAgama;

    protected $filterKerja;

    public function __construct(
        string $search = '',
        string $filterJabatan = '',
        string $filterStatus = '',
        string $filterAgama = '',
        string $filterKerja = '',
    ) {
        $this->search = $search;
        $this->filterJabatan = $filterJabatan;
        $this->filterStatus = $filterStatus;
        $this->filterAgama = $filterAgama;
        $this->filterKerja = $filterKerja;
    }

    public function collection()
    {
        return Karyawan::with(['jabatan', 'status'])
            ->when($this->search, fn ($q) => $q->where('nama_karyawan', 'like', "%{$this->search}%")
                ->orWhere('nik', 'like', "%{$this->search}%"))
            ->when($this->filterJabatan, fn ($q) => $q->where('jabatan_id', $this->filterJabatan))
            ->when($this->filterStatus === 'aktif', fn ($q) => $q->where('is_active', true))
            ->when($this->filterStatus === 'nonaktif', fn ($q) => $q->where('is_active', false))
            ->when($this->filterAgama, fn ($q) => $q->where('agama_karyawan', $this->filterAgama))
            ->when($this->filterKerja, fn ($q) => $q->where('status_id', $this->filterKerja))
            ->orderBy('nama_karyawan')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'NIK',
            'Nama Karyawan',
            'Jenis Kelamin',
            'Agama',
            'Telepon',
            'Email',
            'Alamat',
            'Jabatan',
            'Status Kerja',
            'Status Aktif',
            'Tanggal Masuk',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->nik,
            $row->nama_karyawan,
            $row->jk_karyawan === 'L' ? 'Laki-laki' : 'Perempuan',
            $row->agama_karyawan ?? '-',
            $row->telp_karyawan,
            $row->email_karyawan,
            $row->alamat_karyawan,
            $row->jabatan?->nama_jabatan ?? '-',
            $row->status?->nama_status ?? '-',
            $row->is_active ? 'Aktif' : 'Nonaktif',
            $row->tanggal_masuk?->format('d/m/Y') ?? '-',
        ];
    }
}
