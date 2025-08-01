<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Providers\ResponseServiceProvider;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $active = $request->boolean('active');
            $search = $request->input('search');

            $products = Product::query()
                ->when($active, function ($query) use ($active) {
                    $query->where('is_active', $active);
                })
                ->when($search, function ($query) use ($search) {
                    $query->where('name', 'ilike', "%{$search}%")
                        ->orWhere('sku', 'ilike', "%{$search}%");
                })
                ->paginate(15);

            return ResponseServiceProvider::response(
                200,
                $products,
                'Products retrieved successfully'
            );
        } catch (\Throwable $th) {
            return ResponseServiceProvider::response(
                500,
                [],
                'An error occurred while retrieving products',
                $th->getMessage()
            );
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'sku' => 'required|string|unique:products,sku',
                'is_active' => 'boolean',
            ]);
    
            $product = Product::query()->create($validated);

            return ResponseServiceProvider::response(
                200,
                $product,
                'Product created successfully'
            );
        } catch (\Throwable $th) {
            return ResponseServiceProvider::response(
                500,
                [],
                'An error occurred while creating the product',
                $th->getMessage()
            );
        }
    }

    public function show(Product $product)
    {
        try {
            $product->load('category');

            return ResponseServiceProvider::response(
                200,
                $product,
                'Product retrieved successfully'
            );
        } catch (\Throwable $th) {
            return ResponseServiceProvider::response(
                500,
                [],
                'An error occurred while retrieving the product',
                $th->getMessage()
            );
        }
    }

    public function update(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|numeric|min:0',
                'stock' => 'sometimes|integer|min:0',
                'sku' => 'sometimes|string|unique:products,sku,' . $product->id,
                'is_active' => 'boolean',
            ]);
    
            $product->lockForUpdate()->update($validated);

            return ResponseServiceProvider::response(
                200,
                $product,
                'Product updated successfully'
            );
        } catch (\Throwable $th) {
            return ResponseServiceProvider::response(
                500,
                [],
                'An error occurred while updating the product',
                $th->getMessage()
            );
        }
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();

            return ResponseServiceProvider::response(
                200,
                [],
                'Product deleted successfully'
            );
        } catch (\Throwable $th) {
            return ResponseServiceProvider::response(
                500,
                [],
                'An error occurred while deleting the product',
                $th->getMessage()
            );
        }
    }
}