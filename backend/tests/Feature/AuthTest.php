<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--database' => 'testing']);
    }

    public function test_user_can_register_successfully()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
            'tenant_id' => null,
        ];

        $response = $this->postJson('/register', $userData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Register successful',
                     'data' => [],
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'name' => $userData['name'],
            'role' => $userData['role'],
        ]);
    }

    public function test_user_registration_fails_with_invalid_data()
    {
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'different',
            'role' => 'invalid_role',
        ];

        $response = $this->postJson('/register', $userData);

        $response->assertStatus(422)
                 ->assertJson([
                     'message' => 'Validation failed',
                 ]);
    }

    public function test_user_registration_fails_with_duplicate_email()
    {
        $existingUser = User::factory()->create([
            'email' => 'test@example.com'
        ]);

        $userData = [
            'name' => $this->faker->name,
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
        ];

        $response = $this->postJson('/register', $userData);

        $response->assertStatus(422);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/login', $loginData);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Login successful',
                 ])
                 ->assertJsonStructure([
                     'data' => [
                         'user' => ['id', 'name', 'email'],
                         'token'
                     ]
                 ]);

        $this->assertDatabaseHas('user_tokens', [
            'user_id' => $user->id,
        ]);
    }

    public function test_user_login_fails_with_invalid_email()
    {
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/login', $loginData);

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Invalid email',
                 ]);
    }

    public function test_user_login_fails_with_invalid_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correct_password'),
        ]);

        $loginData = [
            'email' => 'test@example.com',
            'password' => 'wrong_password',
        ];

        $response = $this->postJson('/login', $loginData);

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Invalid password',
                 ]);
    }

    public function test_user_login_fails_with_validation_errors()
    {
        $loginData = [
            'email' => 'invalid-email',
            'password' => '',
        ];

        $response = $this->postJson('/login', $loginData);

        $response->assertStatus(422)
                 ->assertJson([
                     'message' => 'Validation failed',
                 ]);
    }

    public function test_authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        $token = UserToken::factory()->create([
            'user_id' => $user->id,
            'token' => 'valid_token_123',
            'expires_at' => now()->addDays(1),
        ]);

        $response = $this->postJson('/logout', [], [
            'token' => 'valid_token_123',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Logout successful',
                 ]);

        $this->assertDatabaseHas('user_tokens', [
            'token' => 'valid_token_123',
            'expires_at' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    public function test_logout_fails_without_token()
    {
        $response = $this->postJson('/logout');

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Invalid token',
                 ]);
    }

    public function test_logout_requires_authentication_middleware()
    {
        $response = $this->postJson('/logout', [], [
            'ctoken' => 'invalid_token',
        ]);

        $response->assertStatus(401);
    }
}