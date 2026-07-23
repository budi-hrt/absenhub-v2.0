<?php

namespace App\Imports;

use App\Models\Absen;
use App\Models\Karyawan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AbsenImport implements ToModel, WithHeadingRow
{
    use Importable;

    public int $successCount = 0;

    public int $duplicateCount = 0;

    public array $errors = [];

    protected bool $invalidFormat = false;

    protected bool $firstRow = true;

    public function model(array $row)
    {
        if ($this->invalidFormat) {
            return null;
        }

        $noId = trim($row['no_id'] ?? '');
        $tanggal = trim($row['tanggal'] ?? '');
        $scanMasuk = trim($row['scan_masuk'] ?? '');
        $scanPulang = trim($row['scan_pulang'] ?? '');

        if ($this->firstRow) {
            $this->firstRow = false;

            if (! isset($row['no_id']) && ! isset($row['tanggal'])) {
                $this->errors[] = 'Format file tidak dikenali. Header kolom harus: No. ID, Tanggal, Scan Masuk, Scan Pulang.';
                $this->invalidFormat = true;

                return null;
            }
        }

        $baris = $this->successCount + $this->duplicateCount + count($this->errors) + 1;

        if ($noId === '') {
            $this->errors[] = "Baris {$baris}: No. ID kosong.";

            return null;
        }

        if (! is_numeric($noId)) {
            $this->errors[] = "Baris {$baris}: No. ID \"{$noId}\" bukan angka.";

            return null;
        }

        if ($tanggal === '') {
            $this->errors[] = "Baris {$baris}: Tanggal kosong.";

            return null;
        }

        if (! preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $tanggal)) {
            $this->errors[] = "Baris {$baris}: format tanggal \"{$tanggal}\" tidak valid (harus dd/mm/YYYY).";

            return null;
        }

        if ($scanMasuk !== '' && ! preg_match('/^\d{2}:\d{2}$/', $scanMasuk)) {
            $this->errors[] = "Baris {$baris}: format Scan Masuk \"{$scanMasuk}\" tidak valid (harus HH:MM).";

            return null;
        }

        if ($scanPulang !== '' && ! preg_match('/^\d{2}:\d{2}$/', $scanPulang)) {
            $this->errors[] = "Baris {$baris}: format Scan Pulang \"{$scanPulang}\" tidak valid (harus HH:MM).";

            return null;
        }

        $karyawan = Karyawan::where('pin_mesin', $noId)->first();
        if (! $karyawan) {
            $this->errors[] = "Baris {$baris}: No. ID {$noId} tidak ditemukan.";

            return null;
        }

        try {
            $dateObj = Carbon::createFromFormat('d/m/Y', $tanggal);
        } catch (\Exception $e) {
            $this->errors[] = "Baris {$baris}: tanggal {$tanggal} tidak valid.";

            return null;
        }

        $exists = Absen::where('karyawan_id', $karyawan->id)
            ->where('tanggal_absen', $dateObj->format('Y-m-d'))
            ->exists();

        if ($exists) {
            $this->duplicateCount++;
            $this->errors[] = "Baris {$baris}: No. ID {$noId} tanggal {$tanggal} sudah ada, dilewati.";

            return null;
        }

        $this->successCount++;

        return new Absen([
            'karyawan_id' => $karyawan->id,
            'tanggal_absen' => $dateObj->format('Y-m-d'),
            'scan_in' => $scanMasuk ?: null,
            'scan_out' => $scanPulang ?: null,
            'keterangan' => $scanMasuk ? 'Hadir' : 'Tidak Absen',
        ]);
    }
}
