<?php

namespace App\Http\Middleware;

use App\Services\Platforms\SpotifyService;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class VerifySpotifyAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $access_token = $user->spotifyAccessToken ?? null;
            if (!$access_token) {

                if (!in_array($request->route()->getName(), ['spotify_redirect', 'login', 'register'])) {
                    $state = Str::random();
                    $scope = 'user-read-private user-read-email playlist-read-private playlist-read-collaborative playlist-modify-private playlist-modify-public';
                    $client_id = config('services.spotify.client_id');
                    $redirect_uri = config('services.spotify.redirect_url');

                    return redirect("https://accounts.spotify.com/authorize?response_type=code&client_id={$client_id}&scope={$scope}&redirect_uri={$redirect_uri}&state={$state}");
                }
            } else {
                if(Carbon::parse($access_token->expires_at) < Carbon::now()) {
                    $token = SpotifyService::refreshToken($access_token->refresh_token);
                    $access_token->update([
                        'token'  => $token['access_token'],
                        'expires_at'    => now()->addMinutes($token['expires_in']),
                    ]);
                }
            }
        }
        return $next($request);
    }
}
