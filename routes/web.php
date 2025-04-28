<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlaylistController;
use App\Jobs\CreatePlaylist;
use App\Models\User;
use App\Services\Platforms\SpotifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home')->middleware('auth');

Route::get('/playlists', [PlaylistController::class, 'playlists'])->name('playlists')->middleware('auth');

Route::get('/playlists/{playlist}', [PlaylistController::class, 'playlist'])->name('playlist')->middleware('auth');

Route::post('convert', [PlaylistController::class, 'convert'])->name('convert');

Route::get('/spotify_auth', function (){
    $state = Str::random();
    $scope = 'user-read-private user-read-email playlist-read-private playlist-read-collaborative playlist-modify-private playlist-modify-public';
    $client_id = config('services.spotify.client_id');
    $redirect_uri = config('services.spotify.redirect_url');

    return redirect("https://accounts.spotify.com/authorize?response_type=code&client_id={$client_id}&scope={$scope}&redirect_uri={$redirect_uri}&state={$state}");
})->name('spotify_auth');

Route::get('/spotify_redirect', function (Request $request) {
    session()->put('code', $request->get('code'));

    return redirect()->route('home');
});


//Auth routes
Route::get('/auth/login', [AuthController::class, 'showLogin'])->name('login');

Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/auth/register', [AuthController::class, 'showRegister'])->name('register');

Route::post('/auth/register', [AuthController::class, 'register']);
