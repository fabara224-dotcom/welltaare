<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories', ['categories' => Category::withCount('products')->orderBy('gender')->orderBy('position')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:80', 'gender' => 'required|in:femme,homme']);
        $slug = Str::slug($data['name'].'-'.$data['gender']);
        Category::create($data + ['slug' => $slug, 'position' => (int) Category::max('position') + 1]);

        return back()->with('status', 'Catégorie ajoutée.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:80', 'gender' => 'required|in:femme,homme', 'position' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
        ]);
        unset($data['image']);
        if ($request->hasFile('image')) {
            if ($category->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image_path);
            }
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }
        $category->update($data);

        return back()->with('status', 'Catégorie modifiée.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return back()->with('status', 'Catégorie supprimée. Ses produits restent en boutique, sans catégorie.');
    }
}
