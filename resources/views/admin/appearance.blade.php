@extends('layouts.admin')
@section('title', 'Apparence & photos')

@section('content')
<div class="topbar"><div><h1>Apparence & photos</h1><p>Remplacez les motifs dorés par vos vraies photos. Chaque image s'affiche aussitôt sur le site. Les photos des produits s'ajoutent dans <a href="{{ route('admin.products.index') }}" style="color:var(--accent)">Produits</a>, celles des catégories dans <a href="{{ route('admin.categories.index') }}" style="color:var(--accent)">Catégories</a>.</p></div></div>

<div class="images" style="grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:16px">
    @foreach ($slots as $key => [$label, $hint, $ratio])
        @php $url = site_image($key); @endphp
        <div class="card" style="overflow:hidden;display:flex;flex-direction:column">
            <div style="aspect-ratio:{{ $ratio }};background:{{ $key === 'logo' ? '#0B0A09' : 'linear-gradient(160deg,#2B2214,#0B0A09)' }};display:grid;place-items:center;overflow:hidden">
                @if ($url)<img src="{{ $url }}" alt="{{ $label }}" style="width:100%;height:100%;object-fit:{{ $key === 'logo' ? 'contain' : 'cover' }}">@else<span style="color:#C9A24A;font-size:.8rem;letter-spacing:.14em">MOTIF PAR DÉFAUT</span>@endif
            </div>
            <div class="card-b" style="display:flex;flex-direction:column;gap:8px;flex:1">
                <b style="font-weight:600;font-size:.92rem">{{ $label }}</b>
                <span class="muted" style="font-size:.82rem;flex:1">{{ $hint }}</span>
                <form method="post" action="{{ route('admin.appearance.update') }}" enctype="multipart/form-data">
                    @csrf<input type="hidden" name="slot" value="{{ $key }}">
                    <label class="btn btn-sm" style="width:100%;cursor:pointer">{{ $url ? 'Remplacer' : 'Ajouter une photo' }}<input type="file" name="image" accept="image/jpeg,image/png,image/webp" hidden onchange="this.form.submit()"></label>
                </form>
                @if ($url)
                    <form method="post" action="{{ route('admin.appearance.destroy', $key) }}">@csrf @method('delete')<button class="btn btn-ghost btn-sm" style="width:100%">Retirer</button></form>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
