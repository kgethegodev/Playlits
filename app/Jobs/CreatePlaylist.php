<?php

namespace App\Jobs;

use App\Models\Playlist;
use App\Models\SpotifyAccessToken;
use App\Models\User;
use App\Services\Platforms\AppleMusicService;
use App\Services\Platforms\SpotifyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreatePlaylist implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $name, public string $url, public string $platform, public string $auth_code, public User $user)
    {
        //
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        $tracks = match ($this->platform) {
            'apple' => AppleMusicService::getTracks($this->url),
            'spotify' => SpotifyService::getTracks($this->url),
        };

        $playlist = $this->user->playlists()->create([
            'name' => $this->name,
            'tracks' => $tracks,
        ]);

        $access_token = $this->user->spotifyAccessToken ?? null;
        if (!$access_token) {
            $token = SpotifyService::createAuthAccessToken($this->auth_code);
            $access_token = $this->user->spotifyAccessToken()->create([
                'token'  => $token['access_token'],
                'refresh_token' => $token['refresh_token'],
                'expires_at'    => now()->addMinutes($token['expires_in']),
            ]);
        } elseif ($access_token->isExpired()) {
            $token = SpotifyService::refreshToken($access_token->refresh_token);
            $access_token = $this->user->spotifyAccessToken()->update([
                'token'  => $token['access_token'],
                'refresh_token' => $token['refresh_token'],
                'expires_at'    => now()->addMinutes($token['expires_in']),
            ]);
        }

        $spotify_user_id = $this->user->spotify_user_id ?? null;
        if (!$spotify_user_id) {
            $spotify_user = SpotifyService::me($access_token->token);
            $this->user->update([
                'spotify_user_id' => $spotify_user['id'],
            ]);
            $spotify_user_id = $spotify_user['id'];
        }

        $spotify_playlist = SpotifyService::createPlaylist($this->name, $access_token->token, $spotify_user_id);

        $playlist->update([
            'spotify_playlist_id'   => $spotify_playlist['id'],
            'spotify_link'          => $spotify_playlist['uri'],
        ]);

        foreach ($tracks as $track) {

        }
    }
}
