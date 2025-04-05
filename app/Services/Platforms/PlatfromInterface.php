<?php

namespace App\Services\Platforms;

interface PlatfromInterface
{
    public static function getTracks(string $url): array;
    public function makePlaylist(string $name, array $tracks);
}
