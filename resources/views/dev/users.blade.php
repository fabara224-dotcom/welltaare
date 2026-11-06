@extends('layouts.admin')
@section('title', 'Comptes & accès')

@section('content')
<div class="topbar"><div><a href="{{ route('admin.dev.index') }}" class="muted">← Espace développeur</a><h1 style="margin-top:6px">Comptes & accès</h1><p>Un <b>administrateur</b> gère la boutique (produits, commandes, paramètres). Un <b>développeur</b> a en plus accès à cet espace.</p></div></div>
<div class="grid g-main" style="align-items:start">
    <div class="card">
        <div class="table-wrap"><table>
            <thead><tr><th>Compte</th><th>Rôle & accès</th><th>Dernière connexion</th><th></th></tr></thead>
            <tbody>
            @foreach ($users as $u)
                <tr>
                    <td colspan="2">
                        <form method="post" action="{{ route('admin.dev.users.update', $u) }}" id="u{{ $u->id }}" style="display:grid;grid-template-columns:1.2fr 1.2fr 1fr auto;gap:8px;align-items:center" class="field">
                            @csrf @method('put')
                            <div><input name="name" value="{{ $u->name }}" aria-label="Nom"><div class="sub muted" style="font-size:.8rem;margin-top:3px">{{ $u->email }}{{ $u->is(auth()->user()) ? ' · vous' : '' }}</div></div>
                            <div><select name="role" aria-label="Rôle">@foreach (\App\Models\User::ROLES as $k => $l)<option value="{{ $k }}" @selected($u->role === $k)>{{ $l }}</option>@endforeach</select>
                                <label style="display:flex;gap:6px;align-items:center;font-size:.8rem;margin-top:4px;font-weight:400"><input type="checkbox" name="is_admin" value="1" @checked($u->is_admin) @disabled($u->is(auth()->user()))> Accès autorisé</label></div>
                            <input type="password" name="password" placeholder="Nouveau mot de passe" aria-label="Nouveau mot de passe" autocomplete="new-password">
                            <button class="btn btn-ghost btn-sm">Enregistrer</button>
                        </form>
                    </td>
                    <td class="muted" style="font-size:.84rem">{{ $u->last_login_at?->diffForHumans() ?? 'Jamais' }}</td>
                    <td style="text-align:right">@unless ($u->is(auth()->user()))<form method="post" action="{{ route('admin.dev.users.destroy', $u) }}" onsubmit="return confirm('Supprimer le compte {{ $u->email }} ?')">@csrf @method('delete')<button class="btn btn-danger btn-sm">Supprimer</button></form>@endunless</td>
                </tr>
            @endforeach
            </tbody>
        </table></div>
    </div>
    <form class="card" method="post" action="{{ route('admin.dev.users.store') }}">
        @csrf
        <div class="card-h"><h2>Nouveau compte</h2></div>
        <div class="card-b form">
            <div class="field"><label for="nname">Nom</label><input id="nname" name="name" value="{{ old('name') }}" required></div>
            <div class="field"><label for="nemail">E-mail</label><input id="nemail" type="email" name="email" value="{{ old('email') }}" required></div>
            <div class="field"><label for="nrole">Rôle</label><select id="nrole" name="role">@foreach (\App\Models\User::ROLES as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
            <div class="field"><label for="npass">Mot de passe</label><input id="npass" type="password" name="password" required autocomplete="new-password"><span class="hint">8 caractères minimum. Transmettez-le à la personne, elle pourra le changer dans Paramètres.</span></div>
            <button class="btn btn-accent">Créer le compte</button>
        </div>
    </form>
</div>
@endsection
