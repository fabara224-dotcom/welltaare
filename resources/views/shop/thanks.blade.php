@extends('layouts.shop')
@section('title', 'Commande '.$order->reference)

@section('content')
@php
    $steps = ['nouvelle' => 'Reçue', 'confirmee' => 'Confirmée', 'preparation' => 'Préparation', 'expediee' => 'En livraison', 'livree' => 'Livrée'];
    $idx = array_search($order->status, array_keys($steps));
@endphp
<div class="container">
    <div class="thanks">
        <span class="eyebrow">Étape 3 sur 3</span>
        @if ($order->isPaid())
            <h1>Merci, {{ \Illuminate\Support\Str::of($order->customer_name)->explode(' ')->first() }} !</h1>
            <div class="alert alert-ok">Paiement Orange Money reçu. Votre commande est confirmée.</div>
        @elseif ($order->payment_method === 'orange_money')
            <h1>Commande enregistrée</h1>
            @if (session('payment_error'))<div class="alert alert-err">{{ session('payment_error') }}</div>@endif
            @if ($order->payment_status === 'failed')<div class="alert alert-err">Le paiement n'a pas abouti.</div>@endif
            <div class="alert alert-info">Votre paiement Orange Money est en attente. La commande sera préparée dès sa réception.</div>
            @if ($order->needsOnlinePayment())
                <a class="btn btn-om" href="{{ route('payment.om.pay', $order) }}">Payer {{ gnf($order->total) }} avec Orange Money</a>
            @endif
        @else
            <h1>Merci, {{ \Illuminate\Support\Str::of($order->customer_name)->explode(' ')->first() }} !</h1>
            <p>Votre commande est enregistrée. Nous vous appelons au <b class="num">{{ $order->customer_phone }}</b> pour confirmer la livraison. Vous paierez à la réception.</p>
        @endif

        @include('partials.whatsapp-send', ['record' => $order])

        <div class="ref-box">
            <div><small>Numéro de commande</small><b class="num">{{ $order->reference }}</b></div>
            <div><small>Total</small><b class="num">{{ gnf($order->total) }}</b></div>
            <div><small>Paiement</small><b style="font-size:1rem;font-family:var(--f-body)">{{ $order->paymentLabel() }} · <span class="status-pill st-{{ $order->payment_status }}">{{ $order->paymentStatusLabel() }}</span></b></div>
        </div>

        @if ($order->status !== 'annulee')
        <div class="timeline">
            @foreach ($steps as $k => $label)<div class="{{ $idx !== false && $loop->index <= $idx ? 'done' : '' }}">{{ $label }}</div>@endforeach
        </div>
        @endif

        <div class="summary" style="position:static">
            @foreach ($order->items as $item)
                <div class="mini-line">
                    <div class="thumb"><div class="art {{ $item->image_path ? 'has-img' : 'art-femme-1' }}">@if($item->imageUrl())<img src="{{ $item->imageUrl() }}" alt="">@endif</div><span class="q">{{ $item->quantity }}</span></div>
                    <div>{{ $item->product_name }}<br><small style="color:var(--muted)">{{ collect([$item->size, $item->color])->filter()->join(' · ') }}</small></div>
                    <span class="num">{{ gnf($item->line_total) }}</span>
                </div>
            @endforeach
            <div class="sum-row" style="border-top:1px solid var(--line);padding-top:14px"><span>Livraison · {{ $order->district }}, {{ $order->city }}</span><span class="num">{{ $order->delivery_fee ? gnf($order->delivery_fee) : 'Offerte' }}</span></div>
            <div class="sum-row total"><span>Total</span><span class="num">{{ gnf($order->total) }}</span></div>
        </div>

        <p style="color:var(--muted)">Gardez votre numéro de commande : il vous permet de <a class="link" href="{{ route('tracking') }}">suivre votre commande</a> à tout moment avec votre téléphone.</p>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <a class="btn" href="{{ route('shop') }}">Continuer mes achats</a>
            @auth<a class="btn btn-ghost" href="{{ route('account') }}">Mes commandes</a>@else<a class="btn btn-ghost" href="{{ route('register') }}">Créer un compte pour suivre mes commandes</a>@endauth
        </div>
    </div>
</div>
@endsection
