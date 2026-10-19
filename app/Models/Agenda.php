<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = 'agenda';

    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'sesi',
        'kategori',
        'badge_kategori',
        'lokasi',
        'penyelenggara',
        'pic',
        'gambar',
        'status',
        'is_prioritas',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_prioritas' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
