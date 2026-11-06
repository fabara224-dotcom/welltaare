@extends('layouts.shop')
@section('title', 'Créer un compte')

@section('content')
<div class="auth">
    <div class="auth-art">
        @if ($img = site_image('auth'))<img src="{{ $img }}" alt="">@else<div class="art art-homme-0"></div>@endif
        <div class="auth-quote"><span class="eyebrow gold">Avantages client</span><p>Suivi de vos commandes · Coordonnées enregistrées · Historique de vos créations</p></div>
    </div>
    <div class="auth-box">
        <form method="post" action="{{ route('register.store') }}" class="auth-form" novalidate data-once>
            @csrf
            <div class="auth-tabs"><a href="{{ route('login') }}">Se connecter</a><a href="{{ route('register') }}" class="on">Créer un compte</a></div>
            <div><h1>Créer mon compte</h1><p class="muted-text">Moins d'une minute, et vos prochaines commandes seront plus rapides.</p></div>
            @if ($errors->any())<div class="alert alert-err">Vérifiez les champs en rouge.</div>@endif
            <div class="field @error('name') has-error @enderror"><label for="name">Nom complet</label><input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required>@error('name')<span class="error">{{ $message }}</span>@enderror</div>
            <div class="field @error('phone') has-error @enderror"><label for="phone">Téléphone / WhatsApp</label><input id="phone" name="phone" value="{{ old('phone') }}" inputmode="tel" autocomplete="tel" placeholder="+224 6xx xx xx xx" required>@error('phone')<span class="error">{{ $message }}</span>@enderror</div>
            <div class="field @error('email') has-error @enderror"><label for="email">E-mail</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<span class="error">{{ $message }}</span>@enderror</div>
            <div class="grid-2">
                <div class="field @error('password') has-error @enderror"><label for="password">Mot de passe</label><input id="password" type="password" name="password" autocomplete="new-password" required>@error('password')<span class="error">{{ $message }}</span>@enderror</div>
                <div class="field"><label for="password_confirmation">Confirmer</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required></div>
            </div>
            <span class="muted-text" style="font-size:.84rem">8 caractères minimum.</span>
            <button type="submit" class="btn btn-block" data-label="Création…">Créer mon compte</button>
            <p class="muted-text">Déjà un compte ? <a class="link" href="{{ route('login') }}">Se connecter</a></p>
        </form>
    </div>
</div>
@endsection
