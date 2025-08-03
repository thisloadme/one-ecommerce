<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Tenant;
use DB;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->attributes->get("user");

            $cart = Cart::query()
                ->where('user_id', $user->id)
                ->notPurchased()
                ->get()
                ->each(function ($item) {
                    $item->configure();

                    $product = Product::query()->findOrFail($item->product_id);
                    $tenant = Tenant::query()->where('database', $item->database)->first(['name', 'id']);

                    $item->product_name = $product->name;
                    $item->product_sku = $product->sku;
                    $item->description = $product->description;
                    $item->is_in_stock = $product->stock >= $item->quantity;
                    $item->price = $product->price;
                    $item->tenant_id = $tenant->id;
                    $item->tenant_name = $tenant->name;
                });

            return ResponseHelper::basicResponse(
                200,
                $cart,
                'Cart retrieved successfully'
            );
        } catch (\Throwable $th) {
            return ResponseHelper::serverError(
                'An error occurred while retrieving cart',
                $th->getMessage()
            );
        }
    }

    public function store(Request $request, $product)
    {
        try {
            $user = $request->attributes->get("user");

            $validated = $request->validate([
                'tenant_id' => 'required|int',
                'quantity' => 'nullable|int|min:1',
            ]);

            $tenant = Tenant::query()->findOrFail($validated['tenant_id']);
            $tenant->configure();

            $product = Product::query()->findOrFail($product);
            $existingCart = Cart::query()
                ->where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->where('database', $tenant->database)
                ->where('is_purchased', false)
                ->first();

            $cart = null;
            DB::transaction(function () use ($product, $existingCart, $validated, $user, $tenant, &$cart) {
                if ($existingCart) {
                    if (!$validated['quantity']) {
                        $existingCart->increment('quantity', 1);
                    }
    
                    $existingCart->lockForUpdate()->update([
                        'subtotal' => $product->price * $existingCart->quantity,
                        ...($validated['quantity'] ? [
                            'quantity' => $validated['quantity'],
                        ] : []),
                    ]);
                    $cart = $existingCart;
                } else {
                    $cart = Cart::query()->create([
                        'user_id' => $user->id,
                        'database' => $tenant->database,
                        'product_id' => $product->id,
                        'quantity' => $validated['quantity'] ?? 1,
                        'subtotal' => $product->price * ($validated['quantity'] ?? 1),
                        'is_purchased' => false,
                    ]);
                }
            });

            return ResponseHelper::basicResponse(
                200,
                $cart,
                'Product added to cart successfully'
            );
        } catch (\Throwable $th) {
            return ResponseHelper::serverError(
                'An error occurred while adding product to cart',
                $th->getMessage()
            );
        }
    }

    public function destroy(Request $request, $product)
    {
        try {
            $user = $request->attributes->get("user");

            $validated = $request->validate([
                'tenant_id' => 'required|int',
                'is_delete' => 'nullable|boolean',
            ]);

            $tenant = Tenant::query()->findOrFail($validated['tenant_id']);
            $tenant->configure();

            $product = Product::query()->findOrFail($product);
            $existingCart = Cart::query()
                ->where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->where('database', $tenant->database)
                ->where('is_purchased', false)
                ->first();

            $cart = null;
            DB::transaction(function () use ($product, $existingCart, $validated, &$cart) {
                if (!$existingCart || $validated['is_delete']) {
                    $existingCart->delete();
                    $cart = null;
                } else {
                    $existingCart->decrement('quantity');
                    $existingCart->lockForUpdate()->update([
                        'subtotal' => $product->price * $existingCart->quantity
                    ]);
                    $cart = $existingCart;
                }
            });

            return ResponseHelper::basicResponse(
                200,
                $cart,
                'Product quantity updated successfully'
            );
        } catch (\Throwable $th) {
            return ResponseHelper::serverError(
                'An error occurred while updating product quantity',
                $th->getMessage()
            );
        }
    }

    public function checkout(Request $request)
    {
        try {
            $user = $request->attributes->get("user");

            $userCart = Cart::query()
                ->where('user_id', $user->id)
                ->where('is_purchased', false)
                ->get();

            DB::transaction(function () use ($userCart) {
                foreach ($userCart as $cart) {
                    $cart->update([
                        'is_purchased' => true,
                    ]);
    
                    $cart->configure();
    
                    $product = Product::query()->findOrFail($cart->product_id);
                    $product->lockForUpdate()->update([
                        'stock' => $product->stock - $cart->quantity,
                    ]);
                }
            });

            return ResponseHelper::basicResponse(
                200,
                $userCart,
                'Checkout successfully'
            );
        } catch (\Throwable $th) {
            return ResponseHelper::serverError(
                'An error occurred while checkout',
                $th->getMessage()
            );
        }
    }
}