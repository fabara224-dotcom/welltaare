<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\OrangeMoney;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingController extends Controller
{
    public function edit(OrangeMoney $om)
    {
        return view('admin.settings', [
            's' => Setting::allValues(),
            'omConfigured' => $om->isConfigured(),
            'omMode' => config('services.orange_money.mode'),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'shop_phone' => 'nullable|string|max:40',
            'shop_whatsapp' => 'nullable|string|max:40',
            'shop_email' => 'nullable|email|max:120',
            'shop_address' => 'nullable|string|max:200',
            'shop_hours' => 'nullable|string|max:120',
            'delivery_fee_conakry' => 'required|integer|min:0',
            'delivery_fee_other' => 'required|integer|min:0',
            'free_delivery_from' => 'nullable|integer|min:0',
            'instagram' => 'nullable|url|max:200',
            'facebook' => 'nullable|url|max:200',
            'tiktok' => 'nullable|url|max:200',
            'announcement' => 'nullable|string|max:200',
        ]);
        $data['cash_on_delivery'] = $request->boolean('cash_on_delivery') ? '1' : '0';
        $data['free_delivery_from'] = (string) ($data['free_delivery_from'] ?? 0);
        Setting::put($data);

        return back()->with('status', 'Paramètres enregistrés.');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], ['current_password.current_password' => 'Mot de passe actuel incorrect.']);

        $request->user()->update(['password' => Hash::make($request->input('password'))]);

        return back()->with('status', 'Mot de passe modifié.');
    }
}
