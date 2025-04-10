<?php

namespace App\Services\Platforms;

use App\Services\Scraper;
use Exception;

class AppleMusicService implements PlatfromInterface
{

    /**
     * @throws Exception
     */
    public static function getTracks(string $url): array
    {
        try {
            $data = Scraper::scrape($url, '.songs-list-row', '.songs-list');
            $tracks = [];
            foreach ($data as $item) {
                $track = explode("\n", trim($item));
                $tracks[] = [
                    'name' => $track[0],
                    'artist' => $track[1],
                    'duration' => $track[2],
                ];
            }
        } catch (Exception $e) {
            throw (new Exception($e->getMessage()));
        }

        return $tracks;
    }


    public function makePlaylist(string $name, array $tracks)
    {
        // TODO: Implement makePlaylist() method.
    }
}
