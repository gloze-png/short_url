<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ShortUrl extends Model
{
    protected $fillable =[
        'user_id',
        'original_url',
        'short_code',
        'expires_at',
        'is_active',
    ];
    protected function casts():array {
        return[
            'expires_at' => 'datetime',
            'is_active' => 'boolean',

        ];
    }
    public function clicks(): HasMany
    {
        return $this->hasMany(UrlClick::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isAvailable(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    // An active link is one that is not expired and is marked as active
    public function scopeAvailable(Builder $query): Builder
{
    return $query->where('is_active', true)
        ->where(function (Builder $q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
}
}
