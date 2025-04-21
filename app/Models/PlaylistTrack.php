<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaylistTrack extends Model
{
    protected $fillable = ['name', 'artist', 'duration', 'external_id', 'playlist_id', 'status', 'meta'];

    protected $casts = ['meta' => 'array'];

    public function playlist():BelongsTo
    {
        return $this->belongsTo(Playlist::class);
    }
}
