<?php

namespace App\Services\Platforms;

use App\Services\Platforms\PlatfromInterface;
use App\Services\Scraper;

class YoutubeMusicService implements PlatfromInterface
{

    /**
     * @throws \Exception
     */
    public static function getTracks(string $url): array
    {
        try {
            $data = Scraper::scrape($url, 'ytmusic-responsive-list-item-renderer', 'ytmusic-app');
            $tracks = [];
            foreach ($data as $item) {
                $track = explode("\n", trim($item));
                $tracks[] = [
                    'name' => $track[0],
                    'artist' => $track[1],
                ];
            }

        } catch (\Exception $e) {
            throw (new \Exception($e->getMessage()));
        }

        return $tracks;
    }

    public function makePlaylist(string $name, array $tracks)
    {
        // TODO: Implement makePlaylist() method.
    }
}
