<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\UserToken;
use App\Providers\ResponseServiceProvider;
use Crypt;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

            $encryptedPassword = Hash::make($validated['password']);
            if ($encryptedPassword != $user->password) {
                return ResponseHelper::basicResponse(
                    401,
                    [],
                    'Invalid password'
                );
            }

            $token = TokenHelper::generateToken($user->id);

            return ResponseHelper::basicResponse(
                200,
                [
                    'user' => $user,
                    'token' => $token,
                ],
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
                'tenant_id' => 'nullable|integer',
            ]);

            User::query()->create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'name' => $validated['name'],
                'role' => $validated['role'],
                'tenant_id' => $validated['tenant_id'],
            ]);

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