<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    public function beranda()
    {
        return view('dashboard.beranda');
    }

    public function profilDesa()
    {
        return view('dashboard.profil-desa');
    }

    public function berita()
    {
        return view('dashboard.berita');
    }

    public function profilBumdes()
    {
        return view('dashboard.profil-bumdes');
    }

    public function agenda()
    {
        return view('dashboard.agenda');
    }

    public function kontak()
    {
        return view('dashboard.kontak');
    }
}
