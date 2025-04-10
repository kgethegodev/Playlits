<?php

namespace App\Jobs;

use App\Models\Playlist;
use App\Models\User;
use App\Services\Platforms\SpotifyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class UpdateTracks implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $user, public Playlist $playlist)
    {
        //
    }

    public function handle(): void
    {
        $tracks = $this->playlist->tracks;
        $count = 0;

        foreach ($tracks as &$track) {
            $trackName = $this->normalizeString($track['name']);
            $trackArtist = $this->normalizeString($track['artist']);

            $query = rawurlencode("{$trackName} {$trackArtist}");
            $accessToken = $this->user->spotifyAccessToken->token;

            $result = SpotifyService::search($accessToken, $query, 'track');

            if ($this->matchAndAssignUri($track, $result, $trackName, $trackArtist)) {
                $count++;
                continue;
            }

            // Fallback: Try using only track name
            $fallbackQuery = rawurlencode($trackName);
            $fallbackResult = SpotifyService::search($accessToken, $fallbackQuery, 'track');

            if ($this->matchAndAssignUri($track, $fallbackResult, $trackName, $trackArtist)) {
                $count++;
                continue;
            }

            Log::info("No match for track", [
                'track' => $track['name'],
                'artist' => $track['artist']
            ]);
        }

        dump("Matched {$count} out of " . count($tracks));
    }

    private function normalizeString(string $str): string
    {
        $str = strtolower($str);
        $str = preg_replace('/\s*\([^)]*\)/', '', $str); // remove (anything)
        $str = preg_replace('/\s*\[[^\]]*\]/', '', $str); // remove [anything]
        $str = preg_replace('/\s*\{[^}]*\}/', '', $str); // remove {anything}
        $str = preg_replace('/feat\..*/i', '', $str); // remove feat.
        return trim($str);
    }

    private function matchAndAssignUri(array &$track, array $result, string $trackName, string $trackArtist): bool
    {
        foreach ($result['tracks']['items'] ?? [] as $item) {
            if ($item['type'] !== 'track') continue;

            $itemName = $this->normalizeString($item['name']);
            $itemArtists = array_map(fn($a) => $this->normalizeString($a['name']), $item['artists']);

            similar_text($trackName, $itemName, $percent);
            $artistMatch = in_array($trackArtist, $itemArtists);

            if ($artistMatch && $percent > 70) {
                $track['uri'] = $item['uri'];
                Log::info("✅ Matched '{$track['name']}' by '{$track['artist']}' to '{$item['name']}'", [
                    'uri' => $item['uri'],
                    'match_percent' => $percent
                ]);
                return true;
            }
        }

        return false;
    }
}
