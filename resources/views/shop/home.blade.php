@extends('layouts.shop')
@section('title', 'Mode, création & design à Conakry')

@section('content')
<section class="hero">
    <div class="hero-copy">
        <span class="eyebrow">Complexe WELLTAARÉ Collection · Conakry</span>
        <h1>L'élégance,<br>l'identité,<br><em>l'innovation.</em></h1>
        <p>Robes élégantes, tenues de soirée et mode africaine pour femmes et hommes, entre tradition et modernité. Et un studio d'infographie au service des marques africaines.</p>
        <div class="hero-actions">
            <a href="{{ route('shop.femme') }}" class="btn btn-light">Collection Femme</a>
            <a href="{{ route('shop.homme') }}" class="btn btn-ghost" style="color:#fff;border-color:rgba(255,255,255,.5)">Collection Homme</a>
        </div>
    </div>
    <a href="{{ route('shop.femme') }}" class="hero-panel">
        @php $hf = $newFemme->first(fn($p) => $p->images->isNotEmpty()); @endphp
        @include('partials.visual', ['src' => site_image('hero_femme') ?? $hf?->mainImageUrl(), 'fallback' => 'art-femme-0', 'alt' => 'Collection Femme'])
        <div class="txt"><h2>Femme</h2><span>Découvrir</span></div>
    </a>
    <a href="{{ route('shop.homme') }}" class="hero-panel">
        @php $hh = $newHomme->first(fn($p) => $p->images->isNotEmpty()); @endphp
        @include('partials.visual', ['src' => site_image('hero_homme') ?? $hh?->mainImageUrl(), 'fallback' => 'art-homme-0', 'alt' => 'Collection Homme'])
        <div class="txt"><h2>Homme</h2><span>Découvrir</span></div>
    </a>
</section>

<section class="trust">
    <div class="container trust-row">
        <div class="trust-item"><svg><use href="#i-truck"/></svg><div><b>Livraison rapide</b><small>Partout à Conakry et en région</small></div></div>
        <div class="trust-item"><svg><use href="#i-lock"/></svg><div><b>Paiement Orange Money</b><small>Sécurisé, depuis votre téléphone</small></div></div>
        <div class="trust-item"><svg><use href="#i-needle"/></svg><div><b>Création sur mesure</b><small>Dessinée et cousue pour vous</small></div></div>
        <div class="trust-item"><svg><use href="#i-chat"/></svg><div><b>Conseil personnalisé</b><small>Par téléphone et WhatsApp</small></div></div>
    </div>
</section>

<section class="section complexe">
    <div class="container complexe-grid">
        <div>
            <span class="eyebrow gold">Le Complexe</span>
            <h2 class="complexe-title">Un univers dédié à la mode, à la créativité et au design.</h2>
        </div>
        <div class="complexe-text">
            <p>COMPLEXE WELLTAARÉ COLLECTION propose des robes élégantes pour femmes, des tenues de soirée ainsi que des collections de vêtements africains pour hommes et femmes, alliant tradition et modernité.</p>
            <p>Nous sommes également spécialisés dans l'infographie et le design graphique, avec pour ambition de valoriser les marques et la créativité africaine.</p>
            <a class="link" href="{{ route('about') }}">Découvrir le Complexe →</a>
        </div>
    </div>
    <div class="container">
        <div class="vision">
            <span class="eyebrow">Notre vision</span>
            <div class="vision-row">
                <div><b>Élégance</b><small>Des coupes soignées et des matières nobles pour chaque occasion.</small></div>
                <div><b>Identité</b><small>Le wax, le bazin et les savoir-faire africains au cœur de nos créations.</small></div>
                <div><b>Innovation</b><small>Des designs modernes, en mode comme en communication visuelle.</small></div>
            </div>
        </div>
        <div class="universes">
            <a href="{{ route('shop.femme') }}" class="universe">@include('partials.visual', ['key' => 'univers_femme', 'fallback' => 'art-femme-0'])<div class="u-txt"><small>Mode femme</small><b>Robes élégantes & tenues de soirée</b></div></a>
            <a href="{{ route('shop', ['africain' => 1]) }}" class="universe">@include('partials.visual', ['key' => 'univers_africain', 'fallback' => 'art-homme-0'])<div class="u-txt"><small>Homme & femme</small><b>Collections africaines</b></div></a>
            <a href="{{ route('design.create') }}" class="universe">@include('partials.visual', ['key' => 'univers_mesure', 'fallback' => 'art-homme-1'])<div class="u-txt"><small>Atelier</small><b>Création sur mesure</b></div></a>
            <a href="{{ route('graphic.create') }}" class="universe">@include('partials.visual', ['key' => 'univers_design', 'fallback' => 'art-studio'])<div class="u-txt"><small>Studio</small><b>Infographie & design graphique</b></div></a>
        </div>
    </div>
