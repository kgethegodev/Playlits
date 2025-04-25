<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'type',
    ];

    public function playlists():BelongsToMany
    {
        return $this->belongsToMany(Playlist::class)->using(PlaylistTag::class);
    }
}
