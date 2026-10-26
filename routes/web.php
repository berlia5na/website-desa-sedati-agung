<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Dashboard\PageController as DashboardPageController;
use App\Http\Controllers\Landing\PageController as LandingPageController;
use Illuminate\Support\Facades\Route;

Route::controller(LandingPageController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/profil-desa', 'profilDesa')->name('profil-desa');
    Route::get('/profil-bumdes', 'profilBumdes')->name('profil-bumdes');
    Route::get('/berita', 'berita')->name('berita');
    Route::get('/agenda', 'agenda')->name('agenda');
    Route::get('/kontak', 'kontak')->name('kontak');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'create'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'store'])->middleware('throttle:10,1')->name('login.submit');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');

        Route::controller(DashboardPageController::class)->group(function () {
            Route::get('/', 'index')->name('dashboard');
            Route::get('/beranda', 'beranda')->name('beranda');
            Route::put('/beranda', 'updateBeranda')->name('beranda.update');
            Route::delete('/beranda/sambutan', 'deleteSambutan')->name('beranda.sambutan.delete');
            Route::put('/beranda/statistik', 'updateStatistik')->name('beranda.statistik.update');
            Route::get('/profil-desa', 'profilDesa')->name('profil-desa');
            Route::get('/berita', 'berita')->name('berita');
            Route::get('/profil-bumdes', 'profilBumdes')->name('profil-bumdes');
            Route::get('/agenda', 'agenda')->name('agenda');
            Route::get('/kontak', 'kontak')->name('kontak');
        });
    });
});
