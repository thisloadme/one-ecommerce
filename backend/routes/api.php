<?php

use App\Helpers\ResponseHelper;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\TenantController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware(['reguler'])->group(function () {
    Route::get('tenants', [TenantController::class,'index']);
    Route::get('tenants/{tenant}', [TenantController::class,'show']);

    Route::get('products', [ProductController::class, 'indexAll']);
});

Route::middleware(['login'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::middleware(['reguler'])->group(function () {
        Route::get('cart', [CartController::class, 'index']);
        Route::post('cart/{product}', [CartController::class, 'store']);
        Route::delete('cart/{product}', [CartController::class, 'destroy']);
        Route::post('checkout', [CartController::class, 'checkout']);
    });

    Route::middleware(['tenant'])->group(function () {
        Route::prefix('tenant')->group(function () {
            Route::get('products', [ProductController::class, 'index']);
            Route::post('products', [ProductController::class, 'store']);
            Route::get('products/{product}', [ProductController::class, 'show']);
            Route::put('products/{product}', [ProductController::class, 'update']); 
            Route::delete('products/{product}', [ProductController::class, 'destroy']);
            
            Route::get('/', function () {
                $tenant = request()->attributes->get('tenant');
                return ResponseHelper::basicResponse(
                    200,
                    [
                        'id' => $tenant->id,
                        'name' => $tenant->name,
                    ],
                    'Tenant info retrieved successfully'
                );
            });
        });
        
        // Route::get('/dashboard', function () {
        //     $tenant = request()->attributes->get('tenant');
        //     return view('dashboard', compact('tenant'));
        // });
    });
});
