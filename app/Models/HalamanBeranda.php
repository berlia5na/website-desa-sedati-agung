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
        'anggaran_tahun',
        'anggaran_pendapatan',
        'realisasi_pendapatan',
        'anggaran_belanja',
        'realisasi_belanja',
        'anggaran_pembiayaan',
        'realisasi_pembiayaan',
        'anggaran_keterangan',
        'anggaran_dokumen_url',
        'status',
        'published_at',
        'published_by',
    ];

    protected $casts = [
        'sambutan_tampil' => 'boolean',
        'sorotan' => 'array',
        'widget_tampil' => 'boolean',
        'anggaran_tahun' => 'integer',
        'anggaran_pendapatan' => 'integer',
        'realisasi_pendapatan' => 'integer',
        'anggaran_belanja' => 'integer',
        'realisasi_belanja' => 'integer',
        'anggaran_pembiayaan' => 'integer',
        'realisasi_pembiayaan' => 'integer',
        'published_at' => 'datetime',
    ];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
