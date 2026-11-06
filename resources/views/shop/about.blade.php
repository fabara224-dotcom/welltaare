@extends('layouts.shop')
@section('title', 'Le Complexe')
@section('description', 'COMPLEXE WELLTAARÉ COLLECTION : un univers dédié à la mode, à la créativité et au design. Notre vision : allier élégance, identité et innovation.')

@section('content')
<section class="page-hero">
    <div class="container">
        <nav class="breadcrumb"><a href="{{ route('home') }}">Accueil</a> / <span>Le Complexe</span></nav>
        <span class="eyebrow gold" style="display:block;margin-top:18px">COMPLEXE WELLTAARÉ COLLECTION</span>
        <h1 style="max-width:18ch">Un univers dédié à la mode, à la créativité et au design.</h1>
    </div>
</section>

<section class="section">
    <div class="container complexe-grid" style="align-items:start">
        @include('partials.visual', ['key' => 'complexe', 'fallback' => 'art-femme-2', 'style' => 'aspect-ratio:4/5', 'label' => 'Tradition<br>& modernité', 'labelStyle' => 'font-size:1.8rem'])
        <div class="complexe-text" style="gap:20px">
            <p>COMPLEXE WELLTAARÉ COLLECTION est un univers dédié à la mode, à la créativité et au design.</p>
            <p>Nous proposons des robes élégantes pour femmes, des tenues de soirée ainsi que des collections de vêtements africains pour hommes et femmes, alliant tradition et modernité.</p>
            <p>Nous sommes également spécialisés dans l'infographie et le design graphique, avec pour ambition de valoriser les marques et la créativité africaine.</p>
            <div class="vision" style="margin-top:12px">
                <span class="eyebrow">Notre vision</span>
                <p style="font-family:var(--f-display);font-size:clamp(1.8rem,3.4vw,2.6rem);line-height:1.15;margin-top:12px">Allier <em style="color:var(--gold)">élégance</em>, <em style="color:var(--gold)">identité</em> et <em style="color:var(--gold)">innovation</em>.</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="padding-top:0">
    <div class="container">
        <div class="section-head"><div><span class="eyebrow gold">Nos pôles</span><h2>Ce que nous faisons</h2></div></div>
        <div class="pillars">
            <div><h3>Robes élégantes & soirée</h3><p>Robes de cérémonie, tenues de soirée et pièces audacieuses pour sublimer la femme.</p><p style="margin-top:16px"><a class="link" href="{{ route('shop.femme') }}">Collection Femme →</a></p></div>
            <div><h3>Mode africaine homme & femme</h3><p>Costumes en wax, grands boubous en bazin, ensembles en pagne : nos savoir-faire revisités avec modernité.</p><p style="margin-top:16px"><a class="link" href="{{ route('shop', ['africain' => 1]) }}">Collections africaines →</a></p></div>
            <div><h3>Création sur mesure</h3><p>Votre idée dessinée puis cousue à vos mesures par notre atelier, pour vos grands moments.</p><p style="margin-top:16px"><a class="link" href="{{ route('design.create') }}">Demander une création →</a></p></div>
            <div><h3>Infographie & design graphique</h3><p>Logos, identités visuelles, affiches, flyers, cartes de visite et visuels digitaux pour valoriser les marques africaines.</p><p style="margin-top:16px"><a class="link" href="{{ route('graphic.create') }}">Demander un devis →</a></p></div>
        </div>
    </div>
</section>
@endsection
