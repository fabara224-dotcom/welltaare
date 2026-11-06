@extends('layouts.admin')
@section('title', 'Produits')

@section('content')
<div class="topbar">
    <div><h1>Produits</h1><p>{{ $products->total() }} article(s) au catalogue.</p></div>
    <a class="btn btn-accent" href="{{ route('admin.products.create') }}">+ Ajouter un produit</a>
</div>

<div class="filters">
    <a class="chip {{ ! request('genre') && ! request('etat') ? 'on' : '' }}" href="{{ route('admin.products.index') }}">Tous</a>
    <a class="chip {{ request('genre') === 'femme' ? 'on' : '' }}" href="{{ route('admin.products.index', ['genre' => 'femme']) }}">Femme</a>
    <a class="chip {{ request('genre') === 'homme' ? 'on' : '' }}" href="{{ route('admin.products.index', ['genre' => 'homme']) }}">Homme</a>
    <a class="chip {{ request('etat') === 'stock' ? 'on' : '' }}" href="{{ route('admin.products.index', ['etat' => 'stock']) }}">Stock faible</a>
    <a class="chip {{ request('etat') === 'masque' ? 'on' : '' }}" href="{{ route('admin.products.index', ['etat' => 'masque']) }}">Masqués</a>
    <form class="search" method="get"><input type="search" name="q" value="{{ request('q') }}" placeholder="Nom ou référence"><button class="btn btn-ghost">Chercher</button></form>
</div>

<div class="card">
    @if ($products->isEmpty())
        <div class="empty">Aucun produit. <a href="{{ route('admin.products.create') }}" style="color:var(--accent)">Ajoutez votre premier article</a>.</div>
    @else
    <div class="table-wrap"><table>
        <thead><tr><th>Produit</th><th>Catégorie</th><th>Prix</th><th>Stock</th><th>Boutique</th><th></th></tr></thead>
        <tbody>
        @foreach ($products as $p)
            <tr>
                <td><div class="prod-cell"><div class="thumb {{ $p->gender }}">@if($p->images->first())<img src="{{ $p->images->first()->url() }}" alt="">@endif</div><div><b style="font-weight:500">{{ $p->name }}</b><div class="sub">{{ $p->images->count() }} photo(s){{ $p->sku ? ' · '.$p->sku : '' }}</div></div></div></td>
                <td>{{ $p->category->name ?? '—' }}<div class="sub">{{ ucfirst($p->gender) }}</div></td>
                <td class="num">{{ gnf($p->price) }}@if($p->compare_price)<div class="sub"><s>{{ gnf($p->compare_price) }}</s></div>@endif</td>
                <td class="num">@if($p->stock === null)<span class="muted">Non suivi</span>@elseif($p->stock <= 0)<span class="pill p-failed">Épuisé</span>@elseif($p->stock <= 2)<span class="pill p-pending">{{ $p->stock }}</span>@else{{ $p->stock }}@endif</td>
                <td>@if($p->is_active)<span class="pill p-on">En ligne</span>@else<span class="pill p-off">Masqué</span>@endif @if($p->is_featured)<span class="pill p-nouvelle">Vedette</span>@endif</td>
                <td style="text-align:right;white-space:nowrap"><a class="btn btn-ghost btn-sm" href="{{ route('product.show', $p) }}" target="_blank">Voir</a> <a class="btn btn-sm" href="{{ route('admin.products.edit', $p) }}">Modifier</a></td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
    {{ $products->links() }}
    @endif
</div>
@endsection
