@extends('layouts.shop')
@section('title', 'Mon compte')

@section('content')
<section class="page-hero">
    <div class="container" style="display:flex;justify-content:space-between;align-items:flex-end;gap:16px;flex-wrap:wrap">
        <div><span class="eyebrow gold">Mon compte</span><h1>Bonjour {{ \Illuminate\Support\Str::of($user->name)->explode(' ')->first() }}</h1><p>{{ $user->email }} · {{ $user->phone }}</p></div>
        <form method="post" action="{{ route('logout') }}">@csrf<button class="btn btn-ghost btn-sm">Se déconnecter</button></form>
    </div>
</section>

<div class="container account" data-tabs>
    @if (session('status'))<div class="alert alert-ok">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="alert alert-err">{{ $errors->first() }}</div>@endif

    <div class="tabs-inline" role="tablist" style="margin-top:8px;overflow-x:auto">
        <button role="tab" data-tab="commandes" aria-selected="true">Mes commandes <span class="num">({{ $orders->count() }})</span></button>
        <button role="tab" data-tab="demandes" aria-selected="false">Mes demandes <span class="num">({{ $designs->count() + $graphics->count() }})</span></button>
        <button role="tab" data-tab="infos" aria-selected="false">Mes informations</button>
    </div>

    <div data-panel="commandes">
        @forelse ($orders as $o)
            <a class="acc-card" href="{{ route('checkout.thanks', $o) }}">
                <div><b class="num">{{ $o->reference }}</b><small>{{ $o->created_at->translatedFormat('d F Y') }} · {{ $o->items->sum('quantity') }} article(s)</small></div>
                <div class="acc-right"><span class="status-pill st-{{ $o->status }}">{{ $o->statusLabel() }}</span><b class="num">{{ gnf($o->total) }}</b></div>
            </a>
        @empty
            <div class="empty-state"><h3>Aucune commande</h3><p>Vos commandes apparaîtront ici avec leur suivi.</p><p style="margin-top:18px"><a class="btn btn-sm" href="{{ route('shop') }}">Découvrir la boutique</a></p></div>
        @endforelse
    </div>

    <div data-panel="demandes" hidden>
        @forelse ($designs->concat($graphics)->sortByDesc('created_at') as $d)
            <a class="acc-card" href="{{ $d instanceof \App\Models\DesignRequest ? route('design.thanks', $d) : route('graphic.thanks', $d) }}">
                <div><b class="num">{{ $d->reference }}</b><small>{{ $d instanceof \App\Models\DesignRequest ? 'Sur mesure · '.$d->garment_type : 'Design graphique · '.$d->serviceLabel() }} · {{ $d->created_at->translatedFormat('d F Y') }}</small></div>
                <div class="acc-right"><span class="status-pill st-{{ $d->status }}">{{ $d->statusLabel() }}</span>@if ($d->quote_amount)<b class="num">Devis {{ gnf($d->quote_amount) }}</b>@endif</div>
            </a>
        @empty
            <div class="empty-state"><h3>Aucune demande</h3><p>Créations sur mesure et projets graphiques apparaîtront ici.</p>
                <p style="margin-top:18px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap"><a class="btn btn-sm" href="{{ route('design.create') }}">Sur mesure</a><a class="btn btn-ghost btn-sm" href="{{ route('graphic.create') }}">Design graphique</a></p></div>
        @endforelse
    </div>

    <div data-panel="infos" hidden>
        <div class="grid-2" style="align-items:start;gap:28px">
            <form method="post" action="{{ route('account.update') }}" class="checkout-form summary" style="position:static">
                @csrf @method('put')
                <h2 style="font-size:1.5rem">Coordonnées & livraison</h2>
                <div class="field"><label for="a-name">Nom complet</label><input id="a-name" name="name" value="{{ old('name', $user->name) }}"></div>
                <div class="field"><label for="a-email">E-mail</label><input id="a-email" type="email" name="email" value="{{ old('email', $user->email) }}"></div>
                <div class="field"><label for="a-phone">Téléphone</label><input id="a-phone" name="phone" value="{{ old('phone', $user->phone) }}" inputmode="tel"></div>
                <div class="grid-2">
                    <div class="field"><label for="a-city">Ville</label><input id="a-city" name="city" value="{{ old('city', $user->city) }}"></div>
                    <div class="field"><label for="a-district">Quartier</label><input id="a-district" name="district" value="{{ old('district', $user->district) }}"></div>
                </div>
                <div class="field"><label for="a-address">Point de repère</label><input id="a-address" name="address" value="{{ old('address', $user->address) }}"></div>
                <button class="btn">Enregistrer</button>
            </form>
            <form method="post" action="{{ route('account.password') }}" class="checkout-form summary" style="position:static">
                @csrf @method('put')
                <h2 style="font-size:1.5rem">Mot de passe</h2>
                <div class="field"><label for="cp">Mot de passe actuel</label><input id="cp" type="password" name="current_password" autocomplete="current-password"></div>
                <div class="field"><label for="np">Nouveau mot de passe</label><input id="np" type="password" name="password" autocomplete="new-password"></div>
                <div class="field"><label for="np2">Confirmer</label><input id="np2" type="password" name="password_confirmation" autocomplete="new-password"></div>
                <button class="btn btn-ghost">Changer le mot de passe</button>
            </form>
        </div>
    </div>
</div>
@endsection
