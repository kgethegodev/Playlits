<?php

namespace App\Http\Controllers;

use App\Enum\PlaylistActionType;
use App\Jobs\AddPlaylist;
use App\Models\Playlist;
use App\Models\PlaylistAction;
use App\Models\Tag;
use App\Rules\PlaylistLinkValidation;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Inertia\Inertia;
use Inertia\Response;

class PlaylistController extends Controller
{
    public function create(): Response
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

        return Inertia::render('Playlist/Create', [
            'platforms' => $platforms,
            'playlists' => $user->playlists()->get(),
            'tags'      => $tags
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
        return Inertia::render('Playlist/Index', [
            'playlist' => $playlist->load(['tracks']),
        ]);
    }

    /**
     * Apply action to playlist
     *
     * @param Playlist $playlist
     * @param Request $request
     * @return RedirectResponse
     */
    public function makeAction(Playlist $playlist, Request $request): RedirectResponse
    {
        $request->validate([
            'type' => ['required', new Enum(PlaylistActionType::class)],
            'meta.message' => ['required_if:type,comment', 'string'],
        ]);

        $action = $playlist->actions()->where(['type' => 'like', 'user_id' => Auth::id()]);

        if($request->input('type') === 'like' && $action->exists()) {
            $action->delete();
        } else {
            $playlist->actions()->create([
                'type'  => $request->input('type'),
                'meta'  => $request->input('meta') ?? null,
                'user_id' => Auth::id(),
            ]);
        }

        return redirect()->back();
    }
}
