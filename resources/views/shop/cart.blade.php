@extends('layouts.shop')
@section('title', 'Votre panier')

@section('content')
<section class="page-hero"><div class="container"><span class="eyebrow">Étape 1 sur 3</span><h1>Votre panier</h1></div></section>

<div class="container">
    @if ($errors->any())<div class="alert alert-err" style="margin-top:24px">{{ $errors->first() }}</div>@endif
    @if (session('status'))<div class="alert alert-ok" style="margin-top:24px">{{ session('status') }}</div>@endif

    @if ($lines->isEmpty())
        <div class="empty-state" style="margin-block:40px 88px">
            <h3>Votre panier est vide</h3>
            <p>Parcourez nos collections et ajoutez vos coups de cœur.</p>
            <p style="margin-top:22px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap"><a class="btn" href="{{ route('shop.femme') }}">Femme</a><a class="btn btn-ghost" href="{{ route('shop.homme') }}">Homme</a></p>
        </div>
    @else
    <div class="cart-layout">
        <div class="cart-lines">
            @foreach ($lines as $l)
                <div class="cart-line">
                    <a href="{{ route('product.show', $l->product) }}" class="thumb">@include('partials.art', ['product' => $l->product, 'label' => false])</a>
                    <div>
                        <h3><a href="{{ route('product.show', $l->product) }}">{{ $l->product->name }}</a></h3>
                        <div class="sub">{{ collect([$l->size ? 'Taille '.$l->size : null, $l->color])->filter()->join(' · ') }}</div>
                        <div class="sub num">{{ gnf($l->unit_price) }} l'unité</div>
                        <div class="actions">
                            <form method="post" action="{{ route('cart.update') }}">
                                @csrf @method('patch')
                                <input type="hidden" name="key" value="{{ $l->key }}">
                                <div class="qty" data-autosubmit><button type="button" data-step="-1" aria-label="Moins">−</button><input type="number" name="quantity" value="{{ $l->quantity }}" min="1" max="20" aria-label="Quantité" onchange="this.form.submit()"><button type="button" data-step="1" aria-label="Plus">+</button></div>
                            </form>
                            <form method="post" action="{{ route('cart.remove') }}">
                                @csrf @method('delete')
                                <input type="hidden" name="key" value="{{ $l->key }}">
                                <button class="rm">Retirer</button>
                            </form>
                        </div>
                    </div>
                    <div class="price line-total">{{ gnf($l->total) }}</div>
                </div>
            @endforeach
            <p style="margin-top:20px"><a class="link" href="{{ route('shop') }}">← Continuer mes achats</a></p>
        </div>

        <aside class="summary">
            <h2>Récapitulatif</h2>
            <div class="sum-row"><span>Sous-total</span><span class="num">{{ gnf($subtotal) }}</span></div>
            <div class="sum-row"><span>Livraison</span><span>Calculée à l'étape suivante</span></div>
            @if ((int) setting('free_delivery_from') > 0 && $subtotal < (int) setting('free_delivery_from'))
                <div class="alert alert-info" style="font-size:.86rem">Plus que <b>{{ gnf((int) setting('free_delivery_from') - $subtotal) }}</b> pour la livraison offerte.</div>
            @endif
            <div class="sum-row total"><span>Total</span><span class="num">{{ gnf($subtotal) }}</span></div>
            <a class="btn btn-accent btn-block" href="{{ route('checkout') }}">Passer la commande</a>
            <div class="secure"><svg><use href="#i-lock"/></svg> Paiement sécurisé Orange Money</div>
        </aside>
    </div>
    @endif
</div>
@endsection
