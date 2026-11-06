<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * Connexion unique du site : clients, administrateurs et développeurs utilisent le même formulaire.
 * Un compte administrateur est redirigé vers l'espace de gestion, un client vers « Mon compte ».
 */
class AccountAuthController extends Controller
{
    public function showLogin()
    {
        return view('account.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = 'login:'.Str::lower($credentials['email']).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['email' => 'Trop de tentatives. Réessayez dans '.RateLimiter::availableIn($key).' secondes.'])->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);

            return back()->withErrors(['email' => 'E-mail ou mot de passe incorrect.'])->onlyInput('email');
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();
        $user = $request->user();
        $user->forceFill(['last_login_at' => now()])->save();

        if ($user->is_admin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Un client ne doit jamais être renvoyé vers une adresse de gestion mémorisée
        $intended = session()->pull('url.intended');
        $adminPrefix = url(config('shop.admin_path'));
        if ($intended && ! Str::startsWith($intended, $adminPrefix)) {
            return redirect()->to($intended);
        }

        return redirect()->route('account')->with('status', 'Bienvenue, '.Str::of($user->name)->explode(' ')->first().' !');
    }

    public function showRegister()
    {
        return view('account.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['required', 'string', 'regex:/^[+0-9 ().-]{8,20}$/'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'email.unique' => 'Un compte existe déjà avec cet e-mail. Connectez-vous.',
            'phone.regex' => 'Numéro de téléphone invalide.',
            'password.confirmed' => 'Les deux mots de passe ne correspondent pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        $user = User::create($data + ['is_admin' => false, 'role' => 'customer']);
        Auth::login($user, true);
        $request->session()->regenerate();

        $intended = session()->pull('url.intended');

        return $intended && ! Str::startsWith($intended, url(config('shop.admin_path')))
            ? redirect()->to($intended)->with('status', 'Votre compte est créé.')
            : redirect()->route('account')->with('status', 'Votre compte est créé. Bienvenue !');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
