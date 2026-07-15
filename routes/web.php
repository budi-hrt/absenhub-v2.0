<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    });

    // roles & permissions (super-admin only)
    Route::middleware('role:super-admin')->group(function () {
        Route::livewire('/roles', 'pages::roles.index');
        Route::livewire('/permissions', 'pages::permissions.index');
    });
});
