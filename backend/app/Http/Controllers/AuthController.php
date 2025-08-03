<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Helpers\TokenHelper;
use App\Models\Tenant;
use App\Models\User;
use App\Providers\TenantServiceProvider;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            $user = User::query()->where('email', $validated['email'])->first();
            if (!$user) {
                return ResponseHelper::basicResponse(
                    401,
                    [],
                    'Invalid email'
                );
            }

            if (!Hash::check($validated['password'], $user->password)) {
                return ResponseHelper::basicResponse(
                    401,
                    [],
                    'Invalid password'
                );
            }

            $token = TokenHelper::generateToken($user->id);

            return ResponseHelper::basicResponse(
                200,
                array_merge(
                    $user->toArray(),
                    [
                        'token' => $token,
                    ]
                ),
                'Login successful'
            );
        } catch (ValidationException $e) {
            return ResponseHelper::validationError(
                'Validation failed',
                $e->errors()
            );
        } catch (\Throwable $th) {
            return ResponseHelper::serverError(
                'An error occurred while logging in',
                $th->getMessage()
            );
        }
    }

    public function register(Request $request) 
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:users',
                'password' => 'required|string',
                'password_confirmation' => 'required|string|same:password',
                'name' => 'required|string',
                'role' => 'required|string|in:user,tenant',
                'tenant_name' => 'nullable|string',
            ]);

            $databaseName = 'tenant_' . Str::slug($validated['tenant_name'], '_');

            $existingTenant = Tenant::query()->where('database', $databaseName)->exists();
            if ($existingTenant) {
                return ResponseHelper::basicResponse(
                    400,
                    [],
                    'Tenant name already exists'
                );
            }

            $tenant = null;
            DB::transaction(function () use ($validated, $databaseName, &$tenant) {
                $tenantId = null;
                if ($validated['role'] === 'tenant') {
                    $tenant = Tenant::query()->create([
                        'name' => $validated['tenant_name'],
                        'database' => $databaseName,
                    ]);
                    $tenantId = $tenant->id;
                }

                User::query()->create([
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'name' => $validated['name'],
                    'role' => $validated['role'],
                    'tenant_id' => $tenantId,
                ]);
            });

            if ($tenant) {
                TenantServiceProvider::createTenantDatabase($tenant);
            }

            return ResponseHelper::basicResponse(
                200,
                [],
                'Register successful'
            );
        } catch (ValidationException $e) {
            return ResponseHelper::validationError(
                'Validation failed',
                $e->errors()
            );
        } catch (\Throwable $th) {
            return ResponseHelper::serverError(
                'An error occurred while registering',
                $th->getMessage()
            );
        }
    }

    public function logout(Request $request)
    {
        try {
            $token = $request->header('token');
            if (!$token) {
                return ResponseHelper::basicResponse(
                    401,
                    [],
                    'Invalid token'
                );
            }

            TokenHelper::invalidateToken($token);

            return ResponseHelper::basicResponse(
                200,
                [],
                'Logout successful'
            );
        } catch (\Throwable $th) {
            return ResponseHelper::serverError(
                'An error occurred while logging out',
                $th->getMessage()
            );
        }
    }
}