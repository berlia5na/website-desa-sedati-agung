<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
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
