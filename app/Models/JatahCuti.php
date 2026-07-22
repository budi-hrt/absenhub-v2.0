<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JatahCuti extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Ambil jatah cuti untuk tahun tertentu (atau buat default 12)
     */
    public static function getTahun(int $tahun = null): self
    {
        $tahun = $tahun ?? now()->year;

        return self::firstOrCreate(
            ['tahun' => $tahun],
            ['jatah_cuti' => 12]
        );
    }

    /**
     * Hitung cuti terpakai untuk karyawan tertentu di tahun ini
     */
    public static function terpakaiByKaryawan(int $karyawanId, int $tahun = null): int
    {
        $tahun = $tahun ?? now()->year;

        // Opsi 1: Hitung riil dari tabel absens harian
        return Absen::where('karyawan_id', $karyawanId)
            ->where('keterangan', 'Cuti')
            ->whereYear('tanggal_absen', $tahun)
            ->count();
    }

    /**
     * Hitung sisa cuti untuk karyawan tertentu
     */
    public static function sisaByKaryawan(int $karyawanId, int $tahun = null, bool $includeMenunggu = true): int
    {
        $tahun = $tahun ?? now()->year;
        $jatah = self::getTahun($tahun);
        $terpakai = self::terpakaiByKaryawan($karyawanId, $tahun);
        
        $menunggu = 0;
        if ($includeMenunggu) {
            // Ambil cuti berstatus menunggu di tahun ini
            $menunggu = PengajuanAbsen::where('karyawan_id', $karyawanId)
                ->where('jenis', 'Cuti')
                ->where('status', 'Menunggu')
                ->get()
                ->sum(function ($p) use ($tahun) {
                    $tanggalArray = $p->tanggal ?? [];
                    return collect($tanggalArray)
                        ->filter(fn($tgl) => str_starts_with($tgl, (string) $tahun))
                        ->count();
                });
        }

        return max(0, $jatah->jatah_cuti - $terpakai - $menunggu);
    }
}
