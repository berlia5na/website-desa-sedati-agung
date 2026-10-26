<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HalamanBeranda extends Model
{
    protected $table = 'halaman_beranda';

    protected $fillable = [
        'judul',
        'deskripsi',
        'gambar',
        'hero_button_label',
        'hero_button_url',
        'sambutan_nama',
        'sambutan_jabatan',
        'sambutan_teks',
        'sambutan_foto',
        'sambutan_tampil',
        'sorotan',
        'widget_judul',
        'widget_teks',
        'widget_tombol',
        'widget_tautan',
        'widget_tampil',
        'status',
        'published_at',
        'published_by',
    ];

    protected $casts = [
        'sambutan_tampil' => 'boolean',
        'sorotan' => 'array',
        'widget_tampil' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
