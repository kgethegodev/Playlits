<?php

namespace App\Services\Platforms;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SpotifyService implements PlatfromInterface
{
    public string $auth_access_token = '';
    public string $user_id = '';

    public function __construct(public string $code = '')
    {
    }

    private function setAccessToken(): void
    {
        try {
            $request = Http::asForm()->post("https://accounts.spotify.com/api/token", [
                "grant_type" => "client_credentials",
                "client_id" => config('services.spotify.client_id'),
                "client_secret" => config('services.spotify.client_secret')
            ]);

            if (!$request->successful()) {
                throw new ConnectionException();
            }

            Cache::store()->put('spotify_access_token', $request->json()['access_token'], $request->json()['expires_in']);
        } catch (ConnectionException $e) {

        }
    }

    private function setAuthAccessToken(): void {
        try {
            $request = Http::asForm()
                ->withHeaders([
                    'Authorization' => "Basic ". base64_encode(config('services.spotify.client_id').':'.config('services.spotify.client_secret')),
                ])
                ->post("https://accounts.spotify.com/api/token", [
                    "grant_type"    => "authorization_code",
                    "code"          => $this->code,
                    "redirect_uri"  => config('services.spotify.redirect_url'),
                ]);


            if (!$request->successful()) {
                throw new ConnectionException();
            }
            $this->auth_access_token = $request->json()['access_token'];
        } catch (ConnectionException $e) {
        }
    }

    public static function getTracks(string $url): array
    {
        return  [];
    }

    public function getArtist()
    {
        if (!Cache::has('spotify_access_token')) {
            $this->setAccessToken();
        }

        $response = Http::withToken(Cache::get('spotify_access_token'))->get("https://api.spotify.com/v1/artists/4Z8W4fKeB5YxbusRsdQVPb");
        if(!$response->successful()) {
            throw new ConnectionException();
        }

        dd($response->json());
    }

    /**
     * @throws ConnectionException
     */
    private function me()
    {
        $response = Http::withToken($this->auth_access_token)->get("https://api.spotify.com/v1/me");
        $this->user_id = $response->json()['id'];
    }

    public function makePlaylist(string $name, array $tracks): void
    {
        $this->setAuthAccessToken();
        $this->me();
    }
}
