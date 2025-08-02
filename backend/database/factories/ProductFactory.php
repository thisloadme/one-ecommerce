<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);
        $sku = strtoupper(Str::random(3)) . '-' . fake()->unique()->randomNumber(3);

        return [
            'name' => ucwords($name),
            'description' => fake()->paragraph(3),
            'price' => fake()->randomFloat(2, 10, 1000),
            'stock' => fake()->numberBetween(0, 100),
            'sku' => $sku,
            'is_active' => fake()->boolean(80), // 80% chance of being active
        ];
    }

    /**
     * Create an active product.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Create an inactive product.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a product with specific stock.
     */
    public function withStock(int $stock): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => $stock,
        ]);
    }

    /**
     * Create an out of stock product.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    /**
     * Create a product with a specific price.
     */
    public function withPrice(float $price): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $price,
        ]);
    }

    /**
     * Create a product with a specific SKU.
     */
    public function withSku(string $sku): static
    {
        return $this->state(fn (array $attributes) => [
            'sku' => $sku,
        ]);
    }

    /**
     * Create a product with a specific name.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }

    /**
     * Create an expensive product (price > 500).
     */
    public function expensive(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->randomFloat(2, 500, 2000),
        ]);
    }

    /**
     * Create a cheap product (price < 50).
     */
    public function cheap(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->randomFloat(2, 1, 50),
        ]);
    }
}