<?php

namespace App\Services\Platforms;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SpotifyService implements PlatfromInterface
{
    public string $auth_access_token = '';
    public string $user_id = '';

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

    public static function createAuthAccessToken(string $auth_code) {
        $data = null;
        try {
            $request = Http::asForm()
                ->withHeaders([
                    'Authorization' => "Basic ". base64_encode(config('services.spotify.client_id').':'.config('services.spotify.client_secret')),
                ])
                ->post("https://accounts.spotify.com/api/token", [
                    "grant_type"    => "authorization_code",
                    "code"          => $auth_code,
                    "redirect_uri"  => config('services.spotify.redirect_url'),
                ]);


            if (!$request->successful()) {
                throw new ConnectionException();
            }

            $data = $request->json();
        } catch (ConnectionException $e) {
        }

        return $data;
    }

    public static function refreshToken(string $refresh_token) {
        $data = null;
        try {
            $request = Http::asForm()
                ->withHeaders([
                    'Authorization' => "Basic ". base64_encode(config('services.spotify.client_id').':'.config('services.spotify.client_secret')),
                ])
                ->post("https://accounts.spotify.com/api/token", [
                    "grant_type"    => "refresh_token",
                    "refresh_token" => $refresh_token,
                    "client_id"     => config('services.spotify.client_id'),
            ]);

            if (!$request->successful()) {
                throw new ConnectionException();
            }

            $data = $request->json();
        } catch (ConnectionException $e) {
        }

        return $data;
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
    public static function me(string $access_token)
    {
        $data = null;
        try {
            $response = Http::withToken($access_token)->get("https://api.spotify.com/v1/me");

            if (!$response->successful()) {
                throw new ConnectionException();
            }

            $data = $response->json();
        } catch (\Exception $e) {

        }

        return $data;
    }

    public static function createPlaylist(string $name, string $access_token, $user_id)
    {
        $data = null;
        try {
            $response = Http::withToken($access_token)->post("https://api.spotify.com/v1/users/" . $user_id . "/playlists", [
                'name' => $name,
                'description' => $name,
                'public' => true
            ]);

            if (!$response->successful()) {
                info($response->body());
                throw new ConnectionException();
            }

            $data = $response->json();
        }
        catch (\Exception $e) {
        }

        return $data;
    }

    public static function search(string $access_token, string $query, string $types)
    {
        $data = null;
        try {
            $response = Http::withToken($access_token)->get("https://api.spotify.com/v1/search", [
                'q' => $query,
                'type' => $types,
                'limit' => 50
            ]);

            if (!$response->successful()) {
                dd($response->body());
                throw new ConnectionException();
            }
            $data = $response->json();
        }
        catch (\Exception $e) {}

        return $data;
    }

    public function makePlaylist(string $name, array $tracks): void
    {
        $this->createAuthAccessToken();
        $this->me();

        // create playlist
//        $playlist = $this->createPlaylist($name);

        $add_count = 0;
        // loop through songs
        foreach ($tracks as $track) {
            // search for song on Spotify
            $query = 'track%3A' . $track['name'] . ' artist%3A' . $track['artist'];
            $result = $this->search($query, 'track');


            foreach ($result['tracks']['items'] as $item) {
                if ($item['type'] !== 'track') {
                    continue;
                }
                $same_artist = false;
                $similar_name = fnmatch($track['name'], $item['name']);
                foreach ($item['artists'] as $artist) {
                    if ($artist['name'] == $track['artist']) {
                        $same_artist = true;
                    }
                }
                if($same_artist && $similar_name) {
                    $add_count++;
                }
            }
            // add song to the playlist
        }

        info(count($tracks) . " - " . $add_count . " songs added");
        dd();
    }
}
