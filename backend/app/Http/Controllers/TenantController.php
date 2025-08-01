<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserToken;
use App\Providers\ResponseServiceProvider;
use Crypt;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');

            $products = Tenant::query()
                ->when($search, function ($query) use ($search) {
                    $query->where('name', 'ilike', "%{$search}%");
                })
                ->get();

            return ResponseHelper::basicResponse(
                200,
                $products,
                'Tenants retrieved successfully'
            );
        } catch (\Throwable $th) {
            return ResponseHelper::serverError(
                'An error occurred while retrieving tenants',
                $th->getMessage()
            );
        }
    }

    public function show(Tenant $tenant)
    {
        try {
            return ResponseHelper::basicResponse(
                200,
                $tenant,
                'Tenant retrieved successfully'
            );
        } catch (\Throwable $th) {
            return ResponseHelper::serverError(
                'An error occurred while retrieving the tenant',
                $th->getMessage()
            );
        }
    }
}