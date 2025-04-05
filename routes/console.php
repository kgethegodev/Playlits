<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Panther\PantherTestCase;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command("scrape", function () {
//    $playlistUrl = "https://music.apple.com/za/playlist/for-the-soul/pl.u-aZb0N67uPVBP0k4";
//    dd(\App\Services\Platforms\AppleMusicService::getTracks($playlistUrl));
//    session(['code' => 'AQBT_PylBFyKiwUxvvyh1J_3zFepQ52BsUlMNJ6wKFx2PrJWnDHR6pqQ2ExfNskcB7tF9kYykpXXgeuVHkBlnT_F3Ld98ttPh-kAwZBP0s80q3X5Omqf9bSX-r1FylEm9x4DqENe9mT29Uw-J7fcH-fIE8Z-hYnWOWwFJgYkdXBI0RYJ1zov8NKUIJmfdneb8K48BiddjFmWD1URgfjf44cgj3NnFG-LhWq81II']);
// (new \App\Services\Platforms\SpotifyService())->makePlaylist();
session()->put('code', 'AQCZTWq4jgr70wimGPzDG9vBq0pUGgkxzNEX6nfnhIL4IuyKlGNO3paVjdCBwaSixJXWHzJfxyUflSz7CXjUZux6Hi3MOzR9JEhY0GHvvpUU5YsRVefYtvPHDFqQg5qUk6uC6IqNayyWxr4Weade3tyDN-wtEM82QyZg5suUuQPBitCUQmkixzlCp8JsUaLhyQ7mgUX67QmhAr5DQKJGi9eu54DJIHrJUe41K4I');
 (new \App\Services\Platforms\SpotifyService())->me();
 dd(session('spotify_auth_access_token'));
});
