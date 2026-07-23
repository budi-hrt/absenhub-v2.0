<?php

namespace App\Services;

use App\Models\PengaturanAbsen;
use Carbon\Carbon;

class LatenessCalculator
{
    public static function getMinutesLate(?string $scanIn, string $tanggalAbsen): ?int
    {
        if (! $scanIn) {
            return null;
        }

        $setting = PengaturanAbsen::where('tanggal_mulai', '<=', $tanggalAbsen)
            ->where(function ($q) use ($tanggalAbsen) {
                $q->whereNull('tanggal_akhir')
                    ->orWhere('tanggal_akhir', '>=', $tanggalAbsen);
            })
            ->orderBy('tanggal_mulai', 'desc')
            ->first();

        if (! $setting) {
            return null;
        }

        $jamMasuk = Carbon::parse($setting->jam_masuk);
        $scanInTime = Carbon::parse($scanIn);
        $batas = $jamMasuk->copy()->addMinutes($setting->toleransi_menit);

        if ($scanInTime->greaterThan($batas)) {
            return (int) $jamMasuk->diffInMinutes($scanInTime);
        }

        return null;
    }

    public static function getJamMasuk(?string $tanggalAbsen): ?string
    {
        $setting = PengaturanAbsen::where('tanggal_mulai', '<=', $tanggalAbsen)
            ->where(function ($q) use ($tanggalAbsen) {
                $q->whereNull('tanggal_akhir')
                    ->orWhere('tanggal_akhir', '>=', $tanggalAbsen);
            })
            ->orderBy('tanggal_mulai', 'desc')
            ->first();

        return $setting?->jam_masuk;
    }
}
