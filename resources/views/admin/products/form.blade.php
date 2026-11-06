@extends('layouts.admin')
@section('title', $product->exists ? 'Modifier '.$product->name : 'Nouveau produit')

@section('content')
<div class="topbar">
    <div><a href="{{ route('admin.products.index') }}" class="muted">← Produits</a><h1 style="margin-top:6px">{{ $product->exists ? $product->name : 'Nouveau produit' }}</h1></div>
    @if ($product->exists)<a class="btn btn-ghost" href="{{ route('product.show', $product) }}" target="_blank">Voir sur le site ↗</a>@endif
</div>

<form method="post" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" class="grid g-main" style="align-items:start">
    @csrf
    @if ($product->exists) @method('put') @endif
    <div class="grid">
        <div class="card"><div class="card-b form">
            <div class="field"><label for="name">Nom du produit *</label><input id="name" name="name" value="{{ old('name', $product->name) }}" required placeholder="Robe fendue en satin">@error('name')<span class="error">{{ $message }}</span>@enderror</div>
            <div class="field"><label for="description">Description</label><textarea id="description" name="description" placeholder="Matière, coupe, conseils d'entretien…">{{ old('description', $product->description) }}</textarea></div>
        </div></div>

        <div class="card">
            <div class="card-h"><h2>Photos</h2><span class="muted" style="font-size:.84rem">La première photo est la photo principale</span></div>
            <div class="card-b">
                @if ($product->exists && $product->images->isNotEmpty())
                    <div class="images" style="margin-bottom:16px">
                        @foreach ($product->images as $i => $img)
                            <div class="img-tile">
                                <img src="{{ $img->url() }}" alt="">
                                @if ($i === 0)<span class="main-tag">PRINCIPALE</span>@endif
                                <div class="acts">
                                    @if ($i > 0)<button type="submit" form="main-{{ $img->id }}">Principale</button>@endif
                                    <button type="submit" form="del-{{ $img->id }}" class="del">Supprimer</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                <label class="dropzone" id="dropzone" for="images">
                    <b>Ajouter des photos</b>
                    <span class="muted" style="font-size:.86rem">Cliquez ou glissez vos images ici · JPG, PNG, WEBP · 8 Mo max par photo · jusqu'à 10 à la fois</span>
                    <input type="file" id="images" name="images[]" accept="image/jpeg,image/png,image/webp" multiple hidden>
                </label>
                <div class="previews" id="previews"></div>
                @error('images.*')<span class="error" style="color:var(--bad);font-size:.85rem">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="card">
            <div class="card-h"><h2>Variantes</h2></div>
            <div class="card-b form">
                <div class="row-2">
                    <div class="field"><label for="sizes">Tailles</label><input id="sizes" name="sizes" value="{{ old('sizes', implode(', ', $product->sizes ?? ['S', 'M', 'L', 'XL'])) }}"><span class="hint">Séparées par des virgules : S, M, L, XL — ou 38, 40, 42</span></div>
                    <div class="field"><label for="colors">Couleurs</label><input id="colors" name="colors" value="{{ old('colors', implode(', ', $product->colors ?? [])) }}" placeholder="Rouge, Noir, Or"><span class="hint">Laissez vide si une seule couleur</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid" style="position:sticky;top:20px">
        <div class="card"><div class="card-b form">
            <label class="switch"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))><span><b>Visible en boutique</b><small>Décochez pour masquer l'article sans le supprimer</small></span></label>
            <label class="switch"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))><span><b>Mettre en vedette</b><small>Affiché dans « Coups de cœur » sur l'accueil</small></span></label>
            <label class="switch"><input type="checkbox" name="is_new" value="1" @checked(old('is_new', $product->is_new))><span><b>Badge « Nouveau »</b></span></label>
        </div></div>
        <div class="card"><div class="card-b form">
            <div class="row-2">
                <div class="field"><label for="gender">Collection *</label><select id="gender" name="gender"><option value="femme" @selected(old('gender', $product->gender) === 'femme')>Femme</option><option value="homme" @selected(old('gender', $product->gender) === 'homme')>Homme</option></select></div>
                <div class="field"><label for="category_id">Catégorie</label><select id="category_id" name="category_id"><option value="">—</option>@foreach ($categories as $c)<option value="{{ $c->id }}" data-g="{{ $c->gender }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }} ({{ $c->gender }})</option>@endforeach</select></div>
            </div>
            <div class="row-2">
                <div class="field"><label for="price">Prix (GNF) *</label><input id="price" name="price" value="{{ old('price', $product->price) }}" inputmode="numeric" required>@error('price')<span class="error">{{ $message }}</span>@enderror</div>
                <div class="field"><label for="compare_price">Prix barré</label><input id="compare_price" name="compare_price" value="{{ old('compare_price', $product->compare_price) }}" inputmode="numeric" placeholder="Pour une promo"></div>
            </div>
            <div class="row-2">
                <div class="field"><label for="stock">Stock</label><input id="stock" name="stock" value="{{ old('stock', $product->stock) }}" inputmode="numeric"><span class="hint">Vide = non suivi</span></div>
                <div class="field"><label for="sku">Référence</label><input id="sku" name="sku" value="{{ old('sku', $product->sku) }}" placeholder="WT-001"></div>
            </div>
            <button class="btn btn-accent" style="padding:12px">{{ $product->exists ? 'Enregistrer' : 'Ajouter à la boutique' }}</button>
        </div></div>
        @if ($product->exists)
            <button type="submit" form="delete-product" class="btn btn-danger" onclick="return confirm('Supprimer définitivement ce produit et ses photos ?')">Supprimer le produit</button>
        @endif
    </div>
