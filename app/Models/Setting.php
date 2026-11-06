<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['key', 'value'];

    public const DEFAULTS = [
        'shop_phone' => '',
        'shop_whatsapp' => '+221 77 127 25 67',
        'shop_email' => '',
        'shop_address' => 'Conakry, Guinée',
        'shop_hours' => 'Lun – Sam, 9h – 20h',
        'delivery_fee_conakry' => '30000',
        'delivery_fee_other' => '60000',
        'free_delivery_from' => '1000000',
        'cash_on_delivery' => '1',
        'instagram' => '',
        'facebook' => '',
        'tiktok' => '',
        'announcement' => 'Livraison partout à Conakry en 24 h · Paiement sécurisé Orange Money',
    ];

    public static function allValues(): array
    {
        return Cache::rememberForever('settings.all', function () {
            try {
                return array_merge(self::DEFAULTS, static::query()->pluck('value', 'key')->all());
            } catch (\Throwable) {
                return self::DEFAULTS;
            }
        });
    }

    public static function get(string $key, $default = null)
    {
        return self::allValues()[$key] ?? $default;
    }

    public static function put(array $values): void
    {
        foreach ($values as $k => $v) {
            static::updateOrCreate(['key' => $k], ['value' => $v]);
        }
        Cache::forget('settings.all');
    }
}
