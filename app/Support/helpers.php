<?php

if (! function_exists('gnf')) {
    /** Formate un montant en francs guinéens : 1 250 000 GNF */
    function gnf($amount): string
    {
        return number_format((float) $amount, 0, ',', ' ').' GNF';
    }
}

if (! function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        return \App\Models\Setting::get($key, $default);
    }
}

if (! function_exists('wa_link')) {
    /** Lien WhatsApp à partir d'un numéro guinéen (ajoute 224 si besoin) */
    function wa_link(?string $phone, string $text = ''): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);
        if (! $digits) {
            return null;
        }
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }
        if (strlen($digits) === 9) {
            $digits = '224'.$digits;
        }

        return 'https://wa.me/'.$digits.($text ? '?text='.rawurlencode($text) : '');
    }
}

if (! function_exists('site_image')) {
    /** URL d'une image du site définie dans Admin → Apparence (null si aucune) */
    function site_image(string $key): ?string
    {
        $path = \App\Models\Setting::get('img_'.$key);

        return $path ? \Illuminate\Support\Facades\Storage::disk('public')->url($path) : null;
    }
}

if (! function_exists('order_whatsapp_text')) {
    /** Récapitulatif envoyé sur WhatsApp à la boutique après une commande ou une demande */
    function order_whatsapp_text($record): string
    {
        $shop = config('shop.name');
        if ($record instanceof \App\Models\Order) {
            $lines = $record->items->map(fn ($i) => '• '.$i->quantity.' × '.$i->product_name
                .($i->size ? ' (taille '.$i->size.')' : '').($i->color ? ' – '.$i->color : '').' : '.gnf($i->line_total))->join("\n");

            return "Bonjour {$shop}, je viens de passer la commande *{$record->reference}* :\n{$lines}\n"
                .'Livraison : '.($record->delivery_fee ? gnf($record->delivery_fee) : 'offerte')."\n"
                .'*Total : '.gnf($record->total)."*\n"
                .'Paiement : '.$record->paymentLabel().' ('.$record->paymentStatusLabel().")\n"
                ."Nom : {$record->customer_name}\nTéléphone : {$record->customer_phone}\n"
                ."Adresse : {$record->district}, {$record->city}".($record->address ? " ({$record->address})" : '')
                .($record->note ? "\nMessage : {$record->note}" : '');
        }
        if ($record instanceof \App\Models\DesignRequest) {
            return "Bonjour {$shop}, voici ma demande de création sur mesure *{$record->reference}* :\n"
                ."{$record->garment_type} (".ucfirst($record->gender).')'.($record->occasion ? " pour {$record->occasion}" : '')."\n"
                .$record->description."\n"
                .($record->budget ? 'Budget : '.gnf($record->budget)."\n" : '')
                ."Nom : {$record->customer_name}\nTéléphone : {$record->customer_phone}";
        }
        if ($record instanceof \App\Models\GraphicRequest) {
            return "Bonjour {$shop}, voici ma demande de design graphique *{$record->reference}* :\n"
                .$record->serviceLabel()."\n".$record->description."\n"
                .($record->company ? "Entreprise : {$record->company}\n" : '')
                ."Nom : {$record->customer_name}\nTéléphone : {$record->customer_phone}";
        }

        return "Bonjour {$shop}";
    }
}
