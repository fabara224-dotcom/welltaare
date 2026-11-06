{{-- Visuel produit : photo si disponible, sinon motif aux couleurs de la collection --}}
@php
    $imgs = $product?->images ?? collect();
    $first = $imgs->first();
    $variant = ($product->id ?? 0) % 3;
    $g = ($product->gender ?? 'femme') === 'homme' ? 'homme' : 'femme';
@endphp
<div class="art {{ $first ? 'has-img' : 'art-'.$g.'-'.$variant }}">
    @if ($first)
        <img src="{{ $first->url() }}" alt="{{ $product->name }}" loading="lazy">
        @if (($hover ?? false) && $imgs->count() > 1)
            <img class="alt" src="{{ $imgs[1]->url() }}" alt="" loading="lazy">
        @endif
    @elseif ($label ?? true)
        <span class="art-label">{{ $product->name }}</span>
    @endif
</div>
