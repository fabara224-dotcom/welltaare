@extends('layouts.shop')
@php
    $title = $gender === 'femme' ? 'Collection Femme' : ($gender === 'homme' ? 'Collection Homme' : (request('africain') ? 'Collections africaines' : (request('nouveau') ? 'Nouveautés' : (request('promo') ? 'Promotions' : 'La boutique'))));
    $routeName = $gender ? 'shop.'.$gender : 'shop';
    $intro = [
        'femme' => 'Robes élégantes, tenues de soirée, ensembles et tenues africaines : des pièces qui subliment la silhouette.',
        'homme' => 'Costumes africains, grands boubous brodés et chemises en wax ou kente, pour toutes les cérémonies.',
    ][$gender] ?? (request('africain') ? 'Wax, bazin, kente : des tenues africaines pour hommes et femmes, entre tradition et modernité.' : 'Toute la collection femme et homme.');
@endphp
@section('title', $title)

@section('content')
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb"><a href="{{ route('home') }}">Accueil</a> / <span>{{ $title }}</span></nav>
        <h1>{{ $title }}@if(request('q')) <em style="font-size:.5em;color:var(--muted)">« {{ request('q') }} »</em>@endif</h1>
        <p>{{ $intro }}</p>
    </div>
</section>

<div class="container shop-layout">
    <aside class="filters" aria-label="Filtres">
        <div class="fgroup">
            <h4>Collection</h4>
            <ul>
                <li><a href="{{ route('shop') }}" class="{{ ! $gender ? 'on' : '' }}">Tout</a></li>
                <li><a href="{{ route('shop.femme') }}" class="{{ $gender === 'femme' ? 'on' : '' }}">Femme</a></li>
                <li><a href="{{ route('shop.homme') }}" class="{{ $gender === 'homme' ? 'on' : '' }}">Homme</a></li>
            </ul>
        </div>
        @if ($categories->isNotEmpty())
        <div class="fgroup">
            <h4>Catégories</h4>
            <ul>
                @foreach ($categories as $c)
                    <li><a href="{{ route($routeName, array_merge(request()->except('page'), ['categorie' => $c->slug])) }}" class="{{ request('categorie') === $c->slug ? 'on' : '' }}">{{ $c->name }} <span>{{ $c->products_count }}</span></a></li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="fgroup">
            <h4>Sélection</h4>
            <ul>
                <li><a href="{{ route($routeName, ['nouveau' => 1]) }}" class="{{ request('nouveau') ? 'on' : '' }}">Nouveautés</a></li>
                <li><a href="{{ route($routeName, ['promo' => 1]) }}" class="{{ request('promo') ? 'on' : '' }}">Promotions</a></li>
            </ul>
        </div>
        <div class="mobile-chips">
            <a class="chip {{ ! $gender ? 'on' : '' }}" href="{{ route('shop') }}">Tout</a>
            <a class="chip {{ $gender === 'femme' ? 'on' : '' }}" href="{{ route('shop.femme') }}">Femme</a>
            <a class="chip {{ $gender === 'homme' ? 'on' : '' }}" href="{{ route('shop.homme') }}">Homme</a>
            @foreach ($categories as $c)
                <a class="chip {{ request('categorie') === $c->slug ? 'on' : '' }}" style="white-space:nowrap" href="{{ route($routeName, ['categorie' => $c->slug]) }}">{{ $c->name }}</a>
            @endforeach
        </div>
    </aside>

    <div>
        <div class="toolbar">
            <span class="count">{{ $products->total() }} article{{ $products->total() > 1 ? 's' : '' }}</span>
            <form method="get">
                @foreach (request()->except('tri', 'page') as $k => $v)<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endforeach
                <label class="sr-only" for="tri">Trier</label>
                <select id="tri" name="tri" onchange="this.form.submit()">
                    <option value="">Les plus récents</option>
                    <option value="prix-asc" @selected(request('tri') === 'prix-asc')>Prix croissant</option>
                    <option value="prix-desc" @selected(request('tri') === 'prix-desc')>Prix décroissant</option>
                </select>
            </form>
        </div>

        @if ($products->isEmpty())
            <div class="empty-state">
                <h3>Aucun article trouvé</h3>
                <p>Essayez une autre catégorie, ou demandez-nous une création sur mesure.</p>
                <p style="margin-top:20px"><a href="{{ route('design.create') }}" class="btn btn-sm">Création sur mesure</a></p>
            </div>
        @else
            <div class="products">@foreach ($products as $product)@include('partials.product-card')@endforeach</div>
            {{ $products->links() }}
        @endif
    </div>
</div>
@endsection
