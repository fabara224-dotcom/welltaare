@extends('layouts.shop')
@section('title', $product->name)
@section('description', \Illuminate\Support\Str::limit($product->description, 150))

@section('content')
<div class="container">
    <nav class="breadcrumb" style="padding-top:24px">
        <a href="{{ route('home') }}">Accueil</a> /
        <a href="{{ route('shop.'.$product->gender) }}">{{ ucfirst($product->gender) }}</a> /
        @if ($product->category)<a href="{{ route('shop.'.$product->gender, ['categorie' => $product->category->slug]) }}">{{ $product->category->name }}</a> /@endif
        <span>{{ $product->name }}</span>
    </nav>

    <div class="pdp">
        <div class="gallery {{ $product->images->count() > 1 ? '' : 'single' }}">
            @if ($product->images->count() > 1)
                <div class="thumbs">
                    @foreach ($product->images as $i => $img)
                        <button type="button" data-gallery-thumb data-src="{{ $img->url() }}" aria-current="{{ $i === 0 ? 'true' : 'false' }}" aria-label="Photo {{ $i + 1 }}"><img src="{{ $img->url() }}" alt=""></button>
                    @endforeach
                </div>
            @endif
            <div class="main">
                @if ($product->images->isNotEmpty())
                    <div class="art has-img"><img data-gallery-main src="{{ $product->images->first()->url() }}" alt="{{ $product->name }}"></div>
                @else
                    @include('partials.art', ['product' => $product])
                @endif
            </div>
        </div>

        <div class="pdp-info">
            <div>
                <span class="eyebrow gold">{{ $product->category->name ?? ucfirst($product->gender) }}</span>
                <h1 style="margin-top:10px">{{ $product->name }}</h1>
            </div>
            <div class="price">
                {{ gnf($product->price) }}
                @if ($product->discountPercent())<s>{{ gnf($product->compare_price) }}</s><span class="off">-{{ $product->discountPercent() }}%</span>@endif
            </div>
            @if ($product->description)<p class="desc">{{ $product->description }}</p>@endif

            @if ($errors->any())<div class="alert alert-err">{{ $errors->first() }}</div>@endif

            <form method="post" action="{{ route('cart.add') }}" id="buy-form" style="display:flex;flex-direction:column;gap:22px">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                @if ($product->sizes)
                    <div class="field">
                        <span class="label">Taille</span>
                        <div class="options">
                            @foreach ($product->sizes as $i => $s)
                                <input type="radio" id="size-{{ $i }}" name="size" value="{{ $s }}" @checked(old('size') === $s)>
                                <label for="size-{{ $i }}">{{ $s }}</label>
                            @endforeach
                        </div>
                        <span class="hint">Entre deux tailles ? <a class="link" href="{{ route('design.create') }}">Faites-la ajuster sur mesure</a>.</span>
                    </div>
                @endif
                @if ($product->colors)
                    <div class="field">
                        <span class="label">Couleur</span>
                        <div class="options">
                            @foreach ($product->colors as $i => $c)
                                <input type="radio" id="color-{{ $i }}" name="color" value="{{ $c }}" @checked(old('color', $product->colors[0]) === $c)>
                                <label for="color-{{ $i }}">{{ $c }}</label>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($product->isOutOfStock())
                    <button class="btn btn-block" disabled>Épuisé</button>
                    <a class="btn btn-ghost btn-block" href="{{ route('design.create') }}">Le commander sur mesure</a>
                @else
                    <div class="buy-row">
                        <div class="qty"><button type="button" data-step="-1" aria-label="Moins">−</button><input type="number" name="quantity" value="1" min="1" max="{{ $product->stock !== null ? min(20, $product->stock) : 20 }}" aria-label="Quantité"><button type="button" data-step="1" aria-label="Plus">+</button></div>
                        <button class="btn btn-accent">Ajouter au panier</button>
                    </div>
                    <button class="btn btn-ghost btn-block" name="buy_now" value="1">Acheter maintenant</button>
                    @if ($product->stock !== null && $product->stock <= 3)<p class="stock-note">Plus que {{ $product->stock }} en stock.</p>@endif
                @endif
            </form>

            <div class="perks">
                <details open><summary>Livraison</summary><p>Livraison à Conakry : {{ gnf(setting('delivery_fee_conakry')) }}. En région : {{ gnf(setting('delivery_fee_other')) }}.@if((int) setting('free_delivery_from') > 0) Offerte dès {{ gnf(setting('free_delivery_from')) }} d'achat.@endif Nous vous appelons pour fixer l'heure de livraison.</p></details>
                <details><summary>Paiement</summary><p>Payez en toute sécurité avec Orange Money depuis votre téléphone{{ setting('cash_on_delivery') === '1' ? ', ou en espèces à la livraison' : '' }}.</p></details>
                <details><summary>Conseil & sur mesure</summary><p>Besoin d'une autre taille, d'une couleur ou d'un ajustement ? Notre atelier crée la pièce à vos mesures.</p></details>
            </div>
        </div>
    </div>

    @unless ($product->isOutOfStock())
        <div class="buy-sticky">
            <div><small>{{ \Illuminate\Support\Str::limit($product->name, 28) }}</small><b class="num">{{ gnf($product->price) }}</b></div>
            <button form="buy-form" class="btn btn-accent">Ajouter au panier</button>
        </div>
    @endunless

    @if ($related->isNotEmpty())
        <section style="padding-bottom:88px">
            <div class="section-head"><div><span class="eyebrow gold">À découvrir aussi</span><h2>Vous aimerez aussi</h2></div></div>
            <div class="products">@foreach ($related as $rel)@include('partials.product-card', ['product' => $rel])@endforeach</div>
        </section>
    @endif
</div>
@endsection
