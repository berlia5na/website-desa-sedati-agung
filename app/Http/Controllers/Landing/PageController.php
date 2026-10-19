<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function home()
    {
        return view('landing.home');
    }

    public function profilDesa()
    {
        return view('landing.profil-desa');
    }

    public function profilBumdes()
    {
        return view('landing.profil-bumdes');
    }

    public function berita()
    {
        return view('landing.berita');
    }

    public function agenda()
    {
        return view('landing.agenda');
    }

    public function kontak()
    {
        return view('landing.kontak');
    }
}
