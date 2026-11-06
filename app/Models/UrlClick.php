<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UrlClick extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'short_url_id',
        'ip_address',
        'user_agent',
        'referer',
        'clicked_at',
    ];

    protected function casts(): array
    {
        return [
            'clicked_at' => 'datetime',
        ];
    }

    public function shortUrl(): BelongsTo
    {
        return $this->belongsTo(ShortUrl::class);
    }
}