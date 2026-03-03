<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Ajuste;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ==================== ROLES ====================
        Role::create(['name' => 'SUPER ADMINISTRADOR', 'guard_name' => 'web']);
        Role::create(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        Role::create(['name' => 'USUARIO', 'guard_name' => 'web']);
        Role::create(['name' => 'VENDEDOR', 'guard_name' => 'web']);
        Role::create(['name' => 'OPERADOR', 'guard_name' => 'web']);
        Role::create(['name' => 'CLIENTE', 'guard_name' => 'web']);

        // ==================== USUARIOS ====================
        // Usuario Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
        ])->assignRole('SUPER ADMINISTRADOR');

        // Usuarios clientes para las reseñas
        $clientUsers = [];
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'name' => "Cliente $i",
                'email' => "cliente$i@example.com",
                'password' => bcrypt('123456'),
            ]);
            $user->assignRole('CLIENTE');
            $clientUsers[] = $user;

            // 1 a 3 direcciones por cliente
            $addressesCount = rand(1, 3);

            for ($j = 1; $j <= $addressesCount; $j++) {
                Address::create([
                    'user_id'     => $user->id,
                    'label'           => $j === 1 ? 'Casa' : 'Oficina',
                    'recipient_name'  => $user->name,
                    'phone'           => fake()->phoneNumber(),

                    'address_line_1'  => fake()->streetAddress(),
                    'address_line_2'  => fake()->secondaryAddress(),
                    'city'            => fake()->city(),
                    'state'           => fake()->state(),
                    'postal_code'     => fake()->postcode(),
                    'country'         => 'CO',

                    'reference'       => fake()->sentence(),
                    'is_default'      => $j === 1, // primera dirección por defecto
                ]);
            }
        }



        // ==================== AJUSTES ====================
        Ajuste::create([
            'name' => 'SoyRonal2 SAS',
            'description' => 'Una empresa que sabe vender',
            'branch' => 'Sucursal 1',
            'address' => 'Los angeles 3 cali-fornia',
            'phones' => '12356489',
            'email' => 'admin@admin.com',
            'logo' => 'logo.jpg',
            'image_login' => 'login.jpg',
            'badge' => '$',
        ]);

        // ==================== MARCAS ====================
        Brand::factory(15)->create();

        // ==================== CATEGORÍAS ====================
        Category::factory(15)->create();

        // ==================== PRODUCTOS ====================
        echo "Creando productos...\n";
        
        // Productos regulares
        $regularProducts = Product::factory(30)->create();
        echo "✓ 30 productos regulares creados\n";
        
        // Productos destacados
        $featuredProducts = Product::factory(10)->featured()->create();
        echo "✓ 10 productos destacados creados\n";
        
        // Productos nuevos
        $newProducts = Product::factory(15)->newProduct()->create();
        echo "✓ 15 productos nuevos creados\n";
        
        // Productos en oferta
        $saleProducts = Product::factory(20)->onSale()->create();
        echo "✓ 20 productos en oferta creados\n";
        
        // Productos populares
        $popularProducts = Product::factory(8)->popular()->create();
        echo "✓ 8 productos populares creados\n";
        
        // Productos sin stock
        $outOfStockProducts = Product::factory(5)->outOfStock()->create();
        echo "✓ 5 productos sin stock creados\n";
        
        // Productos con stock bajo
        $lowStockProducts = Product::factory(10)->lowStock()->create();
        echo "✓ 10 productos con stock bajo creados\n";
        
        // Flash sales
        $flashSaleProducts = Product::factory(5)->flashSale()->create();
        echo "✓ 5 productos en flash sale creados\n";
        
        // Productos económicos
        $budgetProducts = Product::factory(15)->budget()->create();
        echo "✓ 15 productos económicos creados\n";
        
        // Productos premium
        $premiumProducts = Product::factory(8)->premium()->create();
        echo "✓ 8 productos premium creados\n";
        
        // Productos inactivos
        $inactiveProducts = Product::factory(3)->inactive()->create();
        echo "✓ 3 productos inactivos creados\n";

        // ==================== RESEÑAS ====================
        echo "\nCreando reseñas...\n";
        
        // Obtener todos los productos activos
        $allProducts = Product::where('status', true)->get();
        
        $comments = [
            'Excelente producto, muy recomendado. La calidad es superior a lo que esperaba.',
            'Buena calidad precio. Cumple con lo prometido.',
            'No cumplió mis expectativas. Esperaba algo mejor por el precio.',
            'Mejor de lo que esperaba. Muy satisfecho con la compra.',
            'Producto como se describe. Sin sorpresas, todo perfecto.',
            'Muy satisfecho con la compra. Lo recomendaría sin dudarlo.',
            'La entrega fue rápida y el producto llegó bien empaquetado.',
            'El producto llegó en perfectas condiciones. Excelente servicio.',
            'Cumple su función perfectamente. Buen producto.',
            'Relación calidad-precio excelente. Volveré a comprar.',
            'Después de usarlo por unas semanas, puedo decir que es muy bueno.',
            'Exactamente lo que buscaba. Muy contento con la compra.',
        ];

        $titles = [
            'Excelente compra',
            'Muy recomendado',
            'Buena inversión',
            'Cumple expectativas',
            'Gran producto',
            'Satisfecho',
            'Vale la pena',
            'Buen producto',
            'Me gustó',
            'Recomendado',
        ];

        $reviewCount = 0;
        foreach ($allProducts as $product) {
            // Crear entre 2 y 5 reseñas por producto
            $numReviews = rand(2, 5);
            
            for ($i = 0; $i < $numReviews; $i++) {
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $clientUsers[array_rand($clientUsers)]->id,
                    'title' => $titles[array_rand($titles)],
                    'comment' => $comments[array_rand($comments)],
                    'rating' => rand(3, 5),
                    'status' => 'approved',
                    'verified_purchase' => rand(0, 1) === 1,
                    'helpful_count' => rand(0, 15),
                    'not_helpful_count' => rand(0, 3),
                ]);
                $reviewCount++;
            }
            
            // Actualizar el rating del producto
            $product->updateRating();
        }
        
        echo "✓ $reviewCount reseñas creadas\n";

        // ==================== RESUMEN ====================
        echo "\n========================================\n";
        echo "SEEDING COMPLETADO\n";
        echo "========================================\n";
        echo "Roles: 6\n";
        echo "Usuarios: " . User::count() . "\n";
        echo "Marcas: " . Brand::count() . "\n";
        echo "Categorías: " . Category::count() . "\n";
        echo "Productos: " . Product::count() . "\n";
        echo "Reseñas: " . Review::count() . "\n";
        echo "========================================\n";
        echo "Usuario admin:\n";
        echo "Email: admin@gmail.com\n";
        echo "Password: 123456\n";
        echo "\nUsuarios clientes:\n";
        echo "Email: cliente1@example.com hasta cliente10@example.com\n";
        echo "Password: 123456\n";
        echo "========================================\n";
    }
}