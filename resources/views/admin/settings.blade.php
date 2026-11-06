@extends('layouts.admin')
@section('title', 'Paramètres')

@section('content')
<div class="topbar"><div><h1>Paramètres</h1><p>Coordonnées, livraison, paiement et sécurité du compte.</p></div></div>

<div class="grid g-main" style="align-items:start">
    <form class="grid" method="post" action="{{ route('admin.settings.update') }}">
        @csrf @method('put')
        <div class="card"><div class="card-h"><h2>Boutique</h2></div><div class="card-b form">
            <div class="row-2">
                <div class="field"><label for="shop_phone">Téléphone</label><input id="shop_phone" name="shop_phone" value="{{ old('shop_phone', $s['shop_phone']) }}" placeholder="+224 6xx xx xx xx"></div>
                <div class="field"><label for="shop_whatsapp">WhatsApp</label><input id="shop_whatsapp" name="shop_whatsapp" value="{{ old('shop_whatsapp', $s['shop_whatsapp']) }}" placeholder="+224 6xx xx xx xx"><span class="hint">Active le bouton WhatsApp sur tout le site.</span></div>
            </div>
            <div class="row-2">
                <div class="field"><label for="shop_email">E-mail</label><input id="shop_email" name="shop_email" value="{{ old('shop_email', $s['shop_email']) }}"></div>
                <div class="field"><label for="shop_hours">Horaires</label><input id="shop_hours" name="shop_hours" value="{{ old('shop_hours', $s['shop_hours']) }}"></div>
            </div>
            <div class="field"><label for="shop_address">Adresse</label><input id="shop_address" name="shop_address" value="{{ old('shop_address', $s['shop_address']) }}"></div>
            <div class="field"><label for="announcement">Bandeau d'annonce (haut du site)</label><input id="announcement" name="announcement" value="{{ old('announcement', $s['announcement']) }}"><span class="hint">Laissez vide pour le masquer.</span></div>
        </div></div>
        <div class="card"><div class="card-h"><h2>Livraison & paiement</h2></div><div class="card-b form">
            <div class="row-3">
                <div class="field"><label for="delivery_fee_conakry">Frais Conakry (GNF)</label><input id="delivery_fee_conakry" name="delivery_fee_conakry" value="{{ old('delivery_fee_conakry', $s['delivery_fee_conakry']) }}" inputmode="numeric"></div>
                <div class="field"><label for="delivery_fee_other">Frais régions (GNF)</label><input id="delivery_fee_other" name="delivery_fee_other" value="{{ old('delivery_fee_other', $s['delivery_fee_other']) }}" inputmode="numeric"></div>
                <div class="field"><label for="free_delivery_from">Livraison offerte dès</label><input id="free_delivery_from" name="free_delivery_from" value="{{ old('free_delivery_from', $s['free_delivery_from']) }}" inputmode="numeric"><span class="hint">0 = jamais</span></div>
            </div>
            <label class="switch"><input type="checkbox" name="cash_on_delivery" value="1" @checked($s['cash_on_delivery'] === '1')><span><b>Accepter le paiement à la livraison</b><small>En plus d'Orange Money</small></span></label>
        </div></div>
        <div class="card"><div class="card-h"><h2>Réseaux sociaux</h2></div><div class="card-b form">
            <div class="row-3">
                <div class="field"><label for="instagram">Instagram</label><input id="instagram" name="instagram" value="{{ old('instagram', $s['instagram']) }}" placeholder="https://instagram.com/…"></div>
                <div class="field"><label for="facebook">Facebook</label><input id="facebook" name="facebook" value="{{ old('facebook', $s['facebook']) }}" placeholder="https://facebook.com/…"></div>
                <div class="field"><label for="tiktok">TikTok</label><input id="tiktok" name="tiktok" value="{{ old('tiktok', $s['tiktok']) }}" placeholder="https://tiktok.com/@…"></div>
            </div>
            <button class="btn btn-accent" style="justify-self:start">Enregistrer les paramètres</button>
        </div></div>
    </form>

    <div class="grid">
        <div class="card">
            <div class="card-h"><h2>Orange Money</h2><span class="om-tag">OM</span></div>
            <div class="card-b" style="display:flex;flex-direction:column;gap:10px">
                @if ($omConfigured)
                    <span class="pill p-on" style="width:fit-content">Configuré · mode {{ $omMode === 'live' ? 'production' : 'test (sandbox)' }}</span>
                    <p class="muted" style="margin:0;font-size:.9rem">Les clients sont redirigés vers la page de paiement Orange Money. Le paiement est confirmé automatiquement.</p>
                @else
                    <span class="pill p-pending" style="width:fit-content">Non configuré</span>
                    <p class="muted" style="margin:0;font-size:.9rem">Renseignez vos identifiants marchand Orange Money dans le fichier <code>.env</code> du serveur (ORANGE_MONEY_*). En attendant, les commandes Orange Money sont enregistrées en « paiement en attente » et vous pouvez encaisser manuellement.</p>
                @endif
                <p class="muted" style="margin:0;font-size:.82rem">Adresse de notification à déclarer chez Orange :<br><code style="overflow-wrap:anywhere">{{ route('payment.om.notify') }}</code></p>
            </div>
        </div>
        <form class="card" method="post" action="{{ route('admin.password.update') }}">
            @csrf @method('put')
            <div class="card-h"><h2>Mot de passe</h2></div>
            <div class="card-b form">
                <div class="field"><label for="current_password">Mot de passe actuel</label><input id="current_password" type="password" name="current_password" autocomplete="current-password"></div>
                <div class="field"><label for="password">Nouveau mot de passe</label><input id="password" type="password" name="password" autocomplete="new-password"><span class="hint">8 caractères minimum</span></div>
                <div class="field"><label for="password_confirmation">Confirmer</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"></div>
                <button class="btn">Changer le mot de passe</button>
            </div>
        </form>
    </div>
</div>
@endsection
