<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->company();
        
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'description' => $this->faker->sentence(10),
            'logo' => null,
            'website' => $this->faker->boolean(70) ? $this->faker->url() : null,
            'status' => true,
            'order' => $this->faker->numberBetween(0, 100),
            'meta_title' => $name . ' - ' . $this->faker->words(3, true),
            'meta_description' => $this->faker->sentence(15),
        ];
    }

    /**
     * Marca inactiva
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }
}