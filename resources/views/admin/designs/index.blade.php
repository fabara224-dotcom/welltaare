@extends('layouts.admin')
@section('title', 'Créations sur mesure')

@section('content')
<div class="topbar"><div><h1>Créations sur mesure</h1><p>Étudiez chaque demande, envoyez le devis et suivez la couture.</p></div></div>
<div class="filters">
    <a class="chip {{ ! request('statut') ? 'on' : '' }}" href="{{ route('admin.designs.index') }}">Toutes<span>{{ $counts->sum() }}</span></a>
    @foreach (\App\Models\DesignRequest::STATUSES as $k => $l)
        <a class="chip {{ request('statut') === $k ? 'on' : '' }}" href="{{ route('admin.designs.index', ['statut' => $k]) }}">{{ $l }}<span>{{ $counts[$k] ?? 0 }}</span></a>
    @endforeach
</div>
<div class="card">
    @if ($designs->isEmpty())
        <div class="empty">Aucune demande pour l'instant.</div>
    @else
    <div class="table-wrap"><table>
        <thead><tr><th>Demande</th><th>Client</th><th>Tenue</th><th>Pour le</th><th>Budget / devis</th><th>Statut</th></tr></thead>
        <tbody>
        @foreach ($designs as $d)
            <tr onclick="location='{{ route('admin.designs.show', $d) }}'" style="cursor:pointer">
                <td><b class="num">{{ $d->reference }}</b><div class="sub">{{ $d->created_at->format('d/m/Y') }}</div></td>
                <td>{{ $d->customer_name }}<div class="sub num">{{ $d->customer_phone }}</div></td>
                <td>{{ $d->garment_type }}<div class="sub">{{ ucfirst($d->gender) }}{{ $d->occasion ? ' · '.$d->occasion : '' }}</div></td>
                <td>{{ $d->deadline?->format('d/m/Y') ?? '—' }}</td>
                <td class="num">{{ $d->quote_amount ? gnf($d->quote_amount) : ($d->budget ? '≈ '.gnf($d->budget) : '—') }}</td>
                <td><span class="pill p-{{ $d->status }}">{{ $d->statusLabel() }}</span></td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
    {{ $designs->links() }}
    @endif
</div>
@endsection
