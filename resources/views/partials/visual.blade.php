{{-- Photo choisie dans Admin → Apparence, sinon motif noir & or --}}
@php $src = $src ?? site_image($key ?? ''); @endphp
<div class="art {{ $src ? 'has-img' : $fallback }}" @isset($style) style="{{ $style }}" @endisset>
    @if ($src)<img src="{{ $src }}" alt="{{ $alt ?? '' }}" loading="lazy">@endif
    @isset($label)<span class="art-label" @isset($labelStyle) style="{{ $labelStyle }}" @endisset>{!! $label !!}</span>@endisset
</div>
