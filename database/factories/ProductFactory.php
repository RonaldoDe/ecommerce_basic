<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        // Precios
        $cost_price = $this->faker->randomFloat(2, 10, 500);
        $profit_margin = $this->faker->randomElement([0.15, 0.20, 0.25, 0.30, 0.35, 0.40]);
        $selling_price = $cost_price * (1 + $profit_margin);

        // Nombres de productos
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
        $name = $this->faker->randomElement($all_names) . ' ' . $this->faker->randomElement(['Pro', 'Elite', 'Max', 'Ultra', 'Standard', 'Plus', 'Premium', 'Basic']);
        
        // Códigos
        $code = "PROD" . $this->faker->unique()->numberBetween(1000, 9999);
        $sku = strtoupper(Str::random(3)) . '-' . $this->faker->unique()->numberBetween(10000, 99999);
        
        // Descuento (50% de probabilidad de tener descuento)
        $hasDiscount = $this->faker->boolean(50);
        $discount_percentage = $hasDiscount ? $this->faker->randomElement([5, 10, 15, 20, 25, 30, 35, 40]) : 0;
        $discount_price = $hasDiscount ? round($selling_price * (1 - $discount_percentage / 100), 2) : null;
        
        // Fechas de descuento (si tiene descuento)
        $discount_start_date = null;
        $discount_end_date = null;
        if ($hasDiscount && $this->faker->boolean(70)) { // 70% de los descuentos tienen fechas
            $discount_start_date = $this->faker->dateTimeBetween('-30 days', 'now');
            $discount_end_date = $this->faker->dateTimeBetween('now', '+60 days');
        }

        // Stock
        $stock = $this->faker->numberBetween(0, 100);
        $stock_status = $stock > 0 ? 'in_stock' : 'out_of_stock';
        
        // Tags
        $tags = $this->faker->randomElements([
            'nuevo', 'popular', 'oferta', 'limitado', 'premium', 'económico',
            'resistente', 'portátil', 'inalámbrico', 'recargable', 'impermeable'
        ], $this->faker->numberBetween(2, 5));

        // Especificaciones técnicas
        $specifications = [
            'Material' => $this->faker->randomElement(['Plástico', 'Metal', 'Aluminio', 'Fibra de carbono', 'Acero inoxidable']),
            'Color' => $this->faker->randomElement(['Negro', 'Blanco', 'Gris', 'Azul', 'Rojo', 'Verde']),
            'Garantía' => $this->faker->randomElement(['1 año', '2 años', '3 años', '6 meses']),
            'Origen' => $this->faker->randomElement(['China', 'Estados Unidos', 'Alemania', 'Japón', 'Corea del Sur']),
        ];

        // Dimensiones (en cm)
        $dimensions = [
            'length' => $this->faker->randomFloat(1, 10, 100),
            'width' => $this->faker->randomFloat(1, 10, 100),
            'height' => $this->faker->randomFloat(1, 5, 50),
        ];

        return [
            // Básicos
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory()->create()->id,
            'brand_id' => Brand::inRandomOrder()->first()?->id ?? null,
            'name' => $name,
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'code' => $code,
            'sku' => $sku,
            
            // Descripciones
            'short_description' => $this->faker->sentence($this->faker->numberBetween(8, 15)),
            'long_description' => $this->faker->paragraph($this->faker->numberBetween(3, 6), true),
            
            // SEO
            'meta_title' => $name . ' - ' . $this->faker->words(3, true),
            'meta_description' => $this->faker->sentence(15),
            'meta_keywords' => implode(', ', $this->faker->words(8)),
            
            // Precios
            'cost_price' => $cost_price,
            'selling_price' => round($selling_price, 2),
            'discount_percentage' => $discount_percentage,
            'discount_price' => $discount_price,
            'discount_start_date' => $discount_start_date,
            'discount_end_date' => $discount_end_date,
            
            // Inventario
            'stock' => $stock,
            'stock_alert' => $this->faker->numberBetween(5, 15),
            'manage_stock' => true,
            'stock_status' => $stock_status,
            
            // Dimensiones y peso
            'weight' => $this->faker->randomFloat(2, 0.1, 50), // en kg
            'dimensions' => $dimensions,
            
            // Estado
            'status' => $this->faker->boolean(90), // 90% activos
            'featured' => $this->faker->boolean(20), // 20% destacados
            'is_new' => $this->faker->boolean(30), // 30% nuevos
            'visibility' => $this->faker->randomElement(['public', 'public', 'public', 'catalog', 'private']), // Mayoría públicos
            
            // Ratings y métricas
            'rating' => $this->faker->randomFloat(2, 3, 5), // Rating entre 3 y 5
            'reviews_count' => $this->faker->numberBetween(0, 150),
            'views_count' => $this->faker->numberBetween(0, 1000),
            'sales_count' => $this->faker->numberBetween(0, 200),
            'wishlist_count' => $this->faker->numberBetween(0, 50),
            
            // Variantes
            'has_variants' => false, // Por defecto sin variantes
            'parent_id' => null,
            
            // Información adicional
            'warranty' => $this->faker->randomElement([
                'Garantía de fábrica de 1 año',
                'Garantía limitada de 2 años',
                'Sin garantía',
                '6 meses de garantía contra defectos de fabricación',
                'Garantía extendida disponible',
            ]),
            'return_policy' => $this->faker->randomElement([
                'Devoluciones aceptadas dentro de 30 días',
                'Política de devolución de 15 días',
                'No se aceptan devoluciones en productos de oferta',
                'Devolución gratuita dentro de 60 días',
            ]),
            'shipping_info' => $this->faker->randomElement([
                'Envío gratis en pedidos superiores a $50',
                'Envío estándar: 3-5 días hábiles',
                'Envío express disponible',
                'Envío gratis en todo el país',
                'Recogida en tienda disponible',
            ]),
            'specifications' => $specifications,
            'tags' => implode(',', $tags),
            'search_keywords' => implode(', ', array_merge(
                explode(' ', strtolower($name)),
                $tags,
                [$this->faker->word(), $this->faker->word()]
            )),
            
            // Publicación
            'published_at' => $this->faker->boolean(95) ? $this->faker->dateTimeBetween('-6 months', 'now') : null,
        ];
    }

    /**
     * Producto destacado
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
            'status' => true,
            'visibility' => 'public',
        ]);
    }

    /**
     * Producto nuevo (renamed from new() to avoid conflict)
     */
    public function newProduct(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_new' => true,
            'status' => true,
            'visibility' => 'public',
            'published_at' => now()->subDays($this->faker->numberBetween(1, 30)),
        ]);
    }

    /**
     * Producto en oferta
     */
    public function onSale(): static
    {
        return $this->state(function (array $attributes) {
            $discount = $this->faker->numberBetween(10, 50);
            return [
                'discount_percentage' => $discount,
                'discount_price' => round($attributes['selling_price'] * (1 - $discount / 100), 2),
                'discount_start_date' => now()->subDays($this->faker->numberBetween(1, 30)),
                'discount_end_date' => now()->addDays($this->faker->numberBetween(7, 60)),
            ];
        });
    }

    /**
     * Producto sin stock
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
            'stock_status' => 'out_of_stock',
        ]);
    }

    /**
     * Producto con stock bajo
     */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => $this->faker->numberBetween(1, 5),
            'stock_status' => 'in_stock',
        ]);
    }

    /**
     * Producto popular
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $this->faker->randomFloat(2, 4.5, 5),
            'reviews_count' => $this->faker->numberBetween(50, 300),
            'views_count' => $this->faker->numberBetween(500, 5000),
            'sales_count' => $this->faker->numberBetween(100, 500),
            'wishlist_count' => $this->faker->numberBetween(50, 200),
            'featured' => true,
        ]);
    }

    /**
     * Producto inactivo
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
            'visibility' => 'private',
        ]);
    }

    /**
     * Producto con descuento temporal
     */
    public function flashSale(): static
    {
        return $this->state(function (array $attributes) {
            $discount = $this->faker->numberBetween(30, 70);
            return [
                'discount_percentage' => $discount,
                'discount_price' => round($attributes['selling_price'] * (1 - $discount / 100), 2),
                'discount_start_date' => now(),
                'discount_end_date' => now()->addHours($this->faker->numberBetween(6, 72)),
                'featured' => true,
            ];
        });
    }

    /**
     * Producto económico
     */
    public function budget(): static
    {
        return $this->state(fn (array $attributes) => [
            'cost_price' => $this->faker->randomFloat(2, 5, 30),
            'selling_price' => $this->faker->randomFloat(2, 10, 50),
        ]);
    }

    /**
     * Producto premium
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'cost_price' => $this->faker->randomFloat(2, 200, 1000),
            'selling_price' => $this->faker->randomFloat(2, 300, 1500),
            'featured' => true,
            'rating' => $this->faker->randomFloat(2, 4.5, 5),
        ]);
    }
}