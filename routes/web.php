<?php

use App\Http\Controllers\PlaylistController;
use App\Jobs\CreatePlaylist;
use App\Models\User;
use App\Services\Platforms\SpotifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;

Route::get('/', [PlaylistController::class, 'index'])->name('home')->middleware('auth');

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

Route::get('/auth/login', function () {
    return Inertia::render('Login');
})->name('login');

Route::post('/auth/login', function (Request $request) {
    $request->validate([
        'email' => ['required', 'string', 'email', 'max:255', 'exists:users'],
        'password' => ['required', 'string', 'min:8'],
    ]);

    $user = Auth::attempt($request->only('email', 'password'));
});

Route::get('/auth/register', function () {
    return Inertia::render('Register');
})->name('register');

Route::post('/auth/register', function (Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = User::query()->create($request->all());
    Auth::login($user);
});
