<article class="p-card">
    <a href="{{ route('product.show', $product) }}" class="media" aria-label="{{ $product->name }}">
        <div class="tags">
            @if ($product->is_new)<span class="tag tag-new">Nouveau</span>@endif
            @if ($product->discountPercent())<span class="tag tag-promo">-{{ $product->discountPercent() }}%</span>@endif
            @if ($product->isOutOfStock())<span class="tag tag-out">Épuisé</span>@endif
        </div>
        @include('partials.art', ['product' => $product, 'hover' => true, 'label' => false])
        @unless ($product->isOutOfStock())
            <span class="quick btn btn-light btn-sm btn-block">Voir l'article</span>
        @endunless
    </a>
    <div class="meta">
        <small>{{ $product->category->name ?? ($product->gender === 'homme' ? 'Homme' : 'Femme') }}</small>
        <h3><a href="{{ route('product.show', $product) }}">{{ $product->name }}</a></h3>
    </div>
    <div class="price">{{ gnf($product->price) }}@if ($product->compare_price && $product->compare_price > $product->price)<s>{{ gnf($product->compare_price) }}</s>@endif</div>
</article>
