<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cost_price = $this->faker->randomFloat(2, 10, 500);
        
        $profit_margin = $this->faker->randomElement([0.15, 0.20, 0.25, 0.30, 0.35, 0.40]);
        $selling_price = $cost_price * (1 + $profit_margin);

        $code = "PROD" . $this->faker->unique()->numberBetween(1000, 9999);

        $electronic_names = [
            'Laptop', 'Smartphone', 'Tablet', 'Headphones', 'Smartwatch',
            'Camera', 'Speaker', 'Monitor', 'Keyboard', 'Mouse'
        ];

        $clothing_names = [
            'T-Shirt', 'Jeans', 'Dress', 'Jacket', 'Sneakers',
            'Hat', 'Scarf', 'Socks', 'Underwear', 'Swimsuit'
        ];

        $household_names = [
            'Vacuum Cleaner', 'Microwave', 'Refrigerator', 'Washing Machine', 'Blender',
            'Toaster', 'Coffee Maker', 'Iron', 'Hair Dryer', 'Fan'
        ];

        $sports_names = [
            'Football', 'Basketball', 'Tennis Racket', 'Yoga Mat', 'Dumbbell',
            'Running Shoes', 'Baseball Glove', 'Cycling Helmet', 'Swimming Goggles', 'Badminton Shuttlecock'
        ];
        
        $all_names = array_merge($electronic_names, $clothing_names, $household_names, $sports_names);
        $name = $this->faker->randomElement($all_names). ' '. $this->faker->randomElement(['Pro', 'Elite', 'Max', 'Ultra', 'Standard']);
        return [
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory()->create()->id,
            'name' => $name,
            'code' => $code,
            'short_description' => $this->faker->sentence(8),
            'long_description' => $this->faker->paragraph(3, true),
            'cost_price' => $cost_price,
            'selling_price' => round($selling_price, 2),
            'stock' => $this->faker->numberBetween(0, 100),
        ];
    }
}
