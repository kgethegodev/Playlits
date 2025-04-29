<?php

namespace App\Http\Middleware;

use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $playlists = Playlist::query()
            ->where('status', 'complete')
            ->with(['tags', 'actions'])
            ->get();

        $playlists->map(function (Playlist $playlist) {
            $track = $playlist->tracks()->where('status', 'found')->first();
            if ($track && isset($track->meta['album']['images'][0]['url'])) {
                $playlist->cover = $track->meta['album']['images'][0]['url'];
            } else {
                $playlist->cover = null; // Optional: handle case when there's no cover
            }
            return $playlist;
        });
        return array_merge(parent::share($request), [
            'auth' => Auth::user(),
            'playlists' => $playlists
        ]);
    }
}
