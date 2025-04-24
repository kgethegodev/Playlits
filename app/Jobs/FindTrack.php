<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use App\Models\PlaylistTrack;
use App\Services\Platforms\SpotifyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FindTrack implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $access_token, public PlaylistTrack $track)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $track_name = $this->normalizeString($this->track->name);
        $track_artist = $this->normalizeString($this->track->artist);

        $query = rawurlencode("{$track_name} {$track_artist}");
        $access_token = $this->access_token;

        $result = SpotifyService::search($access_token, $query, 'track');

        if ($this->matchAndAssignUri($this->track, $result, $track_name, $track_artist)) {
            return;
        }
        // Fallback: Try using only track name
        $fallback_query = rawurlencode($track_name);
        $fallback_result = SpotifyService::search($access_token, $fallback_query, 'track');

        $this->matchAndAssignUri($this->track, $fallback_result, $track_name, $track_artist);
    }

    public static function normalizeString(string $str): string
    {
        $str = strtolower($str);
        $str = preg_replace('/\s*\([^)]*\)/', '', $str); // remove (anything)
        $str = preg_replace('/\s*\[[^\]]*\]/', '', $str); // remove [anything]
        $str = preg_replace('/\s*\{[^}]*\}/', '', $str); // remove {anything}
        $str = preg_replace('/feat\..*/i', '', $str); // remove feat.
        return trim($str);
    }

    private function matchAndAssignUri(PlaylistTrack $track, array $result, string $track_name, string $track_artist): bool
    {
        foreach ($result as $item) {
            if ($item['type'] !== 'track') continue;

            $item_name = $this->normalizeString($item['name']);
            $item_artists = array_map(fn($a) => $this->normalizeString($a['name']), $item['artists']);

            similar_text($track_name, $item_name, $percent);
            $artist_match = in_array($track_artist, $item_artists);

            if ($artist_match && $percent > 70) {
                $track->update([
                    'external_id'   => $item['uri'],
                    'status'        => 'found',
                    'meta'          => $item
                ]);
                Log::info("✅ Matched '{$track['name']}' by '{$track['artist']}' to '{$item['name']}'", [
                    'uri' => $item['uri'],
                    'match_percent' => $percent
                ]);

                return true;
            }
        }

        $track->update([
            'status' => 'not_found'
        ]);

        return false;
    }
}
