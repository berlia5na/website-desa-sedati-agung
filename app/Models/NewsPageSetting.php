<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsPageSetting extends Model
{
    protected $fillable = [
        'badge',
        'title',
        'description',
    ];
}
