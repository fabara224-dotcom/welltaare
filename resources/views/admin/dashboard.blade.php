@extends('layouts.admin')
@section('title', 'Tableau de bord')

@section('content')
<div class="topbar">
    <div><h1>Bonjour {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first() }}</h1><p>{{ now()->translatedFormat('l d F Y') }} · voici l'activité de la boutique.</p></div>
    <div style="display:flex;gap:8px;flex-wrap:wrap"><a class="btn btn-ghost" href="{{ route('admin.orders.index', ['statut' => 'nouvelle']) }}">Commandes à traiter</a><a class="btn btn-accent" href="{{ route('admin.products.create') }}">+ Ajouter un produit</a></div>
</div>

<div class="grid g-4" style="margin-bottom:18px">
    <div class="card kpi {{ $stats['new_orders'] ? 'alert' : '' }}"><span>Nouvelles commandes</span><b class="num">{{ $stats['new_orders'] }}</b><small><a href="{{ route('admin.orders.index', ['statut' => 'nouvelle']) }}" style="color:var(--accent)">À confirmer →</a></small></div>
    <div class="card kpi"><span>Ventes du mois</span><b class="num">{{ number_format($stats['month_revenue'], 0, ',', ' ') }}</b><small class="muted">GNF · {{ $stats['month_orders'] }} commande(s)</small></div>
    <div class="card kpi"><span>Encaissé Orange Money (mois)</span><b class="num">{{ number_format($stats['paid_om'], 0, ',', ' ') }}</b><small class="muted">GNF payés en ligne</small></div>
    <div class="card kpi {{ $stats['designs'] ? 'alert' : '' }}"><span>Demandes sur mesure & design</span><b class="num">{{ $stats['designs'] }}</b><small><a href="{{ route('admin.designs.index', ['statut' => 'nouvelle']) }}" style="color:var(--accent)">À étudier →</a></small></div>
</div>

<div class="grid g-main">
    <div class="grid">
        <div class="card">
            <div class="card-h"><h2>Ventes des 14 derniers jours</h2><span class="muted num" style="font-size:.85rem">{{ gnf($chart->sum('value')) }}</span></div>
            <div class="card-b">
                @php $max = max(1, $chart->max('value')); @endphp
                <div class="chart">
                    @foreach ($chart as $d)
                        <div class="bar {{ $d['value'] ? '' : 'zero' }}" title="{{ $d['label'] }} : {{ gnf($d['value']) }}"><i style="height:{{ max(1, round($d['value'] / $max * 100)) }}%"></i><small>{{ $d['day'] }}</small></div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-h"><h2>Dernières commandes</h2><a class="btn btn-ghost btn-sm" href="{{ route('admin.orders.index') }}">Tout voir</a></div>
            @if ($recent->isEmpty())
                <div class="empty">Aucune commande pour l'instant. Elles apparaîtront ici dès le premier achat.</div>
            @else
            <div class="table-wrap"><table>
                <thead><tr><th>Commande</th><th>Client</th><th>Total</th><th>Paiement</th><th>Statut</th></tr></thead>
                <tbody>
                @foreach ($recent as $o)
                    <tr onclick="location='{{ route('admin.orders.show', $o) }}'" style="cursor:pointer">
                        <td><b class="num">{{ $o->reference }}</b><div class="sub">{{ $o->created_at->diffForHumans() }}</div></td>
                        <td>{{ $o->customer_name }}<div class="sub num">{{ $o->customer_phone }}</div></td>
                        <td class="num">{{ gnf($o->total) }}</td>
                        <td>@if($o->payment_method === 'orange_money')<span class="om-tag">OM</span>@endif <span class="pill p-{{ $o->payment_status }}">{{ $o->paymentStatusLabel() }}</span></td>
                        <td><span class="pill p-{{ $o->status }}">{{ $o->statusLabel() }}</span></td>
                    </tr>
                @endforeach
                </tbody>
            </table></div>
            @endif
        </div>
    </div>
    <div class="grid" style="align-content:start">
        <div class="card">
            <div class="card-h"><h2>Meilleures ventes</h2></div>
            @forelse ($top as $t)
                <div class="list-row"><span>{{ $t->product_name }}</span><span class="muted num">{{ $t->qty }} vendu(s)</span></div>
            @empty
                <div class="empty">Pas encore de ventes.</div>
            @endforelse
        </div>
        <div class="card">
            <div class="card-h"><h2>Stock faible</h2><a class="btn btn-ghost btn-sm" href="{{ route('admin.products.index', ['etat' => 'stock']) }}">Voir</a></div>
            @forelse ($lowStock as $p)
                <div class="list-row"><a href="{{ route('admin.products.edit', $p) }}">{{ $p->name }}</a><span class="pill {{ $p->stock <= 0 ? 'p-failed' : 'p-pending' }}">{{ $p->stock <= 0 ? 'Épuisé' : $p->stock.' restant(s)' }}</span></div>
            @empty
                <div class="empty">Tous les stocks sont bons.</div>
            @endforelse
        </div>
        <div class="card">
            <div class="card-h"><h2>Demandes sur mesure</h2></div>
            @forelse ($designs as $d)
                <a class="list-row" href="{{ route('admin.designs.show', $d) }}"><span>{{ $d->garment_type }}<br><small class="muted">{{ $d->customer_name }}</small></span><span class="pill p-{{ $d->status }}">{{ $d->statusLabel() }}</span></a>
            @empty
                <div class="empty">Aucune demande.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
