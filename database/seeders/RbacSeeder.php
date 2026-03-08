<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Roles (Job Desc)
        $founderRole = Role::firstOrCreate(['name' => 'founder']);
        $kasirRole = Role::firstOrCreate(['name' => 'kasir']);

        // 2. Create/Find Admin User and Assign Founder Role
        $adminEmail = 'admin@gmail.com';
        $adminUser = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Admin Toko',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole($founderRole);
        $this->command->info("User '{$adminEmail}' - Role 'founder' assigned.");

        // 3. Create Specific Kasir User
        $kasirEmail = 'kasir@gmail.com';
        $kasirUser = User::firstOrCreate(
            ['email' => $kasirEmail],
            [
                'name' => 'Kasir Toko',
                'password' => Hash::make('kasir123'),
                'email_verified_at' => now(),
            ]
        );
        $kasirUser->assignRole($kasirRole);
        $this->command->info("User '{$kasirEmail}' - Role 'kasir' assigned.");
    }
}