</form>

@if ($product->exists)
    <form id="delete-product" method="post" action="{{ route('admin.products.destroy', $product) }}">@csrf @method('delete')</form>
    @foreach ($product->images as $img)
        <form id="del-{{ $img->id }}" method="post" action="{{ route('admin.images.destroy', $img) }}" onsubmit="return confirm('Supprimer cette photo ?')">@csrf @method('delete')</form>
        <form id="main-{{ $img->id }}" method="post" action="{{ route('admin.images.main', $img) }}">@csrf</form>
    @endforeach
@endif
@endsection

@push('scripts')
<script>
(function () {
  var input = document.getElementById('images'), zone = document.getElementById('dropzone'), prev = document.getElementById('previews');
  function show() {
    prev.innerHTML = '';
    Array.prototype.forEach.call(input.files, function (f) {
      var img = document.createElement('img'); img.src = URL.createObjectURL(f); img.alt = f.name; prev.appendChild(img);
    });
  }
  // Réduit les photos (max 1600 px, JPEG) avant l'envoi : évite les refus pour fichier trop lourd
  function shrink(file) {
    return new Promise(function (resolve) {
      if (!/^image\/(jpeg|png|webp)$/.test(file.type) || !window.createImageBitmap) return resolve(file);
      createImageBitmap(file).then(function (bmp) {
        var k = Math.min(1, 1600 / Math.max(bmp.width, bmp.height));
        if (k === 1 && file.size < 1500000) return resolve(file);
        var c = document.createElement('canvas'); c.width = Math.round(bmp.width * k); c.height = Math.round(bmp.height * k);
        c.getContext('2d').drawImage(bmp, 0, 0, c.width, c.height);
        c.toBlob(function (b) { resolve(b ? new File([b], file.name.replace(/\.\w+$/, '') + '.jpg', { type: 'image/jpeg' }) : file); }, 'image/jpeg', 0.86);
      }).catch(function () { resolve(file); });
    });
  }
  var busy = false;
  function prepare() {
    if (busy || !input.files.length) return show();
    busy = true; zone.querySelector('b').textContent = 'Préparation des photos…';
    Promise.all(Array.prototype.map.call(input.files, shrink)).then(function (files) {
      try { var dt = new DataTransfer(); files.forEach(function (f) { dt.items.add(f); }); input.files = dt.files; } catch (e) {}
      busy = false; zone.querySelector('b').textContent = files.length + ' photo(s) prête(s) — cliquez sur Enregistrer'; show();
    });
  }
  input.addEventListener('change', prepare);
  ['dragenter', 'dragover'].forEach(function (e) { zone.addEventListener(e, function (ev) { ev.preventDefault(); zone.classList.add('drag'); }); });
  ['dragleave', 'drop'].forEach(function (e) { zone.addEventListener(e, function (ev) { ev.preventDefault(); zone.classList.remove('drag'); }); });
  zone.addEventListener('drop', function (ev) { input.files = ev.dataTransfer.files; prepare(); });
  // Filtre les catégories selon la collection
  var g = document.getElementById('gender'), c = document.getElementById('category_id');
  function filt() { Array.prototype.forEach.call(c.options, function (o) { if (o.dataset.g) o.hidden = o.dataset.g !== g.value; }); if (c.selectedOptions[0] && c.selectedOptions[0].hidden) c.value = ''; }
  g.addEventListener('change', filt); filt();
})();
</script>
@endpush
