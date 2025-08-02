<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function indexAll(Request $request) {
        try {
            $limit = $request->integer('limit', 15);
            $page = $request->integer('page', 1);
            $tenant = $request->input('tenant');

            $finalData = collect();

            if (!empty($tenant)) {
                $tenantData = Tenant::query()->find($tenant);
                if (!$tenantData) {
                    return ResponseHelper::basicResponse(
                        404,
                        [],
                        'Tenant not found'
                    );
                }

                $tenantData->configure();

                $request->merge(['active' => true]);
                $getProducts = $this->index($request, true);
                if ($getProducts->getData()->code != 200) {
                    return $getProducts;
                }

                $finalData = $getProducts->getData()->data;
            } else {
                $allTenants = Tenant::query()->get();
                foreach ($allTenants as $tenant) {
                    $tenant->configure();

                    $request->merge(['active'=> true]);
                    $getProducts = $this->index($request, true);
                    if ($getProducts->getData()->code == 200) {
                        $finalData = $finalData->merge($getProducts->getData()->data);
                    }
                }
            }

            $finalData = $finalData->skip(($page - 1) * $limit)->take($limit)->values();

            return ResponseHelper::basicResponse(
                200,
                $finalData,
                'Products retrieved successfully'
            );
        } catch (\Throwable $th) {
            return ResponseHelper::serverError(
                'An error occurred while retrieving products',
                $th->getMessage()
            );
        }
    }

    public function index(Request $request, $returnAll = false)
    {
        try {
            $active = $request->boolean('active');
            $search = $request->input('search');
            $limit = $request->integer('limit', 15);
            $page = $request->integer('page', 1);

            $products = Product::query()
                ->when($active, function ($query) use ($active) {
                    $query->where('is_active', $active);
                })
                ->when($search, function ($query) use ($search) {
                    $query->where('name', 'ilike', "%{$search}%")
                        ->orWhere('sku', 'ilike', "%{$search}%");
                });

            if ($returnAll) {
                $products = $products->get();
            } else {
                $products = $products->paginate($limit, ['*'], 'page', $page);
            }

            return ResponseHelper::basicResponse(
                200,
                $products,
                'Products retrieved successfully'
            );
        } catch (\Throwable $th) {
            return ResponseHelper::serverError(
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

            return ResponseHelper::basicResponse(
                200,
                $product,
                'Product created successfully'
            );
        } catch (\Throwable $th) {
            return ResponseHelper::serverError(
                'An error occurred while creating the product',
                $th->getMessage()
            );
        }
    }

    public function show(Product $product)
    {
        try {
            return ResponseHelper::basicResponse(
                200,
                $product,
                'Product retrieved successfully'
            );
        } catch (\Throwable $th) {
            return ResponseHelper::serverError(
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

            return ResponseHelper::basicResponse(
                200,
                $product,
                'Product updated successfully'
            );
        } catch (\Throwable $th) {
            return ResponseHelper::serverError(
                'An error occurred while updating the product',
                $th->getMessage()
            );
        }
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();

            return ResponseHelper::basicResponse(
                200,
                [],
                'Product deleted successfully'
            );
        } catch (\Throwable $th) {
            return ResponseHelper::serverError(
                'An error occurred while deleting the product',
                $th->getMessage()
            );
        }
    }
}