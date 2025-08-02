<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tenant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();
        $database = 'tenant_' . Str::slug($name, '_') . '_' . fake()->unique()->randomNumber(4);

        return [
            'name' => $name,
            'database' => $database,
        ];
    }

    /**
     * Create a tenant with a specific name.
     */
    public function withName(string $name): static
    {
        $database = 'tenant_' . Str::slug($name, '_') . '_' . fake()->unique()->randomNumber(4);
        
        return $this->state(fn (array $attributes) => [
            'name' => $name,
            'database' => $database,
        ]);
    }

    /**
     * Create a tenant with a specific database name.
     */
    public function withDatabase(string $database): static
    {
        return $this->state(fn (array $attributes) => [
            'database' => $database,
        ]);
    }

    /**
     * Create a tenant with both name and database specified.
     */
    public function withNameAndDatabase(string $name, string $database): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
            'database' => $database,
        ]);
    }
}