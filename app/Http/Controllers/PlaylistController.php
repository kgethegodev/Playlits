<?php

namespace App\Http\Controllers;

use App\Jobs\AddPlaylist;
use App\Jobs\CreatePlaylist;
use App\Models\Playlist;
use App\Models\Tag;
use App\Rules\PlaylistLinkValidation;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PlaylistController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();
        $tags = Tag::all()->groupBy('type');
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
            'tags'      => $tags,
            'has_code'  => session('code') !== null
        ]);
    }

    public function convert(Request $request): RedirectResponse
    {
        $request->validate([
            'playlist_name' => ['required', 'string', 'max:255'],
            'playlist_link' => ['required', 'url', 'active_url', new PlaylistLinkValidation],
            'mood' => ['required', Rule::exists('tags', 'name')->where(function (Builder $query) {
                $query->where('type', 'mood');
            })],
            'genre' => ['required', Rule::exists('tags', 'name')->where(function (Builder $query) {
                $query->where('type', 'genre');
            })],
            'activity' => ['required', Rule::exists('tags', 'name')->where(function (Builder $query) {
                $query->where('type', 'activity');
            })],
        ]);

        $host = parse_url($request->input('playlist_link'), PHP_URL_HOST);
        $platform = match (true) {
            str_contains($host, 'spotify') => 'spotify',
            str_contains($host, 'apple') => 'apple',
            str_contains($host, 'youtube') => 'youtube',
            default => 'unknown',
        };
        $columns = ['mood', 'genre', 'activity'];
        $tags = [];
        foreach ($columns as $column) {
            $tags[] = Tag::query()->where(['type' => $column, 'name' => $request->get($column)])->first()->id;
        }
        AddPlaylist::dispatch($request->input('playlist_name'), $request->input('playlist_link'), $platform, $tags, Auth::user());

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
