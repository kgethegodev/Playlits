<?php

namespace App\Jobs;

use App\Models\Playlist;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;

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
            $jobs[] = new FindTrack($user->spotifyAccessToken->token, $track);
        }

        $playlist = $this->playlist;

        Bus::batch($jobs)
            ->then(function () use ($playlist) {
                AddSongsToPlaylist::dispatch($playlist);
            })
            ->dispatch();

        $this->playlist->update([
            'status' => 'tracks_dispatched'
        ]);
    }
}
