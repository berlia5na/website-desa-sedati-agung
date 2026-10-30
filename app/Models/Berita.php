<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Berita extends Model
{
    protected $table = 'berita';

    protected $fillable = [
        'user_id',
        'judul',
        'slug',
        'isi',
        'excerpt',
        'kategori',
        'badge_kategori',
        'author_name',
        'read_time',
        'gambar',
        'status',
        'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getGambarUrlAttribute(): ?string
    {
        if (! $this->gambar) {
            return null;
        }

        return str_starts_with($this->gambar, 'http://') || str_starts_with($this->gambar, 'https://')
            ? $this->gambar
            : Storage::disk('public')->url($this->gambar);
    }
}
