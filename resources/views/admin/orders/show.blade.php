@extends('layouts.admin')
@section('title', 'Commande '.$order->reference)

@section('content')
<div class="topbar">
    <div><a href="{{ route('admin.orders.index') }}" class="muted">← Commandes</a><h1 class="num" style="margin-top:6px">{{ $order->reference }}</h1><p>Passée le {{ $order->created_at->translatedFormat('d F Y à H:i') }}</p></div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        @if ($wa = wa_link($order->customer_phone, 'Bonjour '.$order->customer_name.', '.config('shop.name').' au sujet de votre commande '.$order->reference.'.'))<a class="btn btn-ghost" href="{{ $wa }}" target="_blank" rel="noopener">WhatsApp client</a>@endif
        <a class="btn btn-ghost" href="tel:{{ preg_replace('/[^\d+]/', '', $order->customer_phone) }}">Appeler</a>
    </div>
</div>

<div class="grid g-main" style="align-items:start">
    <div class="grid">
        <div class="card">
            <div class="card-h"><h2>Articles</h2><span class="pill p-{{ $order->status }}">{{ $order->statusLabel() }}</span></div>
            <div class="table-wrap"><table>
                <thead><tr><th>Article</th><th>Variante</th><th>Qté</th><th>Prix</th><th>Total</th></tr></thead>
                <tbody>
                @foreach ($order->items as $i)
                    <tr>
                        <td><div class="prod-cell"><div class="thumb">@if($i->imageUrl())<img src="{{ $i->imageUrl() }}" alt="">@endif</div><div>{{ $i->product_name }}@if($i->product)<div class="sub"><a href="{{ route('admin.products.edit', $i->product) }}">Fiche produit</a></div>@endif</div></div></td>
                        <td>{{ collect([$i->size, $i->color])->filter()->join(' · ') ?: '—' }}</td>
                        <td class="num">{{ $i->quantity }}</td>
                        <td class="num">{{ gnf($i->unit_price) }}</td>
                        <td class="num">{{ gnf($i->line_total) }}</td>
                    </tr>
                @endforeach
                <tr><td colspan="4" style="text-align:right" class="muted">Sous-total</td><td class="num">{{ gnf($order->subtotal) }}</td></tr>
                <tr><td colspan="4" style="text-align:right" class="muted">Livraison</td><td class="num">{{ gnf($order->delivery_fee) }}</td></tr>
                <tr><td colspan="4" style="text-align:right"><b>Total</b></td><td class="num"><b>{{ gnf($order->total) }}</b></td></tr>
                </tbody>
            </table></div>
        </div>
        <div class="grid g-2">
            <div class="card"><div class="card-h"><h2>Client</h2></div><div class="card-b"><dl class="dl">
                <dt>Nom</dt><dd>{{ $order->customer_name }}</dd>
                <dt>Téléphone</dt><dd class="num">{{ $order->customer_phone }}</dd>
                <dt>E-mail</dt><dd>{{ $order->customer_email ?: '—' }}</dd>
            </dl></div></div>
            <div class="card"><div class="card-h"><h2>Livraison</h2></div><div class="card-b"><dl class="dl">
                <dt>Ville</dt><dd>{{ $order->city }}</dd>
                <dt>Quartier</dt><dd>{{ $order->district }}</dd>
                <dt>Repère</dt><dd>{{ $order->address ?: '—' }}</dd>
                <dt>Message</dt><dd>{{ $order->note ?: '—' }}</dd>
            </dl></div></div>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <div class="card-h"><h2>Paiement</h2>@if($order->payment_method === 'orange_money')<span class="om-tag">Orange Money</span>@endif</div>
            <div class="card-b">
                <dl class="dl" style="grid-template-columns:110px 1fr">
                    <dt>Mode</dt><dd>{{ $order->paymentLabel() }}</dd>
                    <dt>Statut</dt><dd><span class="pill p-{{ $order->payment_status }}">{{ $order->paymentStatusLabel() }}</span></dd>
                    @if ($order->paid_at)<dt>Payé le</dt><dd>{{ $order->paid_at->format('d/m/Y H:i') }}</dd>@endif
                    @if ($order->om_txnid)<dt>Transaction</dt><dd class="num">{{ $order->om_txnid }}</dd>@endif
                </dl>
                @if ($order->payment_method === 'orange_money' && ! $order->isPaid() && $order->om_pay_token)
                    <form method="post" action="{{ route('admin.orders.verify', $order) }}" style="margin-top:14px">@csrf<button class="btn btn-om btn-sm">Vérifier le paiement auprès d'Orange</button></form>
                @endif
            </div>
        </div>
        <form class="card" method="post" action="{{ route('admin.orders.update', $order) }}">
            @csrf @method('put')
            <div class="card-h"><h2>Traitement</h2></div>
            <div class="card-b form">
                <div class="field"><label for="status">Statut de la commande</label><select id="status" name="status">@foreach (\App\Models\Order::STATUSES as $k => $l)<option value="{{ $k }}" @selected($order->status === $k)>{{ $l }}</option>@endforeach</select><span class="hint">« Annulée » remet les articles en stock.</span></div>
                <div class="field"><label for="payment_status">Statut du paiement</label><select id="payment_status" name="payment_status">@foreach (\App\Models\Order::PAYMENT_STATUSES as $k => $l)<option value="{{ $k }}" @selected($order->payment_status === $k)>{{ $l }}</option>@endforeach</select><span class="hint">Pour un paiement à la livraison, passez à « Payé » à la remise du colis.</span></div>
                <div class="field"><label for="admin_note">Note interne</label><textarea id="admin_note" name="admin_note" style="min-height:90px" placeholder="Visible uniquement par l'administration">{{ $order->admin_note }}</textarea></div>
                <button class="btn btn-accent">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
