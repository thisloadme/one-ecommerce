<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--database' => 'testing']);
    }

    public function test_login_middleware_allows_valid_token()
    {
        $user = User::factory()->create();
        $token = UserToken::factory()->create([
            'user_id' => $user->id,
            'token' => 'valid_token_123',
            'expires_at' => now()->addDays(1),
        ]);

        $response = $this->getJson('/tenants', [
            'ctoken' => 'valid_token_123',
        ]);

        $response->assertStatus(200);
    }

    public function test_login_middleware_blocks_invalid_token()
    {
        $response = $this->getJson('/tenants', [
            'ctoken' => 'invalid_token_123',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_middleware_blocks_expired_token()
    {
        $user = User::factory()->create();
        $token = UserToken::factory()->create([
            'user_id' => $user->id,
            'token' => 'expired_token_123',
            'expires_at' => now()->subDays(1),
        ]);

        $response = $this->getJson('/tenants', [
            'ctoken' => 'expired_token_123',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_middleware_blocks_missing_token()
    {
        $response = $this->getJson('/tenants');

        $response->assertStatus(401);
    }

    public function test_tenant_middleware_allows_valid_tenant()
    {
        $user = User::factory()->create();
        $token = UserToken::factory()->create([
            'user_id' => $user->id,
            'token' => 'valid_token_123',
            'expires_at' => now()->addDays(1),
        ]);

        $tenant = Tenant::factory()->create([
            'database' => 'test_tenant_db',
        ]);

        $response = $this->getJson('/tenant/products', [
            'ctoken' => 'valid_token_123',
            'Host' => 'test_tenant_db',
        ]);

        // Should not return 404 (tenant not found)
        $this->assertNotEquals(404, $response->status());
    }

    public function test_tenant_middleware_blocks_invalid_tenant()
    {
        $user = User::factory()->create();
        $token = UserToken::factory()->create([
            'user_id' => $user->id,
            'token' => 'valid_token_123',
            'expires_at' => now()->addDays(1),
        ]);

        $response = $this->getJson('/tenant/products', [
            'ctoken' => 'valid_token_123',
            'Host' => 'nonexistent_tenant',
        ]);

        $response->assertStatus(404);
    }

    public function test_tenant_middleware_blocks_missing_tenant_header()
    {
        $user = User::factory()->create();
        $token = UserToken::factory()->create([
            'user_id' => $user->id,
            'token' => 'valid_token_123',
            'expires_at' => now()->addDays(1),
        ]);

        $response = $this->getJson('/tenant/products', [
            'ctoken' => 'valid_token_123',
        ]);

        $response->assertStatus(404);
    }

    public function test_middleware_chain_works_correctly()
    {
        // Test that both login and tenant middleware work together
        $user = User::factory()->create();
        $token = UserToken::factory()->create([
            'user_id' => $user->id,
            'token' => 'valid_token_123',
            'expires_at' => now()->addDays(1),
        ]);

        $tenant = Tenant::factory()->create([
            'database' => 'test_tenant_db',
        ]);

        // Valid token + valid tenant should work
        $response = $this->getJson('/tenant/products', [
            'ctoken' => 'valid_token_123',
            'Host' => 'test_tenant_db',
        ]);

        $this->assertNotEquals(401, $response->status()); // Not unauthorized
        $this->assertNotEquals(404, $response->status()); // Not tenant not found
    }

    public function test_middleware_chain_fails_with_invalid_token_but_valid_tenant()
    {
        $tenant = Tenant::factory()->create([
            'database' => 'test_tenant_db',
        ]);

        $response = $this->getJson('/tenant/products', [
            'ctoken' => 'invalid_token_123',
            'Host' => 'test_tenant_db',
        ]);

        $response->assertStatus(401); // Should fail at login middleware
    }

    public function test_middleware_chain_fails_with_valid_token_but_invalid_tenant()
    {
        $user = User::factory()->create();
        $token = UserToken::factory()->create([
            'user_id' => $user->id,
            'token' => 'valid_token_123',
            'expires_at' => now()->addDays(1),
        ]);

        $response = $this->getJson('/tenant/products', [
            'ctoken' => 'valid_token_123',
            'Host' => 'nonexistent_tenant',
        ]);

        $response->assertStatus(404); // Should fail at tenant middleware
    }

    public function test_routes_without_middleware_are_accessible()
    {
        // Test that routes without middleware (like login, register) are accessible
        $response = $this->postJson('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Should not be blocked by middleware (though login might fail due to invalid credentials)
        $this->assertNotEquals(401, $response->status());
    }

    public function test_login_middleware_header_case_insensitive()
    {
        $user = User::factory()->create();
        $token = UserToken::factory()->create([
            'user_id' => $user->id,
            'token' => 'valid_token_123',
            'expires_at' => now()->addDays(1),
        ]);

        // Test with different header case
        $response = $this->getJson('/tenants', [
            'CTOKEN' => 'valid_token_123',
        ]);

        // Laravel normalizes headers, so this should work
        $response->assertStatus(200);
    }

    public function test_tenant_middleware_sets_tenant_in_request()
    {
        $user = User::factory()->create();
        $token = UserToken::factory()->create([
            'user_id' => $user->id,
            'token' => 'valid_token_123',
            'expires_at' => now()->addDays(1),
        ]);

        $tenant = Tenant::factory()->create([
            'name' => 'Test Tenant',
            'database' => 'test_tenant_db',
        ]);

        $response = $this->getJson('/tenant/', [
            'ctoken' => 'valid_token_123',
            'Host' => 'test_tenant_db',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'name' => 'Test Tenant',
                 ]);
    }

    public function test_multiple_valid_tokens_for_same_user()
    {
        $user = User::factory()->create();
        
        $token1 = UserToken::factory()->create([
            'user_id' => $user->id,
            'token' => 'token_1',
            'expires_at' => now()->addDays(1),
        ]);
        
        $token2 = UserToken::factory()->create([
            'user_id' => $user->id,
            'token' => 'token_2',
            'expires_at' => now()->addDays(1),
        ]);

        // Both tokens should work
        $response1 = $this->getJson('/tenants', ['ctoken' => 'token_1']);
        $response2 = $this->getJson('/tenants', ['ctoken' => 'token_2']);

        $response1->assertStatus(200);
        $response2->assertStatus(200);
    }

    public function test_token_expiration_boundary()
    {
        $user = User::factory()->create();
        
        // Token that expires exactly now
        $token = UserToken::factory()->create([
            'user_id' => $user->id,
            'token' => 'boundary_token',
            'expires_at' => now(),
        ]);

        $response = $this->getJson('/tenants', [
            'ctoken' => 'boundary_token',
        ]);

        // Should be unauthorized since token expires exactly now
        $response->assertStatus(401);
    }
}