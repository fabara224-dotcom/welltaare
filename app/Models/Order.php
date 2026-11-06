<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    public const STATUSES = [
        'nouvelle' => 'Nouvelle',
        'confirmee' => 'Confirmée',
        'preparation' => 'En préparation',
        'expediee' => 'En livraison',
        'livree' => 'Livrée',
        'annulee' => 'Annulée',
    ];

    public const PAYMENT_METHODS = [
        'orange_money' => 'Orange Money',
        'cash' => 'Paiement à la livraison',
    ];

    public const PAYMENT_STATUSES = [
        'pending' => 'En attente',
        'paid' => 'Payé',
        'failed' => 'Échoué',
        'cancelled' => 'Annulé',
    ];

    protected $fillable = [
        'user_id', 'reference', 'customer_name', 'customer_phone', 'customer_email', 'city', 'district', 'address', 'note',
        'payment_method', 'payment_status', 'status', 'subtotal', 'delivery_fee', 'total',
        'om_pay_token', 'om_notif_token', 'om_txnid', 'paid_at', 'admin_note',
    ];

    protected $hidden = ['om_pay_token', 'om_notif_token'];

    protected function casts(): array
    {
        return ['paid_at' => 'datetime'];
    }

    public static function newReference(string $prefix = 'WT'): string
    {
        do {
            $ref = $prefix.'-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (static::where('reference', $ref)->exists());

        return $ref;
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function paymentLabel(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? $this->payment_method;
    }

    public function paymentStatusLabel(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? $this->payment_status;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function needsOnlinePayment(): bool
    {
        return $this->payment_method === 'orange_money' && ! $this->isPaid() && $this->status !== 'annulee';
    }
}
