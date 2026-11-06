@extends('layouts.shop')
@section('title', 'Connexion')

@section('content')
<div class="auth">
    <div class="auth-art">
        @if ($img = site_image('auth'))<img src="{{ $img }}" alt="">@else<div class="art art-femme-0"></div>@endif
        <div class="auth-quote"><span class="eyebrow gold">COMPLEXE WELLTAARÉ COLLECTION</span><p>Élégance · Identité · Innovation</p></div>
    </div>
    <div class="auth-box">
        <form method="post" action="{{ route('login.attempt') }}" class="auth-form" novalidate data-once>
            @csrf
            <div class="auth-tabs"><a href="{{ route('login') }}" class="on">Se connecter</a><a href="{{ route('register') }}">Créer un compte</a></div>
            <div><h1>Bon retour parmi nous</h1><p class="muted-text">Retrouvez vos commandes, vos créations sur mesure et commandez plus vite.</p></div>
            @if (session('status'))<div class="alert alert-ok">{{ session('status') }}</div>@endif
            @error('email')<div class="alert alert-err">{{ $message }}</div>@enderror
            <div class="field"><label for="email">E-mail</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus></div>
            <div class="field"><label for="password">Mot de passe</label><input id="password" type="password" name="password" autocomplete="current-password" required></div>
            <label class="check-line"><input type="checkbox" name="remember" value="1" checked> Rester connecté sur cet appareil</label>
            <button type="submit" class="btn btn-block" data-label="Connexion…">Se connecter</button>
            <p class="muted-text" style="font-size:.88rem">Mot de passe oublié ?
                @if ($wa = wa_link(setting('shop_whatsapp'), 'Bonjour, j\'ai oublié le mot de passe de mon compte client.'))<a class="link" href="{{ $wa }}" target="_blank" rel="noopener">Écrivez-nous sur WhatsApp</a>@else contactez la boutique.@endif
            </p>
            <p class="muted-text">Pas encore de compte ? <a class="link" href="{{ route('register') }}">Créer un compte</a></p>
        </form>
    </div>
</div>
@endsection
