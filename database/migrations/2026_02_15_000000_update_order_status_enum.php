<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // specific to MySQL/MariaDB
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('new', 'processing', 'ready', 'shipped', 'delivered', 'cancelled') DEFAULT 'new'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('new', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'new'");
    }
};
