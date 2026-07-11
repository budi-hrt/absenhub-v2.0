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
    Route::livewire('/dashboard', 'pages::karyawan.dashboard')->name('dashboard');
    Route::livewire('/users', 'pages::users.index');
    Route::livewire('/users-karyawan', 'pages::users.⚡karyawan');
    Route::livewire('/users/create', 'pages::users.create')->name('users.create');
    Route::livewire('/users/{user}/edit', 'pages::users.edit')->name('users.edit');
    // ... more
});
