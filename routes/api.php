<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\AuthController;
use App\Http\Controllers\Api\V1\Admin\CategoryController;
use App\Http\Controllers\Api\V1\Admin\DashboardController;
use App\Http\Controllers\Api\V1\Admin\ProductController;
use App\Http\Controllers\Api\V1\Admin\UserController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::get('/user', function (Request $request) {
    return ['message' => 'You are authenticated'];
});

Route::get('/test',function (Request $request) {
    return response()->json(['message' => 'oui ']);
});

Route::prefix('v1')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);

            Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware(['role:super_admin', 'permission:view_dashboard']);

            Route::get('products', [ProductController::class, 'index'])
            ->middleware(['role:super_admin', 'permission:view_products']);
            Route::post('products', [ProductController::class, 'store'])
            ->middleware(['role:super_admin', 'permission:create_products']);
            Route::get('products/{id}', [ProductController::class, 'show'])
            ->middleware(['role:super_admin', 'permission:view_products']);
            Route::put('products/{id}', [ProductController::class, 'update'])
            ->middleware(['role:super_admin', 'permission:edit_products']);
            Route::delete('products/{id}', [ProductController::class, 'destroy'])
            ->middleware(['role:super_admin','permission:delete_products']);

            Route::get('categories', [CategoryController::class, 'index'])
            ->middleware(['role:super_admin','permission:view_categories']);
            Route::post('categories', [CategoryController::class, 'store'])
            ->middleware(['role:super_admin','permission:create_categories']);
            Route::get('categories/{id}', [CategoryController::class, 'show'])
            ->middleware(['role:super_admin','permission:view_categories']);
             Route::put('categories/{id}', [CategoryController::class, 'update'])
            ->middleware(['role:super_admin','permission:edit_categories']);
            Route::delete('categories/{id}', [CategoryController::class, 'destroy'])
            ->middleware(['role:super_admin','permission:delete_categories']);

            Route::get('users', [UserController::class, 'index'])
            ->middleware(['role:super_admin','permission:view_users']);
            Route::post('users', [UserController::class, 'store'])
            ->middleware(['role:super_admin','permission:create_users']);
            Route::get('users/{id}', [UserController::class, 'show'])
            ->middleware(['role:super_admin','permission:view_users']);
            Route::put('users/{id}', [UserController::class, 'update'])
            ->middleware(['role:super_admin','permission:edit_users']);
            Route::delete('users/{id}', [UserController::class, 'destroy'])
            ->middleware(['role:super_admin','permission:delete_users']);

        });
    });
});
