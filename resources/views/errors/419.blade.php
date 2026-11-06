@extends('layouts.shop')
@section('title', 'Page expirée')
@section('content')
<div class="container"><div class="thanks" style="text-align:center;align-items:center">
    <span class="eyebrow">Un instant</span>
    <h1>La page a expiré.</h1>
    <p style="color:var(--muted)">Rechargez la page puis recommencez.</p>
    <a class="btn" href="{{ url()->previous() }}">Revenir</a>
</div></div>
@endsection
