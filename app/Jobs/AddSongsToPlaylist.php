<?php

namespace App\Jobs;

use App\Models\Playlist;
use App\Services\Platforms\SpotifyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AddSongsToPlaylist implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Playlist $playlist)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $access_token = $this->playlist->user->spotifyAccessToken;

        if (now() >= $access_token->expires_at) {
            $token = SpotifyService::refreshToken($access_token->refresh_token);
            $access_token = $access_token->update([
                'token' => $token['access_token'],
                'expires_at' => now()->addMinutes($token['expires_in']),
            ]);
        }
        $tracks = $this->playlist->tracks;
        $uri = [];
        foreach ($tracks as $track) {
            if($track->external_id) {
                $uri[] = $track->external_id;
            }
        }

        try {
            if (config('app.env') === 'production') {
                foreach (array_chunk($uri, 100) as $chunk) {
                    SpotifyService::addTracks($access_token->token, $this->playlist->spotify_playlist_id, $chunk);
                }
            }
             $this->playlist->update([
                 'status' => 'complete'
             ]);
        } catch (\Exception $e) {
            Log::info("Playlist {$this->playlist->id} failed");
            Log::error($e->getMessage());
        }
    }
}
