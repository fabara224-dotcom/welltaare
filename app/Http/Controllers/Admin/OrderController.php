<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrangeMoney;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $q = Order::withCount('items')->latest();
        if (array_key_exists($s = $request->query('statut'), Order::STATUSES)) {
            $q->where('status', $s);
        }
        if (array_key_exists($p = $request->query('paiement'), Order::PAYMENT_STATUSES)) {
            $q->where('payment_status', $p);
        }
        if ($search = trim((string) $request->query('q'))) {
            $q->where(fn ($w) => $w->where('reference', 'like', "%{$search}%")
                ->orWhere('customer_name', 'like', "%{$search}%")
                ->orWhere('customer_phone', 'like', "%{$search}%"));
        }

        return view('admin.orders.index', [
            'orders' => $q->paginate(25)->withQueryString(),
            'counts' => Order::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status'),
        ]);
    }

    public function show(Order $order)
    {
        return view('admin.orders.show', ['order' => $order->load('items.product')]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(Order::STATUSES))],
            'payment_status' => ['required', 'in:'.implode(',', array_keys(Order::PAYMENT_STATUSES))],
            'admin_note' => ['nullable', 'string', 'max:3000'],
        ]);

        DB::transaction(function () use ($order, $data) {
            // Remise en stock si la commande est annulée (et retrait si elle est réactivée)
            if ($order->status !== 'annulee' && $data['status'] === 'annulee') {
                foreach ($order->items as $i) {
                    Product::whereKey($i->product_id)->whereNotNull('stock')->increment('stock', $i->quantity);
                }
            } elseif ($order->status === 'annulee' && $data['status'] !== 'annulee') {
                foreach ($order->items as $i) {
                    Product::whereKey($i->product_id)->whereNotNull('stock')->decrement('stock', $i->quantity);
                }
            }
            if ($data['payment_status'] === 'paid' && ! $order->paid_at) {
                $data['paid_at'] = now();
            }
            $order->update($data);
        });

        return back()->with('status', 'Commande mise à jour.');
    }

    public function verifyPayment(Order $order, OrangeMoney $om)
    {
        if (! $om->isConfigured()) {
            return back()->withErrors(['om' => 'Orange Money n\'est pas configuré (voir le fichier .env).']);
        }
        $status = $om->checkStatus($order);
        $om->apply($order, $status);

        return back()->with('status', $status ? "Statut Orange Money : {$status}" : 'Orange Money n\'a pas répondu. Réessayez plus tard.');
    }
}
