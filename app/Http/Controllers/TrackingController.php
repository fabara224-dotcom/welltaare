<?php

namespace App\Http\Controllers;

use App\Models\DesignRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function form()
    {
        $mine = $this->mine();

        return view('shop.tracking', ['mine' => $mine]);
    }

    public function lookup(Request $request)
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:30'],
            'phone' => ['required', 'string', 'max:20'],
        ]);
        $ref = strtoupper(trim($data['reference']));
        $digits = fn ($p) => substr(preg_replace('/\D/', '', (string) $p), -8);

        $found = Order::with('items')->where('reference', $ref)->first()
            ?? DesignRequest::where('reference', $ref)->first()
            ?? \App\Models\GraphicRequest::where('reference', $ref)->first();

        if (! $found || $digits($found->customer_phone) !== $digits($data['phone'])) {
            return back()->withInput()->withErrors(['reference' => 'Aucune commande ne correspond à ce numéro et ce téléphone.']);
        }

        return view('shop.tracking', [
            'mine' => $this->mine(),
            'result' => $found,
        ]);
    }

    private function mine()
    {
        return Order::where(function ($q) {
            $q->whereIn('reference', session('my_orders', []));
            if (auth()->id()) {
                $q->orWhere('user_id', auth()->id());
            }
        })->latest()->take(20)->get();
    }
}
