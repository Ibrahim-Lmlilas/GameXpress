<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {




        $superAdminRole = Role::create(['name' => 'super_admin']);
        $superAdminRole->givePermissionTo([
            'view_dashboard',
            'view_products', 'create_products', 'edit_products', 'delete_products',
            'view_categories', 'create_categories', 'edit_categories', 'delete_categories',
            'view_users', 'create_users', 'edit_users', 'delete_users'
        ]);

        $productManagerRole = Role::create(['name' => 'product_manager']);
        $productManagerRole->givePermissionTo([
            'view_dashboard',
            'view_products', 'create_products', 'edit_products', 'delete_products',
            'view_categories', 'create_categories', 'edit_categories', 'delete_categories'
        ]);

        $userManagerRole = Role::create(['name' => 'user_manager']);
        $userManagerRole->givePermissionTo([
            'view_dashboard',
            'view_users', 'create_users', 'edit_users', 'delete_users'
        ]);
    }
}
