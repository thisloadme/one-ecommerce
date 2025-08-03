<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Create a valid authentication token for testing
     */
    protected function createAuthToken($user = null): string
    {
        if (!$user) {
            $user = \App\Models\User::factory()->create();
        }

        $token = \App\Models\UserToken::factory()->create([
            'user_id' => $user->id,
            'token' => 'test_token_' . uniqid(),
            'expires_at' => now()->addDays(1),
        ]);

        return $token->token;
    }

    /**
     * Create authenticated headers for API requests
     */
    protected function authHeaders($token = null): array
    {
        if (!$token) {
            $token = $this->createAuthToken();
        }

        return [
            'ctoken' => $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Create tenant headers for API requests
     */
    protected function tenantHeaders($tenantDatabase = null, $token = null): array
    {
        if (!$tenantDatabase) {
            $tenant = \App\Models\Tenant::factory()->create();
            $tenantDatabase = $tenant->database;
        }

        $headers = $this->authHeaders($token);
        $headers['Host'] = $tenantDatabase;

        return $headers;
    }

    /**
     * Assert that a JSON response has the expected structure
     */
    protected function assertJsonResponseStructure($response, $expectedStructure = null)
    {
        $defaultStructure = [
            'data',
            'message',
        ];

        $structure = $expectedStructure ?: $defaultStructure;
        
        $response->assertJsonStructure($structure);
    }

    /**
     * Assert that a response is a successful API response
     */
    protected function assertSuccessfulResponse($response, $message = null)
    {
        $response->assertStatus(200);
        
        if ($message) {
            $response->assertJson(['message' => $message]);
        }
        
        $this->assertJsonResponseStructure($response);
    }

    /**
     * Assert that a response is an error response
     */
    protected function assertErrorResponse($response, $statusCode, $message = null)
    {
        $response->assertStatus($statusCode);
        
        if ($message) {
            $response->assertJson(['message' => $message]);
        }
    }

    /**
     * Assert that a response is a validation error
     */
    protected function assertValidationError($response, $field = null)
    {
        $response->assertStatus(422);
        $response->assertJson(['message' => 'Validation failed']);
        
        if ($field) {
            $response->assertJsonValidationErrors($field);
        }
    }

    /**
     * Create test data for product
     */
    protected function createProductData($overrides = []): array
    {
        return array_merge([
            'name' => 'Test Product',
            'description' => 'Test product description',
            'price' => 99.99,
            'stock' => 10,
            'sku' => 'TEST-' . uniqid(),
            'is_active' => true,
        ], $overrides);
    }

    /**
     * Create test data for user registration
     */
    protected function createUserData($overrides = []): array
    {
        return array_merge([
            'name' => 'Test User',
            'email' => 'test' . uniqid() . '@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
            'tenant_id' => null,
        ], $overrides);
    }

    /**
     * Create test data for user login
     */
    protected function createLoginData($overrides = []): array
    {
        return array_merge([
            'email' => 'test@example.com',
            'password' => 'password123',
        ], $overrides);
    }
}
