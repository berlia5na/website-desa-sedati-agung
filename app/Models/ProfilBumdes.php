<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilBumdes extends Model
{
    protected $table = 'profil_bumdes';

    protected $fillable = [
        'nama_bumdes',
        'deskripsi',
        'sejarah',
        'visi',
        'misi',
        'alamat',
        'telepon',
        'email',
        'logo',
    ];

    public function unitBumdes()
    {
        return $this->hasMany(UnitBumdes::class, 'bumdes_id');
    }
}
