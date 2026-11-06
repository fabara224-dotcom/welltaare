@extends('layouts.admin')
@section('title', 'Commandes')

@section('content')
<div class="topbar"><div><h1>Commandes</h1><p>Confirmez, préparez et suivez chaque commande jusqu'à la livraison.</p></div></div>

<div class="filters">
    <a class="chip {{ ! request('statut') && ! request('paiement') ? 'on' : '' }}" href="{{ route('admin.orders.index') }}">Toutes<span>{{ $counts->sum() }}</span></a>
    @foreach (\App\Models\Order::STATUSES as $k => $l)
        <a class="chip {{ request('statut') === $k ? 'on' : '' }}" href="{{ route('admin.orders.index', ['statut' => $k]) }}">{{ $l }}<span>{{ $counts[$k] ?? 0 }}</span></a>
    @endforeach
    <a class="chip {{ request('paiement') === 'paid' ? 'on' : '' }}" href="{{ route('admin.orders.index', ['paiement' => 'paid']) }}">Payées OM</a>
    <form class="search" method="get"><input type="search" name="q" value="{{ request('q') }}" placeholder="Référence, nom, téléphone"><button class="btn btn-ghost">Chercher</button></form>
</div>

<div class="card">
    @if ($orders->isEmpty())
        <div class="empty">Aucune commande{{ request()->hasAny(['statut', 'q', 'paiement']) ? ' pour ce filtre' : ' pour l\'instant' }}.</div>
    @else
    <div class="table-wrap"><table>
        <thead><tr><th>Commande</th><th>Client</th><th>Livraison</th><th>Articles</th><th>Total</th><th>Paiement</th><th>Statut</th></tr></thead>
        <tbody>
        @foreach ($orders as $o)
            <tr onclick="location='{{ route('admin.orders.show', $o) }}'" style="cursor:pointer">
                <td><b class="num">{{ $o->reference }}</b><div class="sub">{{ $o->created_at->format('d/m/Y H:i') }}</div></td>
                <td>{{ $o->customer_name }}<div class="sub num">{{ $o->customer_phone }}</div></td>
                <td>{{ $o->district }}<div class="sub">{{ $o->city }}</div></td>
                <td class="num">{{ $o->items_count }}</td>
                <td class="num"><b style="font-weight:500">{{ gnf($o->total) }}</b></td>
                <td>@if($o->payment_method === 'orange_money')<span class="om-tag">OM</span>@else<span class="sub">Espèces</span>@endif <span class="pill p-{{ $o->payment_status }}">{{ $o->paymentStatusLabel() }}</span></td>
                <td><span class="pill p-{{ $o->status }}">{{ $o->statusLabel() }}</span></td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
    {{ $orders->links() }}
    @endif
</div>
@endsection
