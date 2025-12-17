<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $name = $this->faker->unique()->randomElement([
            'Comida', 'Bebida', 'Limpieza', 'Diversión', 'Otros', 'Electrónica', 'Electrodomésticos', 'Accesorios', 'Ropa', 'Mascotas', 'Deportes', 'Juguetes', 'Hogar', 'Casa', 'Moda',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->optional(0.7)->sentence(15),
        ];
    }
}
