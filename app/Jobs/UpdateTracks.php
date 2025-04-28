<?php

namespace App\Jobs;

use App\Mail\PlaylistReadyMail;
use App\Models\Playlist;
use App\Services\Platforms\SpotifyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class UpdateTracks implements ShouldQueue
{
    use Queueable;

    public function __construct(public Playlist $playlist)
    {
        //
    }

    /**
     * @throws \Throwable
     */
    public function handle(): void
    {
        $user = $this->playlist->user;
        $tracks = $this->playlist->tracks;

        $jobs = [] ;
        foreach ($tracks as $track) {
            if (!Cache::has('spotify_access_token')) {
                $response = SpotifyService::refreshToken(config('services.spotify.refresh_token'));
                Cache::store()->put('spotify_access_token', $response['access_token'], now()->addMinutes($response['expires_in']));
            }
            $access_token = Cache::get('spotify_access_token');

            $jobs[] = new FindTrack($access_token, $track);
        }

        $playlist = $this->playlist;

        $this->playlist->update([
            'status' => 'tracks_dispatched'
        ]);

        Bus::batch($jobs)
            ->then(function () use ($playlist) {
                $playlist->update([
                    'status' => 'complete'
                ]);
                $user = $playlist->user;
                Mail::to($user->email)->send(new PlaylistReadyMail($user, $playlist));
            })
            ->dispatch();
    }
}
