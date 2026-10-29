<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitBumdes extends Model
{
    protected $table = 'unit_bumdes';

    protected $fillable = [
        'bumdes_id',
        'nama_unit',
        'jenis',
        'deskripsi',
        'harga',
        'telepon',
        'gambar',
        'status',
    ];

    public function profilBumdes(): BelongsTo
    {
        return $this->belongsTo(ProfilBumdes::class, 'bumdes_id');
    }
}
