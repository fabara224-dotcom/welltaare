<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return view('account.index', [
            'user' => $user,
            'orders' => $user->orders()->with('items')->get(),
            'designs' => $user->designRequests()->get(),
            'graphics' => $user->graphicRequests()->get(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $user->update($request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'string', 'regex:/^[+0-9 ().-]{8,20}$/'],
            'city' => ['nullable', 'string', 'max:80'],
            'district' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
        ], ['phone.regex' => 'Numéro de téléphone invalide.']));

        return back()->with('status', 'Vos informations sont enregistrées.');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], ['current_password.current_password' => 'Mot de passe actuel incorrect.', 'password.confirmed' => 'Les deux mots de passe ne correspondent pas.']);

        $request->user()->update(['password' => Hash::make($request->input('password'))]);

        return back()->with('status', 'Mot de passe modifié.');
    }
}
