<?php

namespace App\Jobs;

use App\Mail\PlaylistReadyMail;
use App\Models\Playlist;
use App\Models\PlaylistAction;
use App\Models\User;
use App\Services\Platforms\SpotifyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AddSongsToPlaylist implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Playlist $playlist, public User $user, public PlaylistAction $playlist_action)
    {
        //
    }

    /**
     * Execute the job.
     * @throws ConnectionException
     */
    public function handle(): void
    {
        try {
            $access_token = $this->user->spotifyAccessToken;

            if (now() >= $access_token->expires_at) {
                $token = SpotifyService::refreshToken($access_token->refresh_token);
                $access_token = $access_token->update([
                    'token' => $token['access_token'],
                    'expires_at' => now()->addMinutes($token['expires_in']),
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

            $spotify_playlist = SpotifyService::createPlaylist($this->playlist->name, $access_token->token, $spotify_user_id);

            $tracks = $this->playlist->tracks;
            $uri = [];
            foreach ($tracks as $track) {
                if($track->external_id) {
                    $uri[] = $track->external_id;
                }
            }


            foreach (array_chunk($uri, 100) as $chunk) {
                SpotifyService::addTracks($access_token->token, $spotify_playlist['id'], $chunk);
            }

            $this->playlist_action->update([
                'meta' => $spotify_playlist
            ]);
            $user = $this->user;
            Mail::to($user->email)->send(new PlaylistReadyMail($user, $this->playlist));
        } catch (\Exception $e) {
            Log::info("Playlist {$this->playlist->id} failed");
            Log::error($e->getMessage());
        }
    }
}
