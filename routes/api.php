<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\AuthController;
use App\Http\Controllers\Api\V1\Admin\DashboardController;

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
            
            Route::get('/dashboard', [DashboardController::class, 'index']);
        });
    });
});
