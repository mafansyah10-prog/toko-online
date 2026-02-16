<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $permissions = [
            'access_pos',
            'manage_products',
            'manage_categories',
            'manage_orders',
            'manage_users',
            'manage_roles',
            'manage_settings',
            'manage_vouchers',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Founder role and assign all permissions
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'founder']);
        $role->syncPermissions(\Spatie\Permission\Models\Permission::all());

        // Create Kasir role and assign POS permission
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'kasir']);
        $role->givePermissionTo('access_pos');
    }
}
