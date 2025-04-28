<?php

namespace App\Jobs;

use App\Models\PlaylistTag;
use App\Models\User;
use App\Services\Platforms\AppleMusicService;
use App\Services\Platforms\SpotifyService;
use App\Services\Platforms\YoutubeMusicService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AddPlaylist implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $name, public string $url, public string $platform, public array $tags, public User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $playlist = $this->user->playlists()->create([
            'name' => $this->name,
        ]);

        foreach ($this->tags as $tag) {
            PlaylistTag::query()->create([
                'playlist_id' => $playlist->id,
                'tag_id' => $tag
            ]);
        }

        $tracks = match ($this->platform) {
            'apple'     => AppleMusicService::getTracks($this->url),
            'spotify'   => SpotifyService::getTracks($this->url),
            'youtube'   => YoutubeMusicService::getTracks($this->url),
        };

        foreach ($tracks as $track) {
            $playlist->tracks()->create([
                'name' => $track['name'],
                'artist' => $track['artist'],
                'duration' => $track['duration'],
            ]);
        }

        UpdateTracks::dispatch($playlist);
    }
}
