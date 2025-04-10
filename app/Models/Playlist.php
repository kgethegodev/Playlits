<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Playlist extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'tracks',
        'spotify_playlist_id',
        'spotify_link'
    ];

    protected $casts = [
        'tracks' => 'json',
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
