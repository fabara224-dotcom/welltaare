@extends('layouts.admin')
@section('title', 'Demande '.$design->reference)

@section('content')
<div class="topbar">
    <div><a href="{{ route('admin.designs.index') }}" class="muted">← Sur mesure</a><h1 class="num" style="margin-top:6px">{{ $design->reference }}</h1><p>Reçue le {{ $design->created_at->translatedFormat('d F Y à H:i') }}</p></div>
    @if ($wa = wa_link($design->customer_phone, 'Bonjour '.$design->customer_name.', '.config('shop.name').' au sujet de votre demande sur mesure '.$design->reference.'.'))<a class="btn btn-ghost" href="{{ $wa }}" target="_blank" rel="noopener">Envoyer le croquis sur WhatsApp</a>@endif
</div>
<div class="grid g-main" style="align-items:start">
    <div class="grid">
        <div class="card">
            <div class="card-h"><h2>{{ $design->garment_type }}</h2><span class="pill p-{{ $design->status }}">{{ $design->statusLabel() }}</span></div>
            <div class="card-b grid" style="gap:16px">
                <dl class="dl">
                    <dt>Pour</dt><dd>{{ ucfirst($design->gender) }}</dd>
                    <dt>Occasion</dt><dd>{{ $design->occasion ?: '—' }}</dd>
                    <dt>Tissu</dt><dd>{{ $design->fabric ?: '—' }}</dd>
                    <dt>Budget</dt><dd class="num">{{ $design->budget ? gnf($design->budget) : '—' }}</dd>
                    <dt>Pour le</dt><dd>{{ $design->deadline?->translatedFormat('d F Y') ?? '—' }}</dd>
                </dl>
                <div><b style="font-weight:600;font-size:.85rem">Description</b><p style="white-space:pre-line;margin:6px 0 0">{{ $design->description }}</p></div>
            </div>
        </div>
        <div class="card">
            <div class="card-h"><h2>Mesures (cm)</h2></div>
            <div class="card-b">
                @if ($design->measurements)
                    <dl class="dl">@foreach ($design->measurements as $k => $v)<dt>{{ \App\Models\DesignRequest::MEASUREMENTS[$k] ?? $k }}</dt><dd class="num">{{ $v }} cm</dd>@endforeach</dl>
                @else
                    <p class="muted" style="margin:0">Aucune mesure fournie : à prendre en boutique.</p>
                @endif
            </div>
        </div>
        @if ($design->inspirationUrl())
            <div class="card"><div class="card-h"><h2>Photo d'inspiration</h2><a class="btn btn-ghost btn-sm" href="{{ $design->inspirationUrl() }}" target="_blank">Ouvrir</a></div><div class="card-b"><img src="{{ $design->inspirationUrl() }}" alt="Inspiration" style="max-height:480px;border-radius:8px"></div></div>
        @endif
    </div>
    <div class="grid">
        <div class="card"><div class="card-h"><h2>Client</h2></div><div class="card-b"><dl class="dl" style="grid-template-columns:90px 1fr">
            <dt>Nom</dt><dd>{{ $design->customer_name }}</dd>
            <dt>Téléphone</dt><dd class="num">{{ $design->customer_phone }}</dd>
            <dt>E-mail</dt><dd>{{ $design->customer_email ?: '—' }}</dd>
        </dl></div></div>
        <form class="card" method="post" action="{{ route('admin.designs.update', $design) }}">
            @csrf @method('put')
            <div class="card-h"><h2>Suivi</h2></div>
            <div class="card-b form">
                <div class="field"><label for="status">Statut</label><select id="status" name="status">@foreach (\App\Models\DesignRequest::STATUSES as $k => $l)<option value="{{ $k }}" @selected($design->status === $k)>{{ $l }}</option>@endforeach</select></div>
                <div class="field"><label for="quote_amount">Montant du devis (GNF)</label><input id="quote_amount" name="quote_amount" value="{{ $design->quote_amount }}" inputmode="numeric"><span class="hint">Le client le voit dans le suivi de sa demande.</span></div>
                <div class="field"><label for="admin_note">Note interne</label><textarea id="admin_note" name="admin_note" style="min-height:90px">{{ $design->admin_note }}</textarea></div>
                <button class="btn btn-accent">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
