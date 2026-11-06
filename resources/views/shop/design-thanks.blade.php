@extends('layouts.shop')
@section('title', 'Demande envoyée')

@section('content')
<div class="container">
    <div class="thanks">
        <span class="eyebrow gold">Création sur mesure</span>
        <h1>Votre demande est entre de bonnes mains.</h1>
        <p>Notre atelier étudie votre idée. Nous vous contactons au <b class="num">{{ $design->customer_phone }}</b> sous 48 h avec un croquis et un devis.</p>
        @include('partials.whatsapp-send', ['record' => $design])
        <div class="ref-box">
            <div><small>Numéro de demande</small><b class="num">{{ $design->reference }}</b></div>
            <div><small>Tenue</small><b style="font-size:1.05rem">{{ $design->garment_type }}</b></div>
            <div><small>Statut</small><span class="status-pill st-{{ $design->status }}">{{ $design->statusLabel() }}</span></div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <a class="btn" href="{{ route('shop') }}">Voir la boutique</a>

        </div>
    </div>
</div>
@endsection
