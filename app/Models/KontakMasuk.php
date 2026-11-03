<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontakMasuk extends Model
{
    protected $table = 'kontak_masuk';

    protected $fillable = [
        'nama',
        'email',
        'telepon',
        'subjek',
        'pesan',
        'status',
        'catatan_admin',
    ];
}
