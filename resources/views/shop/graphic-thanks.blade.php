@extends('layouts.shop')
@section('title', 'Demande de devis envoyée')

@section('content')
<div class="container">
    <div class="thanks">
        <span class="eyebrow gold">Infographie & design graphique</span>
        <h1>Merci, votre projet est bien reçu.</h1>
        <p>Notre studio étudie votre demande et vous contacte au <b class="num">{{ $req->customer_phone }}</b> avec une proposition et un devis.</p>
        @include('partials.whatsapp-send', ['record' => $req])
        <div class="ref-box">
            <div><small>Numéro de demande</small><b class="num">{{ $req->reference }}</b></div>
            <div><small>Prestation</small><b style="font-size:1.05rem">{{ $req->serviceLabel() }}</b></div>
            <div><small>Statut</small><span class="status-pill st-{{ $req->status }}">{{ $req->statusLabel() }}</span></div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <a class="btn" href="{{ route('home') }}">Retour à l'accueil</a>

        </div>
    </div>
</div>
@endsection
