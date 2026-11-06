<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Tableau de bord') · Gestion {{ config('shop.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..700;1,6..96,400..600&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v=1">
</head>
<body>
@php
    $newOrders = \App\Models\Order::where('status', 'nouvelle')->count();
    $newDesigns = \App\Models\DesignRequest::where('status', 'nouvelle')->count();
    $newGraphics = \App\Models\GraphicRequest::where('status', 'nouvelle')->count();
@endphp
<div class="mobile-top"><b>WELLTAARÉ</b><button type="button" onclick="document.querySelector('.side').classList.toggle('open')">Menu</button></div>
<div class="app">
    <aside class="side" onclick="if(event.target===this)this.classList.remove('open')">
        <a href="{{ route('admin.dashboard') }}" class="brand"><small style="letter-spacing:.5em;color:#9D9384">COMPLEXE</small><b>WELLTAARÉ</b><small>GESTION</small></a>
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'on' : '' }}">Tableau de bord</a>
            <span class="grp">Ventes</span>
            <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'on' : '' }}">Commandes @if($newOrders)<span class="badge">{{ $newOrders }}</span>@endif</a>
            <a href="{{ route('admin.designs.index') }}" class="{{ request()->routeIs('admin.designs.*') ? 'on' : '' }}">Sur mesure @if($newDesigns)<span class="badge">{{ $newDesigns }}</span>@endif</a>
            <a href="{{ route('admin.graphics.index') }}" class="{{ request()->routeIs('admin.graphics.*') ? 'on' : '' }}">Infographie @if($newGraphics)<span class="badge">{{ $newGraphics }}</span>@endif</a>
            <span class="grp">Catalogue</span>
            <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.index', 'admin.products.edit') ? 'on' : '' }}">Produits</a>
            <a href="{{ route('admin.products.create') }}" class="{{ request()->routeIs('admin.products.create') ? 'on' : '' }}">+ Ajouter un produit</a>
            <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'on' : '' }}">Catégories</a>
            <span class="grp">Boutique</span>
            <a href="{{ route('admin.appearance.edit') }}" class="{{ request()->routeIs('admin.appearance.*') ? 'on' : '' }}">Apparence & photos</a>
            <a href="{{ route('admin.settings.edit') }}" class="{{ request()->routeIs('admin.settings.*') ? 'on' : '' }}">Paramètres</a>
            <a href="{{ route('home') }}" target="_blank">Voir le site ↗</a>
            @if (auth()->user()->isDeveloper())
                <span class="grp" style="color:var(--gold)">Développeur</span>
                <a href="{{ route('admin.dev.index') }}" class="{{ request()->routeIs('admin.dev.index') ? 'on' : '' }}">Diagnostic & outils</a>
                <a href="{{ route('admin.dev.logs') }}" class="{{ request()->routeIs('admin.dev.logs') ? 'on' : '' }}">Journal des erreurs</a>
                <a href="{{ route('admin.dev.users') }}" class="{{ request()->routeIs('admin.dev.users') ? 'on' : '' }}">Comptes & accès</a>
                <a href="{{ route('admin.dev.database') }}" class="{{ request()->routeIs('admin.dev.database') ? 'on' : '' }}">Base de données</a>
            @endif
        </nav>
        <div class="foot">
            <span>{{ auth()->user()->name }}<br><small style="color:#8F8096">{{ auth()->user()->roleLabel() }}</small></span>
            @if (app()->isDownForMaintenance())<span style="color:#E3A955">● Site en maintenance</span>@endif
            <form method="post" action="{{ route('admin.logout') }}">@csrf<button>Se déconnecter</button></form>
        </div>
    </aside>
    <main class="main">
        @if (session('status'))<div class="flash flash-ok">{{ session('status') }}</div>@endif
        @if ($errors->any())<div class="flash flash-err">{{ $errors->first() }}</div>@endif
        @if (session('output'))<pre class="console">{{ session('output') }}</pre>@endif
        @yield('content')
    </main>
</div>
@stack('scripts')
</body>
</html>
