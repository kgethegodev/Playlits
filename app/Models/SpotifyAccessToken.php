<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpotifyAccessToken extends Model
{
    protected $fillable = [
        'token',
        'refresh_token',
        'user_id',
        'expires_at'
    ];
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return now() > $this->expires_at;
    }
}
