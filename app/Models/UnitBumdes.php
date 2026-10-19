<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitBumdes extends Model
{
    protected $table = 'unit_bumdes';

    protected $fillable = [
        'bumdes_id',
        'nama_unit',
        'jenis',
        'deskripsi',
        'harga',
        'gambar',
        'status',
    ];

    public function profilBumdes()
    {
        return $this->belongsTo(ProfilBumdes::class, 'bumdes_id');
    }
}
