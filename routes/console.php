<?php

use App\Services\Platforms\SpotifyService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Panther\PantherTestCase;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command("scrape", function () {
    $user = \App\Models\User::query()->first();
    $access_token = $user->spotifyAccessToken;
    $token = SpotifyService::refreshToken($access_token->refresh_token);
    $access_token = $user->spotifyAccessToken()->update([
        'token'  => $token['access_token'],
        'expires_at'    => now()->addMinutes($token['expires_in']),
    ]);

    $playlist = \App\Models\Playlist::query()->first();

    \App\Jobs\UpdateTracks::dispatch($user, $playlist);
});
