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
        $tracks = [];
        try {
            // Obtain client credentials access token
            if (!Cache::has('spotify_client_access_token')) {
                $tokenResponse = Http::asForm()->post('https://accounts.spotify.com/api/token', [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => config('services.spotify.client_id'),
                    'client_secret' => config('services.spotify.client_secret'),
                ]);
                if (!$tokenResponse->successful()) {
                    throw new ConnectionException('Failed to authenticate with Spotify API.');
                }
                Cache::put('spotify_client_access_token', $tokenResponse->json('access_token'), $tokenResponse->json('expires_in'));
            }
            $accessToken = Cache::get('spotify_client_access_token');

            // Parse playlist ID from URL
            $path = parse_url($url, PHP_URL_PATH);
            $segments = explode('/', trim($path, '/'));
            if (!isset($segments[1])) {
                throw new \InvalidArgumentException("Invalid Spotify playlist URL: {$url}");
            }
            $playlistId = $segments[1];

            // Fetch tracks in pages
            $next = "https://api.spotify.com/v1/playlists/{$playlistId}/tracks?limit=100";
            while ($next) {
                $response = Http::withToken($accessToken)->get($next);
                if (!$response->successful()) {
                    throw new ConnectionException('Failed to retrieve Spotify playlist tracks.');
                }
                $data = $response->json();
                foreach ($data['items'] ?? [] as $item) {
                    if (empty($item['track'])) {
                        continue;
                    }
                    $t = $item['track'];
                    $name = $t['name'] ?? '';
                    $artistName = isset($t['artists'][0]['name']) ? $t['artists'][0]['name'] : '';
                    $durationMs = $t['duration_ms'] ?? 0;
                    $totalSeconds = (int) ($durationMs / 1000);
                    $hours = floor($totalSeconds / 3600);
                    $minutes = floor(($totalSeconds % 3600) / 60);
                    $seconds = $totalSeconds % 60;
                    $duration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                    $tracks[] = [
                        'name'     => $name,
                        'artist'   => $artistName,
                        'duration' => $duration,
                    ];
                }
                $next = $data['next'];
            }
        } catch (\Exception $e) {
            // Log error and return what has been collected (possibly empty)
            info("SpotifyService::getTracks error for URL {$url}: {$e->getMessage()}");
        }
        return $tracks;
    }

    public static function addTracks(string $access_token, string $playlist_id, array $uri_array)
    {
        $data = null;
        try {
            $response = Http::withToken($access_token)->post("https://api.spotify.com/v1/playlists/{$playlist_id}/tracks", [
                'uris' => $uri_array,
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
        $data = [];
        try {
            $response = Http::withToken($access_token)->get("https://api.spotify.com/v1/search", [
                'q' => $query,
                'type' => $types,
                'limit' => 50
            ]);

            if (!$response->successful()) {
                info("We failed niggas shit");
                throw new ConnectionException();
            }

            $data = $response->json()['tracks']['items'];
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
    }
}
