<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Intégration Orange Money Web Payment (WebPay).
 *
 * Flux :
 *  1. getToken()            → jeton OAuth (client_credentials)
 *  2. createPayment($order) → URL de paiement Orange où le client valide avec son code
 *  3. Orange appelle notif_url (serveur à serveur) puis renvoie le client sur return_url
 *  4. checkStatus($order)   → on confirme toujours auprès d'Orange avant de marquer "payé"
 *
 * Les URL et identifiants sont dans config/services.php (variables ORANGE_MONEY_* du .env).
 */
class OrangeMoney
{
    public function __construct(private array $config = [])
    {
        $this->config = $config ?: config('services.orange_money');
    }

    public function isConfigured(): bool
    {
        return (bool) ($this->config['enabled'] ?? false)
            && ! empty($this->config['authorization_header'])
            && ! empty($this->config['merchant_key'])
            && ! str_contains((string) $this->config['merchant_key'], 'VOTRE_');
    }

    public function getToken(): string
    {
        return Cache::remember('orange_money.token', now()->addMinutes(50), function () {
            $res = Http::asForm()
                ->withHeaders(['Authorization' => $this->config['authorization_header'], 'Accept' => 'application/json'])
                ->timeout(20)
                ->post($this->config['token_url'], ['grant_type' => 'client_credentials']);

            if (! $res->successful() || ! $res->json('access_token')) {
                Log::error('Orange Money token error', ['status' => $res->status(), 'body' => $res->body()]);
                throw new RuntimeException('Impossible d\'obtenir le jeton Orange Money.');
            }

            return $res->json('access_token');
        });
    }

    /** Crée le paiement et renvoie l'URL de la page Orange Money */
    public function createPayment(Order $order): string
    {
        $payload = [
            'merchant_key' => $this->config['merchant_key'],
            'currency' => $this->config['currency'],
            'order_id' => $order->reference,
            'amount' => (int) $order->total,
            'return_url' => route('payment.om.return', $order),
            'cancel_url' => route('payment.om.cancel', $order),
            'notif_url' => route('payment.om.notify'),
            'lang' => $this->config['lang'] ?? 'fr',
            'reference' => $this->config['reference'] ?? 'WELLTAARE',
        ];

        $res = Http::withToken($this->getToken())
            ->acceptJson()
            ->timeout(25)
            ->post(rtrim($this->config['base_url'], '/').'/webpayment', $payload);

        if ($res->status() === 401) {
            Cache::forget('orange_money.token');
        }

        if (! $res->successful() || ! $res->json('payment_url')) {
            Log::error('Orange Money webpayment error', ['order' => $order->reference, 'status' => $res->status(), 'body' => $res->body()]);
            throw new RuntimeException('Le paiement Orange Money n\'a pas pu être initié.');
        }

        $order->update([
            'om_pay_token' => $res->json('pay_token'),
            'om_notif_token' => $res->json('notif_token'),
        ]);

        return $res->json('payment_url');
    }

    /**
     * Interroge Orange pour connaître le statut réel.
     * Renvoie INITIATED | PENDING | SUCCESS | FAILED | EXPIRED (ou null si erreur).
     */
    public function checkStatus(Order $order): ?string
    {
        if (! $order->om_pay_token) {
            return null;
        }
        try {
            $res = Http::withToken($this->getToken())
                ->acceptJson()
                ->timeout(20)
                ->post(rtrim($this->config['base_url'], '/').'/transactionstatus', [
                    'order_id' => $order->reference,
                    'amount' => (int) $order->total,
                    'pay_token' => $order->om_pay_token,
                ]);
        } catch (\Throwable $e) {
            Log::warning('Orange Money status error', ['order' => $order->reference, 'error' => $e->getMessage()]);

            return null;
        }

        if (! $res->successful()) {
            return null;
        }
        if ($txn = $res->json('txnid')) {
            $order->om_txnid = $txn;
        }

        return strtoupper((string) $res->json('status'));
    }

    /** Applique un statut Orange à la commande */
    public function apply(Order $order, ?string $status): void
    {
        if ($order->isPaid() || ! $status) {
            return;
        }
        if ($status === 'SUCCESS') {
            $order->forceFill(['payment_status' => 'paid', 'paid_at' => now()]);
            if ($order->status === 'nouvelle') {
                $order->status = 'confirmee';
            }
        } elseif (in_array($status, ['FAILED', 'EXPIRED'], true)) {
            $order->payment_status = 'failed';
        }
        $order->save();
    }
}
