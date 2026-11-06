<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\Cart;
use App\Services\OrangeMoney;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function show(OrangeMoney $om)
    {
        $lines = $this->cart->lines();
        if ($lines->isEmpty()) {
            return redirect()->route('cart');
        }
        $subtotal = $this->cart->subtotal($lines);

        return view('shop.checkout', [
            'lines' => $lines,
            'subtotal' => $subtotal,
            'feeConakry' => Cart::deliveryFee($subtotal, 'Conakry'),
            'feeOther' => Cart::deliveryFee($subtotal, 'Autre'),
            'omAvailable' => (bool) config('services.orange_money.enabled'),
            'cashAvailable' => setting('cash_on_delivery') === '1',
            'user' => auth()->user(),
        ]);
    }

    public function store(Request $request)
    {
        $methods = array_keys(array_filter([
            'orange_money' => (bool) config('services.orange_money.enabled'),
            'cash' => setting('cash_on_delivery') === '1',
        ]));

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'regex:/^[+0-9 ().-]{8,20}$/'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'city' => ['required', 'string', 'max:80'],
            'district' => ['required', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:'.implode(',', $methods)],
        ], [
            'customer_phone.regex' => 'Numéro de téléphone invalide.',
            'payment_method.in' => 'Choisissez un mode de paiement.',
        ]);

        $lines = $this->cart->lines();
        if ($lines->isEmpty()) {
            return redirect()->route('cart')->withErrors(['cart' => 'Votre panier est vide.']);
        }

        foreach ($lines as $l) {
            if ($l->product->stock !== null && $l->product->stock < $l->quantity) {
                return redirect()->route('cart')->withErrors(['cart' => "Stock insuffisant pour « {$l->product->name} » (reste {$l->product->stock})."]);
            }
        }

        $subtotal = $this->cart->subtotal($lines);
        $fee = Cart::deliveryFee($subtotal, $data['city']);

        $order = DB::transaction(function () use ($data, $lines, $subtotal, $fee) {
            $order = Order::create($data + [
                'user_id' => auth()->id(),
                'reference' => Order::newReference('WT'),
                'subtotal' => $subtotal,
                'delivery_fee' => $fee,
                'total' => $subtotal + $fee,
                'status' => 'nouvelle',
                'payment_status' => 'pending',
            ]);
            foreach ($lines as $l) {
                $order->items()->create([
                    'product_id' => $l->product->id,
                    'product_name' => $l->product->name,
                    'image_path' => $l->product->images->first()?->path,
                    'size' => $l->size,
                    'color' => $l->color,
                    'quantity' => $l->quantity,
                    'unit_price' => $l->unit_price,
                    'line_total' => $l->total,
                ]);
                if ($l->product->stock !== null) {
                    Product::whereKey($l->product->id)->decrement('stock', $l->quantity);
                }
            }

            return $order;
        });

        $this->cart->clear();
        session()->push('my_orders', $order->reference);
        session()->flash('open_whatsapp', true);

        // Le client enregistre ses coordonnées pour la prochaine commande
        if ($u = auth()->user()) {
            $u->fill(array_filter([
                'phone' => $u->phone ?: $data['customer_phone'],
                'city' => $data['city'], 'district' => $data['district'], 'address' => $data['address'] ?? $u->address,
            ]))->save();
        }

        if ($order->payment_method === 'orange_money') {
            return redirect()->route('payment.om.pay', $order);
        }

        return redirect()->route('checkout.thanks', $order);
    }

    public function thanks(Order $order)
    {
        abort_unless(in_array($order->reference, session('my_orders', []), true)
            || ($order->user_id && $order->user_id === auth()->id()), 404);

        return view('shop.thanks', ['order' => $order->load('items')]);
    }
}
