<?php

use App\Providers\ResponseServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['tenant'])->group(function () {

    Route::prefix('api')->group(function () {
        // Products Routes  
        Route::get('products', [ProductController::class, 'index']);
        Route::post('products', [ProductController::class, 'store']);
        Route::get('products/{product}', [ProductController::class, 'show']);
        Route::put('products/{product}', [ProductController::class, 'update']); 
        Route::delete('products/{product}', [ProductController::class, 'destroy']);
        
        // Tenant info
        Route::get('tenant', function () {
            $tenant = request()->attributes->get('tenant');
            return ResponseServiceProvider::response(
                200,
                [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                ],
                'Tenant info retrieved successfully'
            );
        });
    });
    
    // Web routes for tenant
    Route::get('/dashboard', function () {
        $tenant = request()->attributes->get('tenant');
        return view('dashboard', compact('tenant'));
    });
});
