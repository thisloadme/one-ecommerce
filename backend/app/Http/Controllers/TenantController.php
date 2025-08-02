<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Tenant;
use Illuminate\Http\Request;

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

    public function show(Request $request)
    {
        try {
            $tenant = Tenant::query()->find($request->tenant);
            if (!$tenant) {
                return ResponseHelper::basicResponse(
                    404,
                    [],
                    'Tenant not found'
                );
            }

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