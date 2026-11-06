<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrangeMoney;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private OrangeMoney $om) {}

    private function ownsOrder(Order $order): bool
    {
        return in_array($order->reference, session('my_orders', []), true)
            || ($order->user_id && $order->user_id === auth()->id());
    }

    /** Redirige le client vers la page de paiement Orange Money */
    public function pay(Order $order)
    {
        abort_unless($this->ownsOrder($order), 404);

        if (! $order->needsOnlinePayment()) {
            return redirect()->route('checkout.thanks', $order);
        }

        if (! $this->om->isConfigured()) {
            return redirect()->route('checkout.thanks', $order)->with('open_whatsapp', true)
                ->with('payment_error', 'Le paiement Orange Money en ligne n\'est pas encore activé. Notre équipe vous contacte pour finaliser le paiement.');
        }

        try {
            return redirect()->away($this->om->createPayment($order));
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('checkout.thanks', $order)->with('open_whatsapp', true)
                ->with('payment_error', 'Le paiement Orange Money n\'a pas pu démarrer. Réessayez dans un instant ou contactez-nous.');
        }
    }

    /** Retour du client après paiement : on vérifie auprès d'Orange */
    public function return(Order $order)
    {
        abort_unless($this->ownsOrder($order), 404);
        $this->om->apply($order, $this->om->checkStatus($order));
        session()->flash('open_whatsapp', true);

        return redirect()->route('checkout.thanks', $order->fresh());
    }

    public function cancel(Order $order)
    {
        abort_unless($this->ownsOrder($order), 404);

        return redirect()->route('checkout.thanks', $order)
            ->with('payment_error', 'Paiement annulé. Vous pouvez réessayer quand vous voulez.');
    }

    /** Notification serveur à serveur envoyée par Orange Money */
    public function notify(Request $request)
    {
        Log::info('Orange Money notification', $request->only('status', 'txnid'));

        $token = (string) $request->input('notif_token');
        $order = $token ? Order::where('om_notif_token', $token)->first() : null;
        if (! $order) {
            return response()->json(['ok' => false], 404);
        }

        if ($txn = $request->input('txnid')) {
            $order->om_txnid = $txn;
        }

        // On ne fait pas confiance au contenu reçu : on revérifie le statut auprès d'Orange.
        $status = $this->om->checkStatus($order);
        if ($status === null) {
            $order->admin_note = trim($order->admin_note."\nNotification Orange « ".$request->input('status')." » reçue le ".now()->format('d/m/Y H:i').' : à vérifier.');
            $order->save();

            return response()->json(['ok' => true]);
        }
        $this->om->apply($order, $status);

        return response()->json(['ok' => true]);
    }
}
