<?php

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

Route::prefix('admin')->name('admin.')->controller(DashboardPageController::class)->group(function () {
    Route::get('/', 'index')->name('dashboard');
    Route::get('/beranda', 'beranda')->name('beranda');
    Route::get('/profil-desa', 'profilDesa')->name('profil-desa');
    Route::get('/berita', 'berita')->name('berita');
    Route::get('/profil-bumdes', 'profilBumdes')->name('profil-bumdes');
    Route::get('/agenda', 'agenda')->name('agenda');
    Route::get('/kontak', 'kontak')->name('kontak');
});
