@extends('layouts.admin')
@section('title', 'Espace développeur')

@section('content')
@php $fmt = fn ($b) => $b > 1048576 ? round($b / 1048576, 1).' Mo' : round($b / 1024).' Ko'; @endphp
<div class="topbar">
    <div><h1>Espace développeur</h1><p>Diagnostic du site, erreurs récentes et outils de dépannage.</p></div>
    <div style="display:flex;gap:8px;flex-wrap:wrap"><a class="btn btn-ghost" href="{{ route('admin.dev.logs') }}">Journal des erreurs</a><a class="btn btn-ghost" href="{{ route('admin.dev.database') }}">Base de données</a></div>
</div>

@if (app()->isDownForMaintenance())
    <div class="flash flash-info">Le site est <b>en maintenance</b> : les visiteurs voient la page « Site en maintenance ». Vous gardez l'accès grâce à votre lien secret{{ $maintenanceSecret ? ' : '.url('/'.$maintenanceSecret) : '' }}.</div>
@endif

<div class="grid g-4" style="margin-bottom:18px">
    <div class="card kpi {{ $errors24 ? 'alert' : '' }}"><span>Erreurs (24 h)</span><b class="num">{{ $errors24 }}</b><small><a href="{{ route('admin.dev.logs', ['niveau' => 'error']) }}" style="color:var(--accent)">Voir le journal →</a></small></div>
    <div class="card kpi"><span>Vérifications</span><b class="num">{{ collect($checks)->where('ok', true)->count() }}/{{ count($checks) }}</b><small class="muted">points au vert</small></div>
    <div class="card kpi"><span>Photos & fichiers</span><b class="num">{{ $fmt($storage) }}</b><small class="muted">storage/app/public</small></div>
    <div class="card kpi"><span>Journal</span><b class="num">{{ $fmt($logSize) }}</b><small class="muted">storage/logs/laravel.log</small></div>
</div>

<div class="grid g-main" style="align-items:start">
    <div class="grid">
        <div class="card">
            <div class="card-h"><h2>Santé du site</h2></div>
            @foreach ($checks as $c)
                <div class="check {{ $c['ok'] ? '' : 'ko' }}">
                    <span class="dot" aria-label="{{ $c['ok'] ? 'OK' : 'Problème' }}"></span>
                    <div>{{ $c['label'] }}<small>{{ $c['detail'] }}</small></div>
                    @if ($c['fix'])
                        <form method="post" action="{{ route('admin.dev.action') }}">@csrf<input type="hidden" name="action" value="{{ $c['fix'] }}"><button class="btn btn-sm">Corriger</button></form>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="card">
            <div class="card-h"><h2>Dernières erreurs</h2><a class="btn btn-ghost btn-sm" href="{{ route('admin.dev.logs') }}">Tout voir</a></div>
            @forelse ($lastErrors as $e)
                <details class="log">
                    <summary><span class="mono muted">{{ $e['date'] }}</span><span class="lv lv-{{ $e['level'] }}">{{ $e['level'] }}</span><span class="msg">{{ $e['message'] }}</span></summary>
                    @if ($e['trace'])<pre class="console">{{ $e['trace'] }}</pre>@endif
                </details>
            @empty
                <div class="empty">Aucune erreur enregistrée. Tout fonctionne.</div>
            @endforelse
        </div>

        <div class="card">
            <div class="card-h"><h2>Outils de dépannage</h2></div>
            <div class="card-b tools">
                @foreach ([
                    ['clear-cache', 'Vider les caches', 'À faire après une modification du code, du .env ou si une page affiche une ancienne version.', ''],
                    ['optimize', 'Optimiser (production)', 'Met en cache la configuration et les routes pour un site plus rapide.', ''],
                    ['storage-link', 'Réparer les photos', 'Recrée le lien public/storage si les images des produits ne s\'affichent pas.', ''],
                    ['make-dirs', 'Recréer les dossiers', 'Corrige l\'erreur « Please provide a valid cache path ».', ''],
                    ['migrate', 'Mettre à jour la base', 'Exécute les migrations en attente après une mise à jour du code.', ''],
                    ['test-om', 'Tester Orange Money', 'Vérifie que les identifiants Orange Money permettent d\'obtenir un jeton.', ''],
                ] as [$a, $t, $d])
                    <form class="tool" method="post" action="{{ route('admin.dev.action') }}">@csrf<input type="hidden" name="action" value="{{ $a }}"><b>{{ $t }}</b><p>{{ $d }}</p><button class="btn btn-ghost btn-sm">Lancer</button></form>
                @endforeach
                @if ($demoCount)
                    <form class="tool" method="post" action="{{ route('admin.dev.action') }}" onsubmit="return confirm('Supprimer les {{ $demoCount }} produits de démonstration ?')">@csrf<input type="hidden" name="action" value="remove-demo"><b>Supprimer les produits de démo</b><p>{{ $demoCount }} produit(s) d'exemple (référence DEMO-…) sont encore en ligne.</p><button class="btn btn-danger btn-sm">Supprimer</button></form>
                @endif
                @if (app()->isDownForMaintenance())
                    <form class="tool" method="post" action="{{ route('admin.dev.action') }}">@csrf<input type="hidden" name="action" value="maintenance-off"><b>Remettre le site en ligne</b><p>Les visiteurs retrouvent la boutique.</p><button class="btn btn-accent btn-sm">Remettre en ligne</button></form>
                @else
                    <form class="tool" method="post" action="{{ route('admin.dev.action') }}" onsubmit="return confirm('Mettre le site en maintenance ? Les clients ne pourront plus commander.')">@csrf<input type="hidden" name="action" value="maintenance-on"><b>Mode maintenance</b><p>Affiche « Site en maintenance » aux visiteurs pendant une correction. Vous gardez l'accès.</p><button class="btn btn-danger btn-sm">Activer</button></form>
                @endif
            </div>
        </div>
    </div>

    <div class="grid">
        <div class="card">
            <div class="card-h"><h2>Système</h2></div>
            <div class="card-b"><dl class="dl" style="grid-template-columns:130px 1fr">
                @foreach ($info as $k => $v)<dt>{{ $k }}</dt><dd class="{{ in_array($k, ['URL', 'Connexion (clients + admin)', 'Espace de gestion']) ? 'mono' : '' }}">{{ $v }}</dd>@endforeach
            </dl></div>
        </div>
        <div class="card">
            <div class="card-h"><h2>Tables</h2><a class="btn btn-ghost btn-sm" href="{{ route('admin.dev.database') }}">Explorer</a></div>
            @foreach ($counts as $t => $n)
                <a class="list-row" href="{{ route('admin.dev.database', $t) }}"><span class="mono">{{ $t }}</span><span class="muted num">{{ $n }}</span></a>
            @endforeach
        </div>
    </div>
</div>
@endsection
