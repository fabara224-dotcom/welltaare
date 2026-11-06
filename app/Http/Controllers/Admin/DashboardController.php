<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesignRequest;
use App\Models\Order;
use App\Models\Product;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function index()
    {
        $valid = Order::where('status', '!=', 'annulee');
        $monthStart = now()->startOfMonth();

        // Ventes des 14 derniers jours (commandes non annulées)
        $from = now()->subDays(13)->startOfDay();
        $daily = (clone $valid)->where('created_at', '>=', $from)->get(['total', 'created_at'])
            ->groupBy(fn ($o) => $o->created_at->format('Y-m-d'))
            ->map->sum('total');
        $chart = collect(CarbonPeriod::create($from, now()->startOfDay()))->map(fn ($d) => [
            'label' => $d->translatedFormat('d M'),
            'day' => $d->format('d'),
            'value' => (int) ($daily[$d->format('Y-m-d')] ?? 0),
        ]);

        $top = \App\Models\OrderItem::selectRaw('product_name, SUM(quantity) as qty, SUM(line_total) as revenue')
            ->whereHas('order', fn ($o) => $o->where('status', '!=', 'annulee'))
            ->groupBy('product_name')->orderByDesc('qty')->take(5)->get();

        return view('admin.dashboard', [
            'stats' => [
                'new_orders' => Order::where('status', 'nouvelle')->count(),
                'month_revenue' => (clone $valid)->where('created_at', '>=', $monthStart)->sum('total'),
                'paid_om' => Order::where('payment_status', 'paid')->where('created_at', '>=', $monthStart)->sum('total'),
                'month_orders' => (clone $valid)->where('created_at', '>=', $monthStart)->count(),
                'designs' => DesignRequest::where('status', 'nouvelle')->count() + \App\Models\GraphicRequest::where('status', 'nouvelle')->count(),
                'low_stock' => Product::whereNotNull('stock')->where('stock', '<=', 2)->count(),
                'products' => Product::count(),
            ],
            'chart' => $chart,
            'recent' => Order::latest()->take(8)->get(),
            'designs' => DesignRequest::latest()->take(5)->get(),
            'top' => $top,
            'lowStock' => Product::whereNotNull('stock')->where('stock', '<=', 2)->orderBy('stock')->take(5)->get(),
        ]);
    }
}
