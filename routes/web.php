<?php

use App\Jobs\CreatePlaylist;
use App\Models\User;
use App\Services\Platforms\SpotifyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;

Route::get('/', function () {
    if(!session('code'))
        return redirect()->route('spotify_auth');
    $platforms = [
        [
            'name'  => 'Apple Music',
            'value' => 'apple'
        ],
        [
            'name'  => 'Spotify',
            'value' => 'spotify'
        ],
        [
            'name'  => 'Youtube Music',
            'value' => 'youtube'
        ]
    ];
    return Inertia::render('Home', [
        'platforms' => $platforms
    ]);
})->name('home')->middleware('auth');

Route::post('convert', function (Request $request) {
    $request->validate([
        'playlist_name' => ['required', 'string', 'max:255'],
        'playlist_link' => ['required', 'url', 'active_url'],
        'platform' => ['required', 'string', 'in:apple,spotify,youtube'],
    ]);
    $code = session('code');
    session()->forget('code');
   CreatePlaylist::dispatch($request->input('playlist_name'), $request->input('playlist_link'), $request->input('platform'), $code, Auth::user());
})->name('convert');

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

    return redirect()->route('home');
});

Route::post('/auth/login', function (Request $request) {
    $user = Auth::attempt($request->only('email', 'password'));
    return redirect()->intended();
});
