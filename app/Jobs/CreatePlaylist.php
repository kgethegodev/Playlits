<?php

namespace App\Jobs;

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
    public function __construct(public string $name, public string $url, public string $platform, public string $auth_code)
    {
        //
    }

    /**
     * Execute the job.
     * @throws \Exception
     */
    public function handle(): void
    {
        $service = new SpotifyService($this->auth_code);

        $tracks = match ($this->platform) {
            'apple' => AppleMusicService::getTracks($this->url),
            'spotify' => $service->getTracks($this->url),
        };

        // use tracks to create playlist
        $service->makePlaylist($this->name, $tracks);
    }
}
