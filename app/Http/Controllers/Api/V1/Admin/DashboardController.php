<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->hasAnyRole(['super_admin']) || (!$request->user()->hasAnyPermission('view_dashboard')) || (!$request->user()->hasAnyPermission('view_dashboard'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'error'
            ], 403);
        }

        $lowStockThreshold = 5;

        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalCategories = Category::count();

        $lowStockProducts = Product::where('stock', '<=', $lowStockThreshold)->count();

        $recentProducts = Product::with('category')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'category' => $product->category ? $product->category->name : 'Non catégorisé',
                    'status' => $product->status,
                    'created_at' => $product->created_at->format('Y-m-d H:i:s')
                ];
            });

        $productsByCategory = Category::withCount('products')
            ->get()
            ->map(function($category) {
                return [
                    'category' => $category->name,
                    'count' => $category->products_count
                ];
            });


        $totalSales = 12500;


        $latestOrders = [
            [
                'id' => 1001,
                'customer' => 'Si Ayyoub Lghzal',
                'amount' => 1299.99,
                'status' => 'completed',
                'date' => '2025-03-10'
            ],
            [
                'id' => 1002,
                'customer' => 'Sara Alami',
                'amount' => 459.50,
                'status' => 'processing',
                'date' => '2025-03-11'
            ]
        ];

        $stats = [
            'total_products' => $totalProducts,
            'total_users' => $totalUsers,
            'total_categories' => $totalCategories,
            'low_stock_products' => $lowStockProducts,
            'total_sales' => $totalSales,
            'latest_orders' => $latestOrders,
            'recent_products' => $recentProducts,
            'products_by_category' => $productsByCategory
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }
}
