<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilDesa extends Model
{
    protected $table = 'profil_desa';

    protected $fillable = [
        'nama_desa',
        'deskripsi',
        'sejarah',
        'visi',
        'misi',
        'alamat',
        'telepon',
        'email',
        'website',
        'foto',
    ];
}
