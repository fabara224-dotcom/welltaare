@extends('layouts.admin')
@section('title', 'Journal des erreurs')

@section('content')
<div class="topbar">
    <div><a href="{{ route('admin.dev.index') }}" class="muted">← Espace développeur</a><h1 style="margin-top:6px">Journal des erreurs</h1><p>{{ $total }} entrée(s){{ $total > 200 ? ', 200 plus récentes affichées' : '' }}. Cliquez sur une ligne pour voir le détail technique.</p></div>
    <form method="post" action="{{ route('admin.dev.logs.clear') }}" onsubmit="return confirm('Vider tout le journal ?')">@csrf @method('delete')<button class="btn btn-danger">Vider le journal</button></form>
</div>
<div class="filters">
    <a class="chip {{ ! request('niveau') ? 'on' : '' }}" href="{{ route('admin.dev.logs', request()->only('q')) }}">Tout</a>
    @foreach ($levels as $l)
        <a class="chip {{ strtoupper(request('niveau')) === $l ? 'on' : '' }}" href="{{ route('admin.dev.logs', ['niveau' => strtolower($l)] + request()->only('q')) }}">{{ $l }}</a>
    @endforeach
    <form class="search" method="get">@if(request('niveau'))<input type="hidden" name="niveau" value="{{ request('niveau') }}">@endif<input type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher dans les messages"><button class="btn btn-ghost">Chercher</button></form>
</div>
<div class="card">
    @forelse ($entries as $e)
        <details class="log">
            <summary><span class="mono muted">{{ $e['date'] }}</span><span class="lv lv-{{ $e['level'] }}">{{ $e['level'] }}</span><span class="msg">{{ $e['message'] }}</span></summary>
            @if ($e['trace'])<pre class="console">{{ $e['trace'] }}</pre>@else<p class="muted" style="margin:0 18px 14px;font-size:.85rem">Pas de détail supplémentaire.</p>@endif
        </details>
    @empty
        <div class="empty">Aucune entrée{{ request()->hasAny(['niveau', 'q']) ? ' pour ce filtre' : '' }}.</div>
    @endforelse
</div>
@endsection
