<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaylistController;
use App\Models\Playlist;
use App\Services\Platforms\SpotifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;

Route::get('/', function () {
    $playlists = Playlist::query()->where('status', 'complete')->with(['actions'])->get();
    return Inertia::render('Home', [
        'banner'    => asset('images/pexels-theshuttervision-15447298.jpg'),
        'playlists' => $playlists,
    ]);
})->name('home');

// Playlists
Route::get('/playlist/add', [PlaylistController::class, 'create'])->name('playlists.add')->middleware('auth');

Route::get('/playlists', [PlaylistController::class, 'playlists'])->name('playlists')->middleware('auth');

Route::get('/playlists/{playlist}', [PlaylistController::class, 'playlist'])->name('playlist');
Route::post('/playlists/{playlist}/action', [PlaylistController::class, 'makeAction'])->name('playlist.action')->middleware('auth');

Route::post('convert', [PlaylistController::class, 'convert'])->name('convert');

Route::get('/spotify_auth', function (){
    $state = Str::random();
    $scope = 'user-read-private user-read-email playlist-read-private playlist-read-collaborative playlist-modify-private playlist-modify-public';
    $client_id = config('services.spotify.client_id');
    $redirect_uri = config('services.spotify.redirect_url');

    return redirect("https://accounts.spotify.com/authorize?response_type=code&client_id={$client_id}&scope={$scope}&redirect_uri={$redirect_uri}&state={$state}");
})->name('spotify_auth');

Route::get('/spotify_redirect', function (Request $request) {
    if (Auth::check()) {
        $user = Auth::user();
        $token = SpotifyService::createAuthAccessToken($request->get('code'));
        $user->spotifyAccessToken()->create([
            'token'  => $token['access_token'],
            'refresh_token' => $token['refresh_token'],
            'expires_at'    => now()->addMinutes($token['expires_in']),
        ]);
    } else {
        session()->put('code', $request->get('code'));
    }

    return redirect()->route('home');
})->name('spotify_redirect');


//Auth routes
Route::get('/auth/login', [AuthController::class, 'showLogin'])->name('login');

Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/auth/register', [AuthController::class, 'showRegister'])->name('register');

Route::post('/auth/register', [AuthController::class, 'register']);
