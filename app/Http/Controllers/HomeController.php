<?php

namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $base = Product::active()->with('images');

        return view('shop.home', [
            'featured' => (clone $base)->where('is_featured', true)->latest()->take(8)->get(),
            'newFemme' => (clone $base)->where('gender', 'femme')->latest()->take(4)->get(),
            'newHomme' => (clone $base)->where('gender', 'homme')->latest()->take(4)->get(),
            'categories' => \App\Models\Category::orderBy('gender')->orderBy('position')->get(),
        ]);
    }

    public function about()
    {
        return view('shop.about');
    }

    public function contact()
    {
        return view('shop.contact');
    }
}
