@extends('layouts.shop')
@section('title', 'Suivre ma commande')

@section('content')
<section class="page-hero"><div class="container"><h1>Suivre ma commande</h1><p>Entrez votre numéro de commande (WT-…), de demande sur mesure (SM-…) ou de design graphique (GD-…) et votre téléphone.</p></div></section>
<div class="container">
    <div class="thanks" style="padding-top:36px">
        <form method="post" action="{{ route('tracking.lookup') }}" class="summary" style="position:static" novalidate>
            @csrf
            @error('reference')<div class="alert alert-err">{{ $message }}</div>@enderror
            <div class="grid-2">
                <div class="field"><label for="reference">Numéro</label><input id="reference" name="reference" value="{{ old('reference', request('ref')) }}" placeholder="WT-261024-AB12" style="text-transform:uppercase"></div>
                <div class="field"><label for="phone">Téléphone utilisé</label><input id="phone" name="phone" value="{{ old('phone') }}" inputmode="tel"></div>
            </div>
            <button class="btn">Voir le statut</button>
        </form>

        @isset($result)
            <div class="ref-box">
                <div><small>{{ $result instanceof \App\Models\Order ? 'Commande' : ($result instanceof \App\Models\GraphicRequest ? 'Infographie & design' : 'Création sur mesure') }}</small><b class="num">{{ $result->reference }}</b></div>
                <div><small>Statut</small><span class="status-pill st-{{ $result->status }}">{{ $result->statusLabel() }}</span></div>
                @if ($result instanceof \App\Models\Order)
                    <div><small>Paiement</small><span class="status-pill st-{{ $result->payment_status }}">{{ $result->paymentStatusLabel() }}</span></div>
                    <div><small>Total</small><b class="num">{{ gnf($result->total) }}</b></div>
                @elseif ($result->quote_amount)
                    <div><small>Devis</small><b class="num">{{ gnf($result->quote_amount) }}</b></div>
                @endif
            </div>
        @endisset

        @if ($mine->isNotEmpty())
            <div>
                <h2 style="font-size:1.6rem;margin-bottom:14px">Commandes passées sur cet appareil</h2>
                <div class="cart-lines">
                    @foreach ($mine as $o)
                        <div class="cart-line" style="grid-template-columns:1fr auto">
                            <div><h3 class="num">{{ $o->reference }}</h3><div class="sub">{{ $o->created_at->translatedFormat('d F Y') }} · {{ gnf($o->total) }}</div></div>
                            <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap"><span class="status-pill st-{{ $o->status }}">{{ $o->statusLabel() }}</span><a class="link" href="{{ route('checkout.thanks', $o) }}">Détail</a></div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
