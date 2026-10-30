<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryPhoto extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'image_path',
        'caption',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getImageUrlAttribute(): string
    {
        return url('/storage/'.ltrim($this->image_path, '/'));
    }
}
