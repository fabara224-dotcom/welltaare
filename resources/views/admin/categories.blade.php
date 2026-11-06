@extends('layouts.admin')
@section('title', 'Catégories')

@section('content')
<div class="topbar"><div><h1>Catégories</h1><p>Organisez la boutique en rayons. Cliquez sur la vignette pour ajouter la photo d'une catégorie (affichée sur l'accueil).</p></div></div>
<div class="grid g-main" style="align-items:start">
    <div class="card">
        <div class="table-wrap"><table>
            <thead><tr><th>Photo · Nom</th><th>Collection</th><th>Ordre</th><th>Produits</th><th></th></tr></thead>
            <tbody>
            @forelse ($categories as $c)
                <tr>
                    <td colspan="3">
                        <form method="post" action="{{ route('admin.categories.update', $c) }}" enctype="multipart/form-data" style="display:grid;grid-template-columns:48px 2fr 1fr 70px auto;gap:8px;align-items:center" class="field">
                            @csrf @method('put')
                            <label class="thumb {{ $c->gender }}" title="Changer la photo de la catégorie" style="cursor:pointer;position:relative">@if($c->imageUrl())<img src="{{ $c->imageUrl() }}" alt="">@endif<input type="file" name="image" accept="image/jpeg,image/png,image/webp" hidden onchange="this.form.submit()"></label>
                            <input name="name" value="{{ $c->name }}" aria-label="Nom">
                            <select name="gender" aria-label="Collection"><option value="femme" @selected($c->gender === 'femme')>Femme</option><option value="homme" @selected($c->gender === 'homme')>Homme</option></select>
                            <input name="position" value="{{ $c->position }}" inputmode="numeric" aria-label="Ordre">
                            <button class="btn btn-ghost btn-sm">OK</button>
                        </form>
                    </td>
                    <td class="num">{{ $c->products_count }}</td>
                    <td style="text-align:right"><form method="post" action="{{ route('admin.categories.destroy', $c) }}" onsubmit="return confirm('Supprimer la catégorie « {{ $c->name }} » ?')">@csrf @method('delete')<button class="btn btn-danger btn-sm">Supprimer</button></form></td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">Aucune catégorie.</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
    <form class="card" method="post" action="{{ route('admin.categories.store') }}">
        @csrf
        <div class="card-h"><h2>Nouvelle catégorie</h2></div>
        <div class="card-b form">
            <div class="field"><label for="cname">Nom</label><input id="cname" name="name" required placeholder="Robes de cocktail"></div>
            <div class="field"><label for="cgender">Collection</label><select id="cgender" name="gender"><option value="femme">Femme</option><option value="homme">Homme</option></select></div>
            <button class="btn btn-accent">Ajouter</button>
        </div>
    </form>
</div>
@endsection
