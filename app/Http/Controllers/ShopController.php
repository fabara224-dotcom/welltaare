<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request, ?string $gender = null)
    {
        $gender = $gender ?? $request->query('genre');
        $gender = in_array($gender, ['femme', 'homme'], true) ? $gender : null;

        $q = Product::active()->with('images', 'category');

        if ($gender) {
            $q->where('gender', $gender);
        }
        if ($cat = $request->query('categorie')) {
            $q->whereHas('category', fn ($c) => $c->where('slug', $cat));
        }
        if ($search = trim((string) $request->query('q'))) {
            $q->where(fn ($w) => $w->where('name', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"));
        }
        if ($request->boolean('africain')) {
            $q->whereHas('category', fn ($c) => $c->whereIn('slug', ['costumes-africains', 'grands-boubous', 'chemises-tuniques', 'tenues-africaines-femme']));
        }
        if ($request->boolean('nouveau')) {
            $q->where('is_new', true);
        }
        if ($request->boolean('promo')) {
            $q->whereNotNull('compare_price')->whereColumn('compare_price', '>', 'price');
        }

        match ($request->query('tri')) {
            'prix-asc' => $q->orderBy('price'),
            'prix-desc' => $q->orderByDesc('price'),
            default => $q->latest(),
        };

        $categories = Category::when($gender, fn ($c) => $c->where('gender', $gender))
            ->withCount(['products' => fn ($p) => $p->where('is_active', true)])
            ->orderBy('gender')->orderBy('position')->get();

        return view('shop.index', [
            'products' => $q->paginate(12)->withQueryString(),
            'categories' => $categories,
            'gender' => $gender,
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);
        $product->load('images', 'category');

        $related = Product::active()->with('images')
            ->where('id', '!=', $product->id)
            ->where('gender', $product->gender)
            ->inRandomOrder()->take(4)->get();

        return view('shop.product', compact('product', 'related'));
    }
}
