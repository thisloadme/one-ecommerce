<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $cart = Cart::query()
                ->where('user_id', $user->id)
                ->notPurchased()
                ->get()
                ->each(function ($item) {
                    $item->configure();

                    $product = Product::query()->findOrFail($item->product_id);
                    $tenant = Tenant::query()->where('database', $item->database)->value('name');

                    $item->product_name = $product->name;
                    $item->is_in_stock = $product->stock >= $item->quantity;
                    $item->price = $product->price;
                    $item->tenant_name = $tenant;
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
            $user = $request->user();

            $validated = $request->validate([
                'tenant_id' => 'required|int',
            ]);

            $tenant = Tenant::query()->findOrFail($validated['tenant_id']);
            $tenant->configure();

            $product = Product::findOrFail($product);
            $existingCart = Cart::query()
                ->where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->where('database', $tenant->database)
                ->where('is_purchased', false)
                ->first();

            if ($existingCart) {
                $existingCart->increment('quantity');
                $existingCart->update([
                    'subtotal' => $product->price * $existingCart->quantity
                ]);
                $cart = $existingCart;
            } else {
                $cart = Cart::query()->create([
                    'user_id' => $user->id,
                    'database' => $tenant->database,
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'subtotal' => $product->price,
                    'is_purchased' => false,
                ]);
            }

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
            $user = $request->user();

            $validated = $request->validate([
                'tenant_id' => 'required|int',
                'is_delete' => 'nullable|boolean',
            ]);

            $tenant = Tenant::query()->findOrFail($validated['tenant_id']);
            $tenant->configure();

            $product = Product::findOrFail($product);
            $existingCart = Cart::query()
                ->where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->where('database', $tenant->database)
                ->where('is_purchased', false)
                ->first();

            if (!$existingCart || $validated['is_delete']) {
                $existingCart->delete();
                $cart = null;
            } else {
                $existingCart->decrement('quantity');
                $existingCart->update([
                    'subtotal' => $product->price * $existingCart->quantity
                ]);
                $cart = $existingCart;
            }

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
}