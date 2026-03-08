<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if ($user) {
            $user->email = 'admin@gmail.com';
            $user->password = Hash::make('Admin123');
            $user->name = 'Admin';
            $user->save();
            $this->command->info('Admin user updated.');
        } else {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('Admin123'),
            ]);
            $this->command->info('Admin user created.');
        }
    }
}
