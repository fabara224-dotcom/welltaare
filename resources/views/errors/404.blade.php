@extends('layouts.shop')
@section('title', 'Page introuvable')
@section('content')
<div class="container"><div class="thanks" style="text-align:center;align-items:center">
    <span class="eyebrow">Erreur 404</span>
    <h1>Cette page n'existe pas.</h1>
    <p style="color:var(--muted)">L'article a peut-être été retiré de la collection.</p>
    <a class="btn" href="{{ route('shop') }}">Retour à la boutique</a>
</div></div>
@endsection
