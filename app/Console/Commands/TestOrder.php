<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Console\Command;

class TestOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing order creation...');

        // Create a test order
        $order = Order::create([
            'user_id' => 1, // Assuming user with ID 1 exists
            'grand_total' => 100000,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'pending',
            'status' => 'new',
            'currency' => 'IDR',
            'shipping_amount' => 10000,
            'shipping_method' => 'standard',
            'notes' => 'Test order from command',
        ]);

        // Create a test order item
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => 1, // Assuming product with ID 1 exists
            'quantity' => 1,
            'unit_amount' => 90000,
        ]);

        $this->info('Test order created successfully with ID: '.$order->id);
    }
}
