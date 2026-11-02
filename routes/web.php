<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Dashboard\BerandaController;
use App\Http\Controllers\Dashboard\BeritaController;
use App\Http\Controllers\Dashboard\BumdesController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ProfilDesaController;
use App\Http\Controllers\Landing\PageController as LandingPageController;
use App\Http\Controllers\ProfilDesaLengkapController;
use Illuminate\Support\Facades\Route;

Route::controller(LandingPageController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/profil-desa', 'profilDesa')->name('profil-desa');
    Route::get('/profil-bumdes', 'profilBumdes')->name('profil-bumdes');
    Route::get('/berita', 'berita')->name('berita');
    Route::get('/berita/{slug}', 'beritaDetail')->name('berita.detail');
    Route::get('/agenda', 'agenda')->name('agenda');
    Route::get('/galeri', 'galeri')->name('galeri');
    Route::get('/kontak', 'kontak')->name('kontak');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'create'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'store'])->middleware('throttle:10,1')->name('login.submit');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');

        Route::controller(DashboardController::class)->group(function () {
            Route::get('/', 'index')->name('dashboard');
            Route::get('/agenda', 'agenda')->name('agenda');
            Route::get('/kontak', 'kontak')->name('kontak');
        });

        Route::controller(BerandaController::class)->group(function () {
            Route::get('/beranda', 'beranda')->name('beranda');
            Route::put('/beranda', 'updateBeranda')->name('beranda.update');
            Route::delete('/beranda/sambutan', 'deleteSambutan')->name('beranda.sambutan.delete');
            Route::put('/beranda/statistik', 'updateStatistik')->name('beranda.statistik.update');
        });

        Route::controller(ProfilDesaController::class)->group(function () {
            Route::get('/profil-desa', 'profilDesa')->name('profil-desa');
            Route::put('/profil-desa', 'updateProfilDesa')->name('profil-desa.update');
        });

        Route::controller(BeritaController::class)->group(function () {
            Route::get('/berita', 'berita')->name('berita');
            Route::put('/berita/header', 'updateBeritaHeader')->name('berita.header.update');
            Route::get('/berita/tulis', 'createBerita')->name('berita.create');
            Route::post('/berita', 'storeBerita')->name('berita.store');
            Route::get('/berita/{berita}/edit', 'editBerita')->name('berita.edit');
            Route::put('/berita/{berita}', 'updateBerita')->name('berita.update');
            Route::delete('/berita/{berita}', 'deleteBerita')->name('berita.delete');
            Route::post('/galeri', 'storeGalleryPhoto')->name('galeri.store');
            Route::delete('/galeri/{galleryPhoto}', 'deleteGalleryPhoto')->name('galeri.delete');
        });

        Route::controller(BumdesController::class)->group(function () {
            Route::get('/profil-bumdes', 'profilBumdes')->name('profil-bumdes');
            Route::put('/profil-bumdes', 'updateProfilBumdes')->name('profil-bumdes.update');
        });
    });
});

Route::get('/profil-desa', [ProfilDesaLengkapController::class, 'index'])
    ->name('profil-desa');
