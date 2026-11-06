@extends('layouts.admin')
@section('title', 'Demande '.$req->reference)

@section('content')
<div class="topbar">
    <div><a href="{{ route('admin.graphics.index') }}" class="muted">← Infographie</a><h1 class="num" style="margin-top:6px">{{ $req->reference }}</h1><p>Reçue le {{ $req->created_at->translatedFormat('d F Y à H:i') }}</p></div>
    @if ($wa = wa_link($req->customer_phone, 'Bonjour '.$req->customer_name.', '.config('shop.name').' au sujet de votre projet graphique '.$req->reference.'.'))<a class="btn btn-ghost" href="{{ $wa }}" target="_blank" rel="noopener">Envoyer la maquette sur WhatsApp</a>@endif
</div>
<div class="grid g-main" style="align-items:start">
    <div class="grid">
        <div class="card">
            <div class="card-h"><h2>{{ $req->serviceLabel() }}</h2><span class="pill p-{{ $req->status }}">{{ $req->statusLabel() }}</span></div>
            <div class="card-b grid" style="gap:16px">
                <dl class="dl">
                    <dt>Budget</dt><dd class="num">{{ $req->budget ? gnf($req->budget) : '—' }}</dd>
                    <dt>Pour le</dt><dd>{{ $req->deadline?->translatedFormat('d F Y') ?? '—' }}</dd>
                </dl>
                <div><b style="font-weight:600;font-size:.85rem">Description</b><p style="white-space:pre-line;margin:6px 0 0">{{ $req->description }}</p></div>
            </div>
        </div>
        @if ($req->attachmentUrl())
            <div class="card"><div class="card-h"><h2>Fichier joint</h2><a class="btn btn-ghost btn-sm" href="{{ $req->attachmentUrl() }}" target="_blank">Ouvrir</a></div>
                @unless (str_ends_with($req->attachment_path, '.pdf'))<div class="card-b"><img src="{{ $req->attachmentUrl() }}" alt="Fichier joint" style="max-height:480px;border-radius:8px"></div>@endunless
            </div>
        @endif
    </div>
    <div class="grid">
        <div class="card"><div class="card-h"><h2>Client</h2></div><div class="card-b"><dl class="dl" style="grid-template-columns:90px 1fr">
            <dt>Nom</dt><dd>{{ $req->customer_name }}</dd>
            <dt>Entreprise</dt><dd>{{ $req->company ?: '—' }}</dd>
            <dt>Téléphone</dt><dd class="num">{{ $req->customer_phone }}</dd>
            <dt>E-mail</dt><dd>{{ $req->customer_email ?: '—' }}</dd>
        </dl></div></div>
        <form class="card" method="post" action="{{ route('admin.graphics.update', $req) }}">
            @csrf @method('put')
            <div class="card-h"><h2>Suivi</h2></div>
            <div class="card-b form">
                <div class="field"><label for="status">Statut</label><select id="status" name="status">@foreach (\App\Models\GraphicRequest::STATUSES as $k => $l)<option value="{{ $k }}" @selected($req->status === $k)>{{ $l }}</option>@endforeach</select></div>
                <div class="field"><label for="quote_amount">Montant du devis (GNF)</label><input id="quote_amount" name="quote_amount" value="{{ $req->quote_amount }}" inputmode="numeric"></div>
                <div class="field"><label for="admin_note">Note interne</label><textarea id="admin_note" name="admin_note" style="min-height:90px">{{ $req->admin_note }}</textarea></div>
                <button class="btn btn-accent">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
