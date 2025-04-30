<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PlaylistLinkValidation implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (
            !$this->isSpotifyPlaylist($value) &&
            !$this->isAppleMusicPlaylist($value) &&
            !$this->isYouTubeMusicPlaylist($value)
        ) {
            $fail('The :attribute must be a valid Spotify, Apple Music, or YouTube Music playlist link.');
        }
    }

    private function isSpotifyPlaylist(string $url): bool
    {
        return (bool) preg_match('/^https:\/\/open\.spotify\.com\/playlist\/[a-zA-Z0-9]+(\?.*)?$/', $url);
    }

    private function isAppleMusicPlaylist(string $url): bool
    {
        return (bool) preg_match(
            '/^(https:\/\/music\.apple\.com\/[a-z]{2}\/playlist\/[^\/]+\/pl\.[a-zA-Z0-9_-]+|https:\/\/open\.spotify\.com\/playlist\/[a-zA-Z0-9]+)$/',
            $url
        );

    }

    private function isYouTubeMusicPlaylist(string $url): bool
    {
        return (bool) preg_match('/^https:\/\/music\.youtube\.com\/playlist\?list=[a-zA-Z0-9_\-]+(\&.*)?$/', $url);
    }
}
