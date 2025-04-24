<?php

namespace App\Console\Commands;

use App\Enum\PlaylistStatus;
use App\Jobs\FindTrack;
use App\Models\Playlist;
use App\Models\PlaylistTrack;
use App\Services\Platforms\SpotifyService;
use Illuminate\Console\Command;

class ShowMissingTrack extends Command
{
    protected $signature = 'show:missing_track';
    protected $description = 'Show missing tracks in a playlist and try to find them on Spotify';

    public function handle()
    {
        $playlistId = $this->ask("🎧 What is the playlist ID?");
        $playlist = Playlist::query()->findOrFail($playlistId);

        if ($playlist->status !== PlaylistStatus::Complete) {
            return $this->error("⚠️ Playlist {$playlist->id} is not complete.");
        }

        $missingTracks = $playlist->tracks()->where('status', 'not_found')->get();

        if ($missingTracks->isEmpty()) {
            return $this->info("✅ All tracks have been found!");
        }

        $this->line("\n🎵 <fg=yellow>Missing Tracks:</>");
        $this->table(['ID', 'Name', 'Artist'], $missingTracks->map(fn ($track) => [
            $track->id,
            $track->name,
            $track->artist,
        ])->toArray());

        $trackId = $this->ask("🔍 What is the track ID you'd like to search for?");
        $track = PlaylistTrack::query()->findOrFail($trackId);
        $trackName = FindTrack::normalizeString($track->name);
        $trackArtist = FindTrack::normalizeString($track->artist);
        $user = $track->playlist->user;
        $accessToken = optional($user->spotifyAccessToken)->token;

        if (!$accessToken) {
            return $this->error("❌ No Spotify access token found for the playlist's user.");
        }

        $this->searchAndDisplayResults($accessToken, "{$trackName} {$trackArtist}", $trackName, $trackArtist, 'Spotify Search Result');
        $this->searchAndDisplayResults($accessToken, $trackName, $trackName, $trackArtist, 'Fallback Search Result');
    }

    protected function searchAndDisplayResults(string $token, string $query, string $trackName, string $trackArtist, string $header): void
    {
        $results = SpotifyService::search($token, rawurlencode($query), 'track');

        $this->line("\n🔍 <fg=green>{$header}:</>");
        foreach ($results as $item) {
            $itemName = FindTrack::normalizeString($item['name']);
            $itemArtists = array_map(fn ($a) => FindTrack::normalizeString($a['name']), $item['artists']);
            similar_text($trackName, $itemName, $percent);
            $artistMatch = in_array($trackArtist, $itemArtists);

            if ($artistMatch && $percent >= 50) {
                $included = str_contains($itemName, $trackName);
                $this->line("Included: " . ($included ? '✅' : '❌'));
                $this->line("Search Track : {$trackName}");
                $this->line("Found Track  : {$itemName}");
                $this->line("Match %      : {$percent}%");
                $this->line(str_repeat('-', 30));
            }
        }
    }
}
