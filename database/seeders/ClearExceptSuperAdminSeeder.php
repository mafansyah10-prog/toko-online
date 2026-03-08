<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClearExceptSuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate tables that have foreign keys to users first
        DB::table('sessions')->truncate();
        DB::table('password_reset_tokens')->truncate();

        // Delete all users except the super admin
        User::where('email', '!=', 'ramy@pura.com')->delete();

        // Get all table names
        $database = env('DB_DATABASE');
        $tables = DB::select("SHOW TABLES IN `$database`");

        $tableKey = "Tables_in_$database";

        // Truncate all other tables except users, migrations, sessions, password_reset_tokens
        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            if (! in_array($tableName, ['users', 'migrations', 'sessions', 'password_reset_tokens'])) {
                DB::table($tableName)->truncate();
            }
        }

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Database cleared except super admin user.');
    }
}
