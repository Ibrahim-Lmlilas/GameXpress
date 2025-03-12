<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->hasAnyRole(['super_admin']) || (!$request->user()->hasAnyPermission('view_dashboard'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'error'
            ], 403);
        }



        $lowStockThreshold = 5;
        $totalUsers = User::count();

        $stats = [
            'total_products' => 145,
            'total_users' => $totalUsers,
            'low_stock_products' => 8,
            'total_sales' => 12500,
            'latest_orders' => [
                [
                    'id' => 1001,
                    'customer' => 'si ayyoub lghzal',
                    'amount' => 1299.99,
                    'status' => 'completed',
                    'date' => '2025-03-10'
                ],
                [
                    'id' => 1002,
                    'customer' => 'Sara Alaoui',
                    'amount' => 459.50,
                    'status' => 'processing',
                    'date' => '2025-03-11'
                ]
            ]
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }
}
