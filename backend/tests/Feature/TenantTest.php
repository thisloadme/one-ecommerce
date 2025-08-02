<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TenantTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--database' => 'testing']);
        
        // Create authenticated user and token
        $this->user = User::factory()->create();
        $this->token = UserToken::factory()->create([
            'user_id' => $this->user->id,
            'token' => 'valid_test_token',
            'expires_at' => now()->addDays(1),
        ]);
    }

    protected function authenticatedHeaders()
    {
        return [
            'ctoken' => 'valid_test_token',
        ];
    }

    public function test_authenticated_user_can_get_tenants_list()
    {
        $tenants = Tenant::factory()->count(3)->create();

        $response = $this->getJson('/tenants', $this->authenticatedHeaders());

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Tenants retrieved successfully',
                 ])
                 ->assertJsonCount(3, 'data');
    }

    public function test_unauthenticated_user_cannot_access_tenants_list()
    {
        $response = $this->getJson('/tenants');

        $response->assertStatus(401);
    }

    public function test_tenants_list_can_be_searched_by_name()
    {
        $tenant1 = Tenant::factory()->create(['name' => 'Test Company']);
        $tenant2 = Tenant::factory()->create(['name' => 'Another Business']);
        $tenant3 = Tenant::factory()->create(['name' => 'Test Store']);

        $response = $this->getJson('/tenants?search=Test', $this->authenticatedHeaders());

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data')
                 ->assertJsonFragment(['name' => 'Test Company'])
                 ->assertJsonFragment(['name' => 'Test Store'])
                 ->assertJsonMissing(['name' => 'Another Business']);
    }

    public function test_tenants_list_returns_empty_array_when_no_matches()
    {
        Tenant::factory()->count(3)->create();

        $response = $this->getJson('/tenants?search=NonExistentTenant', $this->authenticatedHeaders());

        $response->assertStatus(200)
                 ->assertJsonCount(0, 'data');
    }

    public function test_authenticated_user_can_get_specific_tenant()
    {
        $tenant = Tenant::factory()->create([
            'name' => 'Test Tenant',
            'database' => 'test_tenant_db',
        ]);

        $response = $this->getJson("/tenants/{$tenant->id}", $this->authenticatedHeaders());

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Tenant retrieved successfully',
                     'data' => [
                         'id' => $tenant->id,
                         'name' => 'Test Tenant',
                         'database' => 'test_tenant_db',
                     ]
                 ]);
    }

    public function test_unauthenticated_user_cannot_access_specific_tenant()
    {
        $tenant = Tenant::factory()->create();

        $response = $this->getJson("/tenants/{$tenant->id}");

        $response->assertStatus(401);
    }

    public function test_get_tenant_returns_404_for_nonexistent_tenant()
    {
        $response = $this->getJson('/tenants/999', $this->authenticatedHeaders());

        $response->assertStatus(404);
    }

    public function test_tenants_list_handles_server_errors_gracefully()
    {
        // Mock a scenario that would cause a server error
        $this->mock(Tenant::class, function ($mock) {
            $mock->shouldReceive('query')->andThrow(new \Exception('Database error'));
        });

        $response = $this->getJson('/tenants', $this->authenticatedHeaders());

        $response->assertStatus(500)
                 ->assertJson([
                     'message' => 'An error occurred while retrieving tenants',
                 ]);
    }

    public function test_get_tenant_handles_server_errors_gracefully()
    {
        $tenant = Tenant::factory()->create();
        
        // Force an error by using an invalid tenant ID format
        $response = $this->getJson('/tenants/invalid-id', $this->authenticatedHeaders());

        // This should trigger model binding failure or server error
        $this->assertTrue(in_array($response->status(), [404, 500]));
    }

    public function test_expired_token_cannot_access_tenants()
    {
        $expiredToken = UserToken::factory()->create([
            'user_id' => $this->user->id,
            'token' => 'expired_token',
            'expires_at' => now()->subDays(1),
        ]);

        $response = $this->getJson('/tenants', [
            'ctoken' => 'expired_token',
        ]);

        $response->assertStatus(401);
    }

    public function test_invalid_token_cannot_access_tenants()
    {
        $response = $this->getJson('/tenants', [
            'ctoken' => 'invalid_token_123',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test tenant info endpoint with valid tenant
     */
    public function test_tenant_info_success()
    {
        $tenant = Tenant::factory()->create();
        $headers = $this->authenticatedHeaders();
        $headers['Host'] = $tenant->database;

        $response = $this->getJson('/tenant/', $headers);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Tenant info retrieved successfully',
                     'data' => [
                         'id' => $tenant->id,
                         'name' => $tenant->name,
                     ],
                 ]);
    }

    /**
     * Test tenant info endpoint without authentication
     */
    public function test_tenant_info_requires_authentication()
    {
        $response = $this->getJson('/tenant/', [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test tenant info endpoint without tenant header
     */
    public function test_tenant_info_requires_tenant_header()
    {
        $headers = $this->authenticatedHeaders();

        $response = $this->getJson('/tenant/', $headers);

        $response->assertStatus(404);
    }

    /**
     * Test tenant info endpoint with invalid tenant
     */
    public function test_tenant_info_with_invalid_tenant()
    {
        $headers = $this->authenticatedHeaders();
        $headers['Host'] = 'nonexistent_tenant';

        $response = $this->getJson('/tenant/', $headers);

        $response->assertStatus(404);
    }
}