<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HalamanBeranda extends Model
{
    protected $table = 'halaman_beranda';

    protected $fillable = [
        'judul',
        'deskripsi',
        'gambar',
        'status',
    ];
}
