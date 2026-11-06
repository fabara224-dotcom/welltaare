@extends('layouts.admin')
@section('title', 'Base de données')

@section('content')
<div class="topbar"><div><a href="{{ route('admin.dev.index') }}" class="muted">← Espace développeur</a><h1 style="margin-top:6px">Base de données</h1><p>Consultation en lecture seule. Les mots de passe et jetons de paiement sont masqués.</p></div></div>
<div class="filters">
    @foreach ($tables as $t => $n)
        <a class="chip {{ $table === $t ? 'on' : '' }}" href="{{ route('admin.dev.database', $t) }}"><span class="mono" style="opacity:1;margin:0">{{ $t }}</span><span>{{ $n }}</span></a>
    @endforeach
</div>
@if ($table)
    <div class="filters"><form class="search" method="get" style="margin-left:0"><input type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher dans {{ $table }}"><button class="btn btn-ghost">Chercher</button></form></div>
    <div class="card">
        @if ($rows->isEmpty())
            <div class="empty">Table vide{{ request('q') ? ' pour cette recherche' : '' }}.</div>
        @else
            <div class="table-wrap"><table>
                <thead><tr>@foreach ($columns as $c)<th class="mono" style="text-transform:none;letter-spacing:0">{{ $c }}</th>@endforeach</tr></thead>
                <tbody>
                @foreach ($rows as $r)
                    <tr>@foreach ($columns as $c)<td class="db-cell mono" title="{{ \Illuminate\Support\Str::limit((string) $r->$c, 500) }}">{{ $r->$c === null ? '∅' : \Illuminate\Support\Str::limit((string) $r->$c, 80) }}</td>@endforeach</tr>
                @endforeach
                </tbody>
            </table></div>
            {{ $rows->links() }}
        @endif
    </div>
@else
    <div class="card"><div class="empty">Choisissez une table ci-dessus.</div></div>
@endif
@endsection
