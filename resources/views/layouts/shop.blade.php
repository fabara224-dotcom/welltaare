<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Mode, création & design à Conakry') · {{ config('shop.name') }}</title>
    <meta name="description" content="@yield('description', 'COMPLEXE WELLTAARÉ COLLECTION, Conakry : robes élégantes et tenues de soirée pour femme, mode africaine homme et femme, créations sur mesure, infographie et design graphique. Paiement Orange Money.')">
    <meta property="og:title" content="@yield('title', config('shop.name'))">
    <meta property="og:site_name" content="{{ config('shop.name') }}">
    <meta name="theme-color" content="#0B0A09">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..700;1,6..96,400..600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}?v=5">
</head>
<body>
@include('partials.icons')
<a class="sr-only" href="#contenu">Aller au contenu</a>

@if (setting('announcement'))
    <div class="announce">{{ setting('announcement') }}</div>
@endif

<header class="site-header">
    <div class="container header-inner">
        <div style="display:flex;align-items:center;gap:8px">
            <button class="icon-btn burger" type="button" data-open-menu aria-label="Menu"><svg><use href="#i-menu"/></svg></button>

        </div>
        <a href="{{ route('home') }}" class="brand" aria-label="{{ config('shop.name') }} — accueil">
            @if ($logo = site_image('logo'))<img src="{{ $logo }}" alt="{{ config('shop.name') }}" class="brand-logo">@else<span class="brand-top">COMPLEXE</span><span class="brand-name">WELLTAARÉ</span><span class="brand-sub">COLLECTION</span>@endif
        </a>
        <div class="header-tools">
            <button class="icon-btn" type="button" data-toggle-search aria-label="Rechercher"><svg><use href="#i-search"/></svg></button>
            <a class="icon-btn hide-sm" href="{{ route('tracking') }}"><svg><use href="#i-truck"/></svg><span class="lbl">Suivi</span></a>
            @auth
                <a class="icon-btn hide-sm" href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('account') }}" aria-label="Mon compte"><svg><use href="#i-user"/></svg><span class="lbl">{{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first() }}</span></a>
            @else
                <a class="icon-btn hide-sm" href="{{ route('login') }}"><svg><use href="#i-user"/></svg><span class="lbl">Connexion</span></a>
            @endauth
            <a class="icon-btn" href="{{ route('cart') }}" aria-label="Panier"><svg><use href="#i-bag"/></svg><span class="lbl">Panier</span><span class="cart-count num">{{ $cartCount }}</span></a>
        </div>
    </div>
    <div class="nav-row">
        <nav class="main-nav container" aria-label="Navigation principale">
                <a href="{{ route('shop.femme') }}" class="{{ request()->routeIs('shop.femme') ? 'active' : '' }}">Femme</a>
                <a href="{{ route('shop.homme') }}" class="{{ request()->routeIs('shop.homme') ? 'active' : '' }}">Homme</a>
                <a href="{{ route('shop', ['nouveau' => 1]) }}" class="{{ request()->routeIs('shop') && request('nouveau') ? 'active' : '' }}">Nouveautés</a>
                <a href="{{ route('design.create') }}" class="{{ request()->routeIs('design.*') ? 'active' : '' }}">Sur mesure</a>
                <a href="{{ route('graphic.create') }}" class="{{ request()->routeIs('graphic.*') ? 'active' : '' }}">Design graphique</a>
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">Le Complexe</a>
            </nav>
    </div>
    <div class="search-bar" id="search-bar" hidden>
        <form class="container" action="{{ route('shop') }}" method="get" role="search">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Robe de soirée, costume en wax, boubou…" aria-label="Rechercher un article">
            <button class="btn btn-sm">Rechercher</button>
        </form>
    </div>
</header>

<div class="mobile-nav" id="mobile-nav" hidden>
    <nav class="panel" aria-label="Menu mobile">
        <a href="{{ route('home') }}">Accueil</a>
        <a href="{{ route('shop.femme') }}">Femme</a>
        <a href="{{ route('shop.homme') }}">Homme</a>
        <a href="{{ route('shop', ['nouveau' => 1]) }}">Nouveautés</a>
        <a href="{{ route('design.create') }}">Création sur mesure</a>
        <a href="{{ route('graphic.create') }}">Infographie & design</a>
        <a href="{{ route('about') }}">Le Complexe</a>
        <a href="{{ route('tracking') }}">Suivre ma commande</a>
        <a href="{{ route('contact') }}">Contact</a>
        @auth<a href="{{ route('account') }}">Mon compte</a>@else<a href="{{ route('login') }}">Connexion / Créer un compte</a>@endauth
        <small class="eyebrow">{{ setting('shop_hours') }}</small>
    </nav>
    <div class="scrim" data-close-menu></div>
