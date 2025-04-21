<?php

namespace App\Http\Controllers;

use App\Jobs\CreatePlaylist;
use App\Models\Playlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PlaylistController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();

        $platforms = [
            [
                'name'  => 'apple music.',
                'value' => 'apple'
            ],
            [
                'name'  => 'youtube music.',
                'value' => 'youtube'
            ]
        ];

        return Inertia::render('Home', [
            'platforms' => $platforms,
            'playlists' => $user->playlists()->get(),
            'has_code'  => session('code') !== null
        ]);
    }

    public function convert(Request $request): RedirectResponse
    {
        $request->validate([
            'playlist_name' => ['required', 'string', 'max:255'],
            'playlist_link' => ['required', 'url', 'active_url'],
            'platform' => ['required', 'string', 'in:apple,spotify,youtube'],
        ]);
        $code = session('code');
        session()->forget('code');
        CreatePlaylist::dispatch($request->input('playlist_name'), $request->input('playlist_link'), $request->input('platform'), $code, Auth::user());

        return to_route('home');
    }

    public function playlists(): Response
    {
        $user = Auth::user();

        return Inertia::render('Playlists', [
            'playlists' => $user->playlists()->get(),
        ]);
    }

    public function playlist(Playlist $playlist): Response|RedirectResponse
    {
        $user = Auth::user();
        if ($playlist->user_id !== $user->id) {
            return back()->withErrors([
                'message' => 'You are not allowed to play this playlist',
            ]);
        }

        return Inertia::render('Playlist', [
            'playlists' => $user->playlists()->get(),
            'playlist' => $playlist->load(['tracks']),
        ]);
    }
}
