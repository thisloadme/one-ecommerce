<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResponseHelper;
use App\Http\Controllers\TenantController;
use App\Providers\ResponseServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

// get /products
// get /cart
// post /cart/{product}
// delete /cart/{product}

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::middleware(['login'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('tenants', [TenantController::class,'index']);
    Route::get('tenants/{tenant}', [TenantController::class,'show']);

    Route::get('products', [ProductController::class, 'indexAll']);

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
