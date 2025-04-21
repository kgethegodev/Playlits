<?php

namespace App\Models;

use App\Enum\PlaylistStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Playlist extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'spotify_playlist_id',
        'spotify_link',
        'status'
    ];

    protected $casts = [
        'status' => PlaylistStatus::class
    ];

    protected $hidden = [
        'user_id'
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(PlaylistTrack::class);
    }
}
