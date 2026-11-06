@extends('layouts.shop')
@section('title', 'Trop de demandes')
@section('content')
<div class="container"><div class="thanks" style="text-align:center;align-items:center">
    <span class="eyebrow">Un instant</span>
    <h1>Trop de demandes en peu de temps.</h1>
    <p style="color:var(--muted)">Patientez une minute puis réessayez.</p>
    <a class="btn" href="{{ url()->previous() }}">Revenir</a>
</div></div>
@endsection
