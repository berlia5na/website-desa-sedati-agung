<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatistikDesa extends Model
{
    protected $table = 'statistik_desa';

    protected $fillable = [
        'jumlah_penduduk',
        'jumlah_usaha_bumdes',
        'jumlah_umkm',
    ];
}
