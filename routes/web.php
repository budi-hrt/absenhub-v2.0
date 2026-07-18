<?php

use App\Exports\AbsenTemplateExport;
use App\Exports\DetailHarianExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::livewire('/login', 'pages::login')->name('login');

// Define the logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');

// Protected routes here
Route::middleware('auth')->group(function () {
    Route::livewire('/', 'pages::index');
    Route::livewire('/users-karyawan', 'pages::users.⚡karyawan');

    // Routes khusus role karyawan
    Route::middleware('role:karyawan')->group(function () {
        Route::livewire('/dashboard', 'pages::karyawan.dashboard')->name('dashboard');
    });

    // Routes untuk admin/manager/operator/super-admin
    Route::middleware('role:admin|super-admin|operator|manager')->group(function () {
        Route::livewire('/users', 'pages::users.index');
        Route::livewire('/users/create', 'pages::users.create')->name('users.create');
        Route::livewire('/users/{user}/edit', 'pages::users.edit')->name('users.edit');
        Route::livewire('/karyawan', 'pages::karyawan.index')->name('karyawan.index');
        Route::livewire('/karyawan/create', 'pages::karyawan.create')->name('karyawan.create');
        Route::livewire('/karyawan/{karyawan}/edit', 'pages::karyawan.edit')->name('karyawan.edit');

        Route::get('/karyawan/export/excel', [App\Http\Controllers\KaryawanExportController::class, 'excel'])
            ->name('karyawan.export.excel');
        Route::get('/karyawan/export/pdf', [App\Http\Controllers\KaryawanExportController::class, 'pdf'])
            ->name('karyawan.export.pdf');

        Route::livewire('/master/jabatan', 'pages::master.jabatan')->name('master.jabatan');
        Route::livewire('/master/status-kerja', 'pages::master.status-kerja')->name('master.status-kerja');
        Route::livewire('/master/masa-kontrak', 'pages::master.masa-kontrak')->name('master.masa-kontrak');
        Route::livewire('/master/penandatangan', 'pages::master.penandatangan')->name('master.penandatangan');

        // Manajemen Absensi
        Route::livewire('/absen/kelola', 'pages::absen.kelola-absen')->name('absen.kelola');
        Route::livewire('/absen/lihat', 'pages::absen.lihat-absen')->name('absen.lihat');
        Route::livewire('/absen/detail-harian', 'pages::absen.detail-harian')->name('absen.detail-harian');
        Route::livewire('/absen/rekap-bulanan', 'pages::absen.rekap-bulanan')->name('absen.rekap-bulanan');
        Route::livewire('/absen/rekap-tahunan', 'pages::absen.rekap-tahunan')->name('absen.rekap-tahunan');
        Route::livewire('/absen/laporan-bulanan', 'pages::absen.laporan-bulanan')->name('absen.laporan-bulanan');
        Route::get('/absen/laporan-bulanan/pdf', [App\Http\Controllers\LaporanBulananController::class, 'pdf'])
            ->name('absen.laporan-bulanan.pdf');
        Route::get('/absen/template', function () {
            return Excel::download(new AbsenTemplateExport, 'template-absensi.xlsx');
        })->name('absen.template');
        Route::get('/absen/detail-harian/export', function () {
            $export = new DetailHarianExport(
                karyawanId: (int) request('karyawan_id'),
                bulan: request('bulan'),
                tahun: request('tahun'),
            );
            return Excel::download($export, 'detail-harian.xlsx');
        })->name('absen.detail-harian.export');

        Route::get('/absen/detail-harian/pdf', function () {
            $karyawanId = (int) request('karyawan_id');
            $bulan = request('bulan', now()->format('m'));
            $tahun = request('tahun', now()->format('Y'));

            $karyawan = \App\Models\Karyawan::with('jabatan')->find($karyawanId);
            if (!$karyawan) abort(404);

            $absens = \App\Models\Absen::where('karyawan_id', $karyawanId)
                ->whereYear('tanggal_absen', $tahun)
                ->whereMonth('tanggal_absen', $bulan)
                ->orderBy('tanggal_absen')
                ->get();

            $months = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
            ];

            $rekap = $absens->groupBy('keterangan')->map->count();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.detail-harian-pdf', [
                'absens' => $absens,
                'namaKaryawan' => $karyawan->nama_karyawan,
                'nik' => $karyawan->nik,
                'jabatan' => $karyawan->jabatan?->nama_jabatan,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'namaBulan' => $months[$bulan] ?? $bulan,
                'rekap' => $rekap,
            ]);

            $pdf->setPaper('folio', 'portrait');

            return $pdf->download("detail-harian-{$karyawan->nama_karyawan}-{$bulan}-{$tahun}.pdf");
        })->name('absen.detail-harian.pdf');

        // Pengaturan
        Route::livewire('/pengaturan/absen', 'pages::pengaturan.absen')->name('pengaturan.absen');
    });

    // roles & permissions (super-admin only)
    Route::middleware('role:super-admin')->group(function () {
        Route::livewire('/roles', 'pages::roles.index');
        Route::livewire('/permissions', 'pages::permissions.index');
    });
});
