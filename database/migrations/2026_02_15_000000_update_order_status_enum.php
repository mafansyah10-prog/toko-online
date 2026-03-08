<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (config('database.default') === 'sqlite') {
            return;
        }
        // specific to MySQL/MariaDB
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('new', 'processing', 'ready', 'shipped', 'delivered', 'cancelled') DEFAULT 'new'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'sqlite') {
            return;
        }
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('new', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'new'");
    }
};