</div>

@if (session('cart_added'))
    <div class="toast" role="status">« {{ session('cart_added') }} » ajouté au panier <a href="{{ route('cart') }}">Voir le panier</a></div>
@endif

<main id="contenu">
    @yield('content')
</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <a href="{{ route('home') }}" class="brand"><span class="brand-top" style="padding-left:0">COMPLEXE</span><span class="brand-name">WELLTAARÉ</span><span class="brand-sub">COLLECTION</span></a>
                <p style="margin-top:18px;max-width:36ch">Un univers dédié à la mode, à la créativité et au design, à Conakry.</p>
                <p style="margin-top:10px;font-family:var(--f-display);font-style:italic;color:var(--gold)">Élégance · Identité · Innovation</p>
            </div>
            <div>
                <h4>Boutique</h4>
                <ul>
                    <li><a href="{{ route('shop.femme') }}">Collection Femme</a></li>
                    <li><a href="{{ route('shop.homme') }}">Collection Homme</a></li>
                    <li><a href="{{ route('shop', ['promo' => 1]) }}">Promotions</a></li>
                    <li><a href="{{ route('design.create') }}">Création sur mesure</a></li>
                    <li><a href="{{ route('graphic.create') }}">Infographie & design graphique</a></li>
                    <li><a href="{{ route('about') }}">Le Complexe</a></li>
                </ul>
            </div>
            <div>
                <h4>Service client</h4>
                <ul>
                    <li><a href="{{ route('tracking') }}">Suivre ma commande</a></li>
                    <li><a href="{{ route('contact') }}">Contact & boutique</a></li>
                    <li>Livraison à Conakry et en région</li>
                    <li>Paiement Orange Money{{ setting('cash_on_delivery') === '1' ? ' ou à la livraison' : '' }}</li>
                </ul>
            </div>
            <div>
                <h4>Nous trouver</h4>
                <ul>
                    <li>{{ setting('shop_address') }}</li>
                    @if (setting('shop_phone'))<li class="num">{{ setting('shop_phone') }}</li>@endif
                    @if (setting('shop_email'))<li>{{ setting('shop_email') }}</li>@endif
                    <li>{{ setting('shop_hours') }}</li>
                    <li style="display:flex;gap:14px;margin-top:6px">
                        @if (setting('instagram'))<a href="{{ setting('instagram') }}" target="_blank" rel="noopener">Instagram</a>@endif
                        @if (setting('facebook'))<a href="{{ setting('facebook') }}" target="_blank" rel="noopener">Facebook</a>@endif
                        @if (setting('tiktok'))<a href="{{ setting('tiktok') }}" target="_blank" rel="noopener">TikTok</a>@endif
                    </li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© {{ date('Y') }} {{ config('shop.name') }} · Tous droits réservés</span>
            <span class="pay-logos"><span class="om-badge">Orange Money</span>@if (setting('cash_on_delivery') === '1')<span class="cash-badge" style="background:rgba(255,255,255,.1)">Paiement à la livraison</span>@endif</span>
        </div>
    </div>
</footer>

<nav class="tabbar" aria-label="Navigation mobile">
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'on' : '' }}"><svg><use href="#i-home"/></svg>Accueil</a>
    <a href="{{ route('shop') }}" class="{{ request()->routeIs('shop*', 'product.*') ? 'on' : '' }}"><svg><use href="#i-grid"/></svg>Boutique</a>
    <a href="{{ route('design.create') }}" class="{{ request()->routeIs('design.*', 'graphic.*') ? 'on' : '' }}"><svg><use href="#i-needle"/></svg>Sur mesure</a>
    <a href="{{ route('cart') }}" class="{{ request()->routeIs('cart', 'checkout*') ? 'on' : '' }}"><svg><use href="#i-bag"/></svg>Panier @if($cartCount)<span class="cart-count num">{{ $cartCount }}</span>@endif</a>
    <a href="{{ auth()->check() ? route('account') : route('login') }}" class="{{ request()->routeIs('account', 'login', 'register') ? 'on' : '' }}"><svg><use href="#i-user"/></svg>{{ auth()->check() ? 'Compte' : 'Connexion' }}</a>
</nav>

@if ($wa = wa_link(setting('shop_whatsapp'), 'Bonjour '.config('shop.name').', '))
    <a class="wa-float" href="{{ $wa }}" target="_blank" rel="noopener" aria-label="Nous écrire sur WhatsApp"><svg><use href="#i-wa"/></svg></a>
@endif

<script src="{{ asset('js/shop.js') }}?v=5" defer></script>
@stack('scripts')
</body>
</html>
