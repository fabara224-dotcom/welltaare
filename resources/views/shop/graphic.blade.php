@extends('layouts.shop')
@section('title', 'Infographie & design graphique')
@section('description', 'Studio d\'infographie du COMPLEXE WELLTAARÉ COLLECTION : logos, identité visuelle, affiches, flyers, cartes de visite, bâches et visuels réseaux sociaux à Conakry.')

@section('content')
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb"><a href="{{ route('home') }}">Accueil</a> / <span>Infographie & design graphique</span></nav>
        <h1>Infographie & design graphique</h1>
        <p>Valorisez votre marque avec des visuels professionnels. Décrivez votre projet : nous revenons vers vous avec une proposition et un devis.</p>
    </div>
</section>

<div class="container design-layout">
    <aside class="design-aside">
        @include('partials.visual', ['key' => 'studio', 'fallback' => 'art-studio', 'style' => 'aspect-ratio:4/3', 'label' => 'Valoriser les marques<br>et la créativité africaine.', 'labelStyle' => 'font-size:1.6rem'])
        <ul class="service-list">
            @foreach (\App\Models\GraphicRequest::SERVICES as $k => $label)
                @continue($k === 'autre')
                <li style="font-size:1.1rem"><span class="num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $label }}</li>
            @endforeach
        </ul>
    </aside>

    <form method="post" action="{{ route('graphic.store') }}" enctype="multipart/form-data" class="checkout-form" data-once novalidate>
        @csrf
        @if ($errors->any())<div class="alert alert-err">Vérifiez les champs en rouge.</div>@endif
        <fieldset>
            <legend>Votre projet</legend>
            <div class="field @error('service') has-error @enderror"><label for="service">Prestation *</label>
                <select id="service" name="service"><option value="">Choisir…</option>@foreach (\App\Models\GraphicRequest::SERVICES as $k => $label)<option value="{{ $k }}" @selected(old('service', request('service')) === $k)>{{ $label }}</option>@endforeach</select>
                @error('service')<span class="error">{{ $message }}</span>@enderror</div>
            <div class="field @error('description') has-error @enderror"><label for="description">Description *</label><textarea id="description" name="description" placeholder="Votre activité, le message à faire passer, les couleurs souhaitées, les formats (A5, A3, bâche 3x1 m…), les quantités…">{{ old('description') }}</textarea>@error('description')<span class="error">{{ $message }}</span>@enderror</div>
            <label class="upload" for="attachment">
                <span class="label">Fichier de référence (facultatif)</span><br>
                <small style="color:var(--muted)">Logo actuel, exemple, maquette · JPG, PNG, WEBP ou PDF · 10 Mo max</small>
                <input type="file" id="attachment" name="attachment" accept="image/jpeg,image/png,image/webp,application/pdf">
                @error('attachment')<span class="error-text">{{ $message }}</span>@enderror
            </label>
            <div class="grid-2">
                <div class="field"><label for="budget">Budget indicatif (GNF)</label><input id="budget" name="budget" value="{{ old('budget') }}" inputmode="numeric"></div>
                <div class="field @error('deadline') has-error @enderror"><label for="deadline">Pour quelle date ?</label><input id="deadline" type="date" name="deadline" value="{{ old('deadline') }}" min="{{ now()->addDay()->format('Y-m-d') }}">@error('deadline')<span class="error">{{ $message }}</span>@enderror</div>
            </div>
        </fieldset>
        <fieldset>
            <legend>Vos coordonnées</legend>
            <div class="grid-2">
                <div class="field @error('customer_name') has-error @enderror"><label for="customer_name">Nom complet *</label><input id="customer_name" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" autocomplete="name">@error('customer_name')<span class="error">{{ $message }}</span>@enderror</div>
                <div class="field"><label for="company">Entreprise / marque</label><input id="company" name="company" value="{{ old('company') }}"></div>
            </div>
            <div class="grid-2">
                <div class="field @error('customer_phone') has-error @enderror"><label for="customer_phone">Téléphone / WhatsApp *</label><input id="customer_phone" name="customer_phone" value="{{ old('customer_phone', auth()->user()?->phone) }}" inputmode="tel" placeholder="+224 6xx xx xx xx">@error('customer_phone')<span class="error">{{ $message }}</span>@enderror</div>
                <div class="field"><label for="customer_email">E-mail</label><input id="customer_email" type="email" name="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}"></div>
            </div>
        </fieldset>
        <button type="submit" class="btn" data-label="Envoi…">Envoyer ma demande de devis</button>
    </form>
</div>
@endsection
