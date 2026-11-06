<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = Product::with('images', 'category')->latest();
        if ($s = trim((string) $request->query('q'))) {
            $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%"));
        }
        if (in_array($g = $request->query('genre'), ['femme', 'homme'], true)) {
            $q->where('gender', $g);
        }
        if ($request->query('etat') === 'masque') {
            $q->where('is_active', false);
        } elseif ($request->query('etat') === 'stock') {
            $q->whereNotNull('stock')->where('stock', '<=', 2);
        }

        return view('admin.products.index', ['products' => $q->paginate(20)->withQueryString()]);
    }

    public function create()
    {
        return view('admin.products.form', ['product' => new Product(['is_active' => true, 'gender' => 'femme']), 'categories' => Category::orderBy('gender')->orderBy('position')->get()]);
    }

    public function store(Request $request)
    {
        $product = Product::create($this->validated($request));
        $this->storeImages($request, $product);

        return redirect()->route('admin.products.edit', $product)->with('status', 'Produit ajouté à la boutique.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', ['product' => $product->load('images'), 'categories' => Category::orderBy('gender')->orderBy('position')->get()]);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validated($request, $product));
        $this->storeImages($request, $product);

        return redirect()->route('admin.products.edit', $product)->with('status', 'Produit enregistré.');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Produit supprimé.');
    }

    public function destroyImage(ProductImage $image)
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('status', 'Photo supprimée.');
    }

    public function mainImage(ProductImage $image)
    {
        $min = ProductImage::where('product_id', $image->product_id)->min('position');
        $image->update(['position' => $min - 1]);

        return back()->with('status', 'Photo principale modifiée.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'gender' => ['required', 'in:femme,homme'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'sku' => ['nullable', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'integer', 'min:0'],
            'compare_price' => ['nullable', 'integer', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'sizes' => ['nullable', 'string', 'max:200'],
            'colors' => ['nullable', 'string', 'max:300'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ], [
            'images.*.image' => 'Chaque fichier doit être une image.',
            'images.*.mimes' => 'Formats acceptés : JPG, PNG, WEBP.',
            'images.*.max' => 'Chaque photo doit faire moins de 8 Mo.',
            'images.*.uploaded' => 'Une photo est trop lourde pour le serveur (limite PHP '.ini_get('upload_max_filesize').'). Réduisez-la ou augmentez upload_max_filesize.',
        ]);

        $split = fn ($v) => array_values(array_filter(array_map('trim', explode(',', (string) $v))));
        $data['sizes'] = $split($data['sizes'] ?? '');
        $data['colors'] = $split($data['colors'] ?? '');
        $data['is_new'] = $request->boolean('is_new');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');
        unset($data['images']);
        if ($product && $product->name !== $data['name']) {
            $data['slug'] = null;
        }

        return $data;
    }

    private function storeImages(Request $request, Product $product): void
    {
        if (! $request->hasFile('images')) {
            return;
        }
        $pos = (int) $product->images()->max('position');
        foreach ($request->file('images') as $file) {
            $product->images()->create([
                'path' => $file->store('products', 'public'),
                'position' => ++$pos,
            ]);
        }
    }
}
