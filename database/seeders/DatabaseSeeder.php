<?php

namespace Database\Seeders;

use App\Models\Ajuste;
use App\Models\Category;
use App\Models\Product;
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
        // User::factory(10)->create();

        Role::create(['name' => 'SUPER ADMINISTRADOR', 'guard_name' => 'web']);
        Role::create(['name' => 'ADMINISTRADOR', 'guard_name' => 'web']);
        Role::create(['name' => 'USUARIO', 'guard_name' => 'web']);
        Role::create(['name' => 'VENDEDOR', 'guard_name' => 'web']);
        Role::create(['name' => 'OPERADOR', 'guard_name' => 'web']);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
        ])->assignRole('SUPER ADMINISTRADOR');

        Ajuste::create([
            'name' => 'SoyRonal2 SAS',
            'description' => 'Una empresa que sabe vender',
            'branch' => 'Suscursal 1',
            'address' => 'Los angeles 3 cali-fornia',
            'phones' => '12356489',
            'email' => 'admin@admin.com',
            'logo' => 'logo.jpg',
            'image_login' => 'login.jpg',
            'badge' => '$',
        ]);

        Category::factory(15)->create();
        Product::factory(50)->create();

    }
}
