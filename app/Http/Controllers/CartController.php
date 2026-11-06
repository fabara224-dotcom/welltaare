<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function index()
    {
        $lines = $this->cart->lines();

        return view('shop.cart', [
            'lines' => $lines,
            'subtotal' => $this->cart->subtotal($lines),
        ]);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:20'],
            'size' => ['nullable', 'string', 'max:30'],
            'color' => ['nullable', 'string', 'max:50'],
        ]);

        $product = Product::active()->findOrFail($data['product_id']);

        if ($product->sizes && empty($data['size'])) {
            return back()->withErrors(['size' => 'Choisissez une taille.'])->withInput();
        }
        if ($product->isOutOfStock()) {
            return back()->withErrors(['size' => 'Cet article est épuisé.']);
        }

        $this->cart->add($product, (int) ($data['quantity'] ?? 1), $data['size'] ?? null, $data['color'] ?? null);

        if ($request->input('buy_now')) {
            return redirect()->route('checkout');
        }

        return back()->with('cart_added', $product->name);
    }

    public function update(Request $request)
    {
        $data = $request->validate(['key' => 'required|string', 'quantity' => 'required|integer|min:0|max:20']);
        $this->cart->update($data['key'], (int) $data['quantity']);

        return redirect()->route('cart');
    }

    public function remove(Request $request)
    {
        $this->cart->remove((string) $request->input('key'));

        return redirect()->route('cart')->with('status', 'Article retiré du panier.');
    }
}
