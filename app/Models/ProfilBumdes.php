<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'foto',
        'legalitas',
        'pengurus',
    ];

    protected function casts(): array
    {
        return [
            'legalitas' => 'array',
            'pengurus' => 'array',
        ];
    }

    public function unitBumdes(): HasMany
    {
        return $this->hasMany(UnitBumdes::class, 'bumdes_id');
    }
}
