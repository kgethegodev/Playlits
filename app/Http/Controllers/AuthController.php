<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    /**
     * Login page
     *
     * @return Response
     */
    public function showLogin(): Response
    {
        return Inertia::render('Login');
    }

    /**
     * Login
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if(Auth::attempt($request->only('email', 'password')))
            return redirect()->route('home');

        return redirect()->back()->withErrors(['message' => 'Failed to login.']);
    }

    /**
     * Register page
     *
     * @return Response
     */
    public function showRegister(): Response
    {
        return Inertia::render('Register');
    }

    /**
     * Register
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::query()->create($request->all());
        Auth::login($user);

        return redirect()->route('home');
    }
}
