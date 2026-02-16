<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        // Categories
        $clothing = Category::updateOrCreate([
            'slug' => 'clothing',
        ], [
            'name' => 'Clothing',
            'slug' => 'clothing',
            'is_active' => true,
        ]);

        $electronics = Category::updateOrCreate([
            'slug' => 'electronics',
        ], [
            'name' => 'Electronics',
            'slug' => 'electronics',
            'is_active' => true,
        ]);

        $accessories = Category::updateOrCreate([
            'slug' => 'accessories',
        ], [
            'name' => 'Accessories',
            'slug' => 'accessories',
            'is_active' => true,
        ]);

        // Products
        Product::updateOrCreate([
            'slug' => 'premium-cotton-t-shirt',
        ], [
            'category_id' => $clothing->id,
            'name' => 'Premium Cotton T-Shirt',
            'slug' => 'premium-cotton-t-shirt',
            'description' => 'High quality cotton t-shirt, comfortable for daily use.',
            'price' => 150000,
            'stock' => 100,
            'is_active' => true,
            'is_featured' => true,
        ]);

        Product::updateOrCreate([
            'slug' => 'slim-fit-jeans',
        ], [
            'category_id' => $clothing->id,
            'name' => 'Slim Fit Jeans',
            'slug' => 'slim-fit-jeans',
            'description' => 'Stylish slim fit jeans with durable denim material.',
            'price' => 350000,
            'stock' => 50,
            'is_active' => true,
            'is_featured' => true,
        ]);

        Product::updateOrCreate([
            'slug' => 'wireless-headphones',
        ], [
            'category_id' => $electronics->id,
            'name' => 'Wireless Headphones',
            'slug' => 'wireless-headphones',
            'description' => 'Noise cancelling wireless headphones with long battery life.',
            'price' => 1200000,
            'stock' => 25,
            'is_active' => true,
            'is_featured' => true,
        ]);

        Product::updateOrCreate([
            'slug' => 'leather-wallet',
        ], [
            'category_id' => $accessories->id,
            'name' => 'Leather Wallet',
            'slug' => 'leather-wallet',
            'description' => 'Genuine leather wallet with multiple card slots.',
            'price' => 250000,
            'stock' => 75,
            'is_active' => true,
            'is_featured' => false,
        ]);
        
        $this->command->info('Shop seeded successfully with demo data!');
    }
}
