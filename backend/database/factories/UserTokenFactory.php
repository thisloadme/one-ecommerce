<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserToken>
 */
class UserTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserToken::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'token' => Str::random(32),
            'expires_at' => now()->addDays(2),
        ];
    }

    /**
     * Create a token for a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create a token with a specific token string.
     */
    public function withToken(string $token): static
    {
        return $this->state(fn (array $attributes) => [
            'token' => $token,
        ]);
    }

    /**
     * Create an expired token.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDays(1),
        ]);
    }

    /**
     * Create a token that expires soon.
     */
    public function expiresSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->addMinutes(30),
        ]);
    }

    /**
     * Create a long-lived token.
     */
    public function longLived(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->addDays(30),
        ]);
    }

    /**
     * Create a token with a specific expiration date.
     */
    public function expiresAt(\DateTime $expiresAt): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Create a valid token (not expired).
     */
    public function valid(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->addDays(1),
        ]);
    }
}