<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Connexion · Gestion {{ config('shop.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..700;1,6..96,400..600&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v=1">
</head>
<body>
<div class="login">
    <div class="login-art">
        <div><small style="margin:0 0 6px">COMPLEXE</small><b>WELLTAARÉ</b><small>COLLECTION</small></div>
        <h2>Espace de gestion de la boutique.</h2>
        <span style="font-size:.8rem;opacity:.8">Accès réservé à l'administration</span>
    </div>
    <div class="login-box">
        <form method="post" action="{{ route('admin.login.attempt') }}" novalidate>
            @csrf
            <div><h1>Connexion</h1><p class="muted" style="margin:6px 0 0">Connectez-vous pour gérer les commandes et les produits.</p></div>
            @if (session('status'))<div class="flash flash-ok" style="margin:0">{{ session('status') }}</div>@endif
            @error('email')<div class="flash flash-err" style="margin:0">{{ $message }}</div>@enderror
            <div class="field"><label for="email">E-mail</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus></div>
            <div class="field"><label for="password">Mot de passe</label><input id="password" type="password" name="password" autocomplete="current-password" required></div>
            <label style="display:flex;gap:8px;align-items:center;font-size:.9rem"><input type="checkbox" name="remember" value="1" style="accent-color:var(--accent)"> Rester connecté</label>
            <button class="btn btn-accent" style="padding:13px">Se connecter</button>
        </form>
    </div>
</div>
</body>
</html>
