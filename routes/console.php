<?php

use App\Services\Platforms\SpotifyService;
use App\Services\Scraper;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Panther\PantherTestCase;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command("scrape", function () {
    $user = \App\Models\User::query()->first();
    $access_token = $user->spotifyAccessToken->token;
    $track = \App\Models\PlaylistTrack::query()->findOrFail(1947);
    \App\Jobs\FindTrack::dispatch($access_token, $track);
});
