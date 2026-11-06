<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Panier stocké en session. Les prix sont toujours relus en base :
 * le client ne peut pas modifier un prix.
 */
class Cart
{
    private const KEY = 'cart.items';

    public function raw(): array
    {
        return session(self::KEY, []);
    }

    public function add(Product $product, int $qty, ?string $size, ?string $color): void
    {
        $items = $this->raw();
        $key = $product->id.'|'.($size ?? '').'|'.($color ?? '');
        $items[$key] = [
            'product_id' => $product->id,
            'size' => $size,
            'color' => $color,
            'quantity' => min(20, ($items[$key]['quantity'] ?? 0) + max(1, $qty)),
        ];
        session([self::KEY => $items]);
    }

    public function update(string $key, int $qty): void
    {
        $items = $this->raw();
        if (isset($items[$key])) {
            if ($qty <= 0) {
                unset($items[$key]);
            } else {
                $items[$key]['quantity'] = min(20, $qty);
            }
            session([self::KEY => $items]);
        }
    }

    public function remove(string $key): void
    {
        $this->update($key, 0);
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }

    /** Lignes enrichies avec le produit actuel */
    public function lines(): Collection
    {
        $raw = $this->raw();
        if (! $raw) {
            return collect();
        }
        $products = Product::with('images')->active()->whereIn('id', collect($raw)->pluck('product_id'))->get()->keyBy('id');

        return collect($raw)->map(function ($item, $key) use ($products) {
            $p = $products->get($item['product_id']);
            if (! $p) {
                return null;
            }

            return (object) [
                'key' => $key,
                'product' => $p,
                'size' => $item['size'],
                'color' => $item['color'],
                'quantity' => $item['quantity'],
                'unit_price' => $p->price,
                'total' => $p->price * $item['quantity'],
            ];
        })->filter()->values();
    }

    public function count(): int
    {
        return (int) collect($this->raw())->sum('quantity');
    }

    public function subtotal(?Collection $lines = null): int
    {
        return (int) ($lines ?? $this->lines())->sum('total');
    }

    public static function deliveryFee(int $subtotal, string $city): int
    {
        $free = (int) setting('free_delivery_from', 0);
        if ($free > 0 && $subtotal >= $free) {
            return 0;
        }
        $isConakry = str_contains(mb_strtolower($city), 'conakry');

        return (int) setting($isConakry ? 'delivery_fee_conakry' : 'delivery_fee_other', 0);
    }
}
