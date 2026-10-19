<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    protected $table = 'berita';

    protected $fillable = [
        'user_id',
        'judul',
        'slug',
        'isi',
        'gambar',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
