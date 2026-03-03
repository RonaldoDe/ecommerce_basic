<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'roles'      => 'Roles',
            'users'      => 'Usuarios',
            'categories' => 'Categorías',
            'products'   => 'Productos',
            'orders'     => 'Órdenes',
            'coupons'    => 'Cupones',
            'reviews'    => 'Reseñas',
            'settings'   => 'Configuración',
        ];

        $actions = [
            'index'   => 'Ver listado',
            'show'    => 'Ver detalle',
            'create'  => 'Crear',
            'edit'    => 'Editar',
            'destroy' => 'Eliminar',
        ];

        foreach ($modules as $module => $label) {
            foreach ($actions as $action => $actionLabel) {
                Permission::firstOrCreate([
                    'name'       => "{$module}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }
    }
}