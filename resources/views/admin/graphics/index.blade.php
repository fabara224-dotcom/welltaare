@extends('layouts.admin')
@section('title', 'Infographie')

@section('content')
<div class="topbar"><div><h1>Infographie & design</h1><p>Demandes de logos, affiches, flyers, cartes de visite et visuels.</p></div></div>
<div class="filters">
    <a class="chip {{ ! request('statut') ? 'on' : '' }}" href="{{ route('admin.graphics.index') }}">Toutes<span>{{ $counts->sum() }}</span></a>
    @foreach (\App\Models\GraphicRequest::STATUSES as $k => $l)
        <a class="chip {{ request('statut') === $k ? 'on' : '' }}" href="{{ route('admin.graphics.index', ['statut' => $k]) }}">{{ $l }}<span>{{ $counts[$k] ?? 0 }}</span></a>
    @endforeach
</div>
<div class="card">
    @if ($items->isEmpty())
        <div class="empty">Aucune demande pour l'instant. Elles arrivent depuis la page « Design graphique » du site.</div>
    @else
    <div class="table-wrap"><table>
        <thead><tr><th>Demande</th><th>Client</th><th>Prestation</th><th>Pour le</th><th>Budget / devis</th><th>Statut</th></tr></thead>
        <tbody>
        @foreach ($items as $g)
            <tr onclick="location='{{ route('admin.graphics.show', $g) }}'" style="cursor:pointer">
                <td><b class="num">{{ $g->reference }}</b><div class="sub">{{ $g->created_at->format('d/m/Y') }}</div></td>
                <td>{{ $g->customer_name }}<div class="sub">{{ $g->company ?: $g->customer_phone }}</div></td>
                <td>{{ $g->serviceLabel() }}</td>
                <td>{{ $g->deadline?->format('d/m/Y') ?? '—' }}</td>
                <td class="num">{{ $g->quote_amount ? gnf($g->quote_amount) : ($g->budget ? '≈ '.gnf($g->budget) : '—') }}</td>
                <td><span class="pill p-{{ $g->status }}">{{ $g->statusLabel() }}</span></td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
    {{ $items->links() }}
    @endif
</div>
@endsection
