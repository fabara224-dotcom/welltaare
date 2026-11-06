@extends('layouts.shop')
@section('title', 'Contact & boutique')

@section('content')
<section class="page-hero"><div class="container"><h1>Contact & boutique</h1><p>Venez essayer nos pièces en boutique ou écrivez-nous : nous répondons rapidement.</p></div></section>
<div class="container">
    <div class="atelier-grid" style="padding-block:48px 88px">
        @include('partials.visual', ['key' => 'contact', 'fallback' => 'art-femme-1', 'style' => 'aspect-ratio:4/3', 'alt' => 'La boutique'])
        <div style="display:flex;flex-direction:column;gap:22px">
            <div><span class="eyebrow">Adresse</span><p style="font-size:1.2rem;margin-top:6px">{{ setting('shop_address') }}</p></div>
            <div><span class="eyebrow">Horaires</span><p style="font-size:1.2rem;margin-top:6px">{{ setting('shop_hours') }}</p></div>
            @if (setting('shop_phone'))<div><span class="eyebrow">Téléphone</span><p class="num" style="font-size:1.2rem;margin-top:6px"><a href="tel:{{ preg_replace('/[^\d+]/', '', setting('shop_phone')) }}">{{ setting('shop_phone') }}</a></p></div>@endif
            @if (setting('shop_email'))<div><span class="eyebrow">E-mail</span><p style="font-size:1.2rem;margin-top:6px"><a href="mailto:{{ setting('shop_email') }}">{{ setting('shop_email') }}</a></p></div>@endif
            <div style="display:flex;gap:10px;flex-wrap:wrap">
                @if ($wa = wa_link(setting('shop_whatsapp'), 'Bonjour '.config('shop.name').', '))<a class="btn btn-accent" href="{{ $wa }}" target="_blank" rel="noopener">Écrire sur WhatsApp</a>@endif
                <a class="btn btn-ghost" href="{{ route('design.create') }}">Demande sur mesure</a>
            </div>
        </div>
    </div>
</div>
@endsection
