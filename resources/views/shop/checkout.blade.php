@extends('layouts.shop')
@section('title', 'Livraison et paiement')

@section('content')
<section class="page-hero"><div class="container"><span class="eyebrow">Étape 2 sur 3</span><h1>Livraison & paiement</h1></div></section>

<div class="container">
    <form method="post" action="{{ route('checkout.store') }}" class="cart-layout" data-once novalidate>
        @csrf
        <div class="checkout-form">
            @if ($errors->any())<div class="alert alert-err">Vérifiez les champs en rouge.</div>@endif
            @guest
                <div class="alert alert-info">Déjà client ? <a class="link" href="{{ route('login') }}">Connectez-vous</a> pour retrouver vos coordonnées et suivre vos commandes. Vous pouvez aussi commander sans compte.</div>
            @endguest

            <fieldset>
                <legend>Vos coordonnées</legend>
                <div class="field @error('customer_name') has-error @enderror"><label for="customer_name">Nom complet *</label><input id="customer_name" name="customer_name" value="{{ old('customer_name', $user?->name) }}" autocomplete="name" required>@error('customer_name')<span class="error">{{ $message }}</span>@enderror</div>
                <div class="grid-2">
                    <div class="field @error('customer_phone') has-error @enderror"><label for="customer_phone">Téléphone *</label><input id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $user?->phone) }}" inputmode="tel" autocomplete="tel" placeholder="+224 6xx xx xx xx" required>@error('customer_phone')<span class="error">{{ $message }}</span>@enderror<span class="hint">Pour confirmer la commande et la livraison.</span></div>
                    <div class="field @error('customer_email') has-error @enderror"><label for="customer_email">E-mail (facultatif)</label><input id="customer_email" type="email" name="customer_email" value="{{ old('customer_email', $user?->email) }}" autocomplete="email">@error('customer_email')<span class="error">{{ $message }}</span>@enderror</div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Adresse de livraison</legend>
                <div class="grid-2">
                    <div class="field @error('city') has-error @enderror"><label for="city">Ville *</label><input id="city" name="city" value="{{ old('city', $user?->city ?: 'Conakry') }}" required list="cities" data-fee-conakry="{{ $feeConakry }}" data-fee-other="{{ $feeOther }}" data-subtotal="{{ $subtotal }}">
                        <datalist id="cities"><option>Conakry</option><option>Coyah</option><option>Dubréka</option><option>Kindia</option><option>Boké</option><option>Mamou</option><option>Labé</option><option>Kankan</option><option>N'Zérékoré</option><option>Kamsar</option></datalist>
                        @error('city')<span class="error">{{ $message }}</span>@enderror</div>
                    <div class="field @error('district') has-error @enderror"><label for="district">Quartier / commune *</label><input id="district" name="district" value="{{ old('district', $user?->district) }}" placeholder="Kaloum, Dixinn, Ratoma, Matam, Matoto…" required>@error('district')<span class="error">{{ $message }}</span>@enderror</div>
                </div>
                <div class="field"><label for="address">Point de repère</label><input id="address" name="address" value="{{ old('address', $user?->address) }}" placeholder="Près de la mosquée, du carrefour…"></div>
                <div class="field"><label for="note">Message pour la boutique</label><textarea id="note" name="note" style="min-height:80px" placeholder="Heure de livraison souhaitée, précision sur la taille…">{{ old('note') }}</textarea></div>
            </fieldset>

            <fieldset>
                <legend>Paiement</legend>
                @error('payment_method')<span class="error-text">{{ $message }}</span>@enderror
                <div class="pay-options">
                    @if ($omAvailable)
                    <label class="pay-option">
                        <input type="radio" name="payment_method" value="orange_money" @checked(old('payment_method', 'orange_money') === 'orange_money')>
                        <div><b>Orange Money</b><small>Vous êtes redirigé vers la page sécurisée Orange Money pour valider le paiement avec votre code.</small></div>
                        <span class="om-badge">Orange Money</span>
                    </label>
                    @endif
                    @if ($cashAvailable)
                    <label class="pay-option">
                        <input type="radio" name="payment_method" value="cash" @checked(old('payment_method') === 'cash' || ! $omAvailable)>
                        <div><b>Paiement à la livraison</b><small>Réglez en espèces à la réception de votre colis.</small></div>
                        <span class="cash-badge">Espèces</span>
                    </label>
                    @endif
                </div>
            </fieldset>
        </div>

        <aside class="summary">
            <h2>Votre commande</h2>
            @foreach ($lines as $l)
                <div class="mini-line">
                    <div class="thumb">@include('partials.art', ['product' => $l->product, 'label' => false])<span class="q">{{ $l->quantity }}</span></div>
                    <div>{{ $l->product->name }}<br><small style="color:var(--muted)">{{ collect([$l->size, $l->color])->filter()->join(' · ') }}</small></div>
                    <span class="num">{{ gnf($l->total) }}</span>
                </div>
            @endforeach
            <div class="sum-row" style="border-top:1px solid var(--line);padding-top:14px"><span>Sous-total</span><span class="num">{{ gnf($subtotal) }}</span></div>
            <div class="sum-row"><span>Livraison</span><span class="num" id="fee">{{ $feeConakry ? gnf($feeConakry) : 'Offerte' }}</span></div>
            <div class="sum-row total"><span>Total</span><span class="num" id="total">{{ gnf($subtotal + $feeConakry) }}</span></div>
            <button type="submit" class="btn btn-accent btn-block" data-label="Envoi en cours…">Valider et payer</button>
            <div class="secure"><svg><use href="#i-lock"/></svg> Vos informations servent uniquement à la livraison.</div>
        </aside>
    </form>
</div>
@endsection
