<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('customer_address')->nullable()->after('customer_phone');
            $table->string('customer_city')->nullable()->after('customer_address');
            $table->string('customer_postal_code')->nullable()->after('customer_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['customer_address', 'customer_city', 'customer_postal_code']);
        });
    }
};