</section>

@if ($categories->isNotEmpty())
<section class="section">
    <div class="container">
        <div class="section-head">
            <div><span class="eyebrow gold">Nos univers</span><h2>Trouvez votre style</h2></div>
            <a href="{{ route('shop') }}" class="btn btn-ghost btn-sm">Toute la boutique</a>
        </div>
        <div class="cat-strip">
            @foreach ($categories as $i => $c)
                <a class="cat-tile" href="{{ route($c->gender === 'homme' ? 'shop.homme' : 'shop.femme', ['categorie' => $c->slug]) }}">
                    @include('partials.visual', ['src' => $c->imageUrl(), 'fallback' => 'art-'.$c->gender.'-'.($i % 3), 'alt' => $c->name])
                    <div style="position:relative;z-index:2"><small>{{ ucfirst($c->gender) }}</small><b>{{ $c->name }}</b></div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="section" style="padding-top:0">
    <div class="container" data-tabs>
        <div class="section-head">
            <div><span class="eyebrow gold">Sélection</span><h2>Les pièces du moment</h2></div>
        </div>
        <div class="tabs-inline" role="tablist">
            <button role="tab" data-tab="featured" aria-selected="true">Coups de cœur</button>
            <button role="tab" data-tab="femme" aria-selected="false">Femme</button>
            <button role="tab" data-tab="homme" aria-selected="false">Homme</button>
        </div>
        @foreach (['featured' => $featured, 'femme' => $newFemme, 'homme' => $newHomme] as $key => $list)
            <div data-panel="{{ $key }}" @if($key !== 'featured') hidden @endif>
                @if ($list->isEmpty())
                    <div class="empty-state"><h3>La collection arrive</h3><p>Les nouveaux articles seront bientôt en ligne.</p></div>
                @else
                    <div class="products">@foreach ($list as $product)@include('partials.product-card')@endforeach</div>
                @endif
            </div>
        @endforeach
    </div>
</section>

<section class="section atelier">
    <div class="container atelier-grid">
        @include('partials.visual', ['key' => 'atelier', 'fallback' => 'art-homme-1', 'alt' => 'Atelier'])
        <div>
            <span class="eyebrow gold">L'atelier WELLTAARÉ</span>
            <h2 style="margin-top:14px">Votre tenue,<br><em>dessinée pour vous.</em></h2>
            <p>Robe de soirée, tenue de mariage, grand boubou ou costume trois pièces en wax ou en bazin : décrivez votre idée, nous créons le design et le cousons à vos mesures.</p>
            <ol class="steps">
                <li><div><b>Vous décrivez votre idée</b><small>Modèle, tissu, occasion, mesures et photo d'inspiration.</small></div></li>
                <li><div><b>Nous dessinons et chiffrons</b><small>Croquis et devis envoyés par WhatsApp.</small></div></li>
                <li><div><b>L'atelier coud votre pièce</b><small>Après validation et acompte.</small></div></li>
                <li><div><b>Essayage et livraison</b><small>Retouches finales puis remise en main propre.</small></div></li>
            </ol>
            <a href="{{ route('design.create') }}" class="btn btn-light">Demander une création</a>
        </div>
    </div>
</section>

<section class="section studio">
    <div class="container studio-grid">
        <div>
            <span class="eyebrow gold">Studio graphique</span>
            <h2 style="font-size:clamp(2.2rem,4.6vw,3.6rem);margin-top:14px">Donnez à votre marque<br><em style="color:var(--gold)">l'image qu'elle mérite.</em></h2>
            <p style="color:var(--muted);margin-top:18px;max-width:48ch">Logos, identités visuelles, affiches, flyers, cartes de visite, bâches et visuels pour les réseaux sociaux : nous concevons des créations qui valorisent votre activité et la créativité africaine.</p>
            <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:28px"><a href="{{ route('graphic.create') }}" class="btn">Demander un devis</a></div>
        </div>
        <ul class="service-list">
            @foreach (\App\Models\GraphicRequest::SERVICES as $k => $label)
                @continue($k === 'autre')
                <li><span class="num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $label }}</li>
            @endforeach
        </ul>
    </div>
</section>

<section class="section quote-band">
    <div class="container">
        <blockquote>« Allier élégance, identité et innovation. »</blockquote>
        <cite>La vision du Complexe WELLTAARÉ Collection</cite>
    </div>
</section>
@endsection
