<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderController extends Controller
{
    public function index()
    {
        return view('checkout');
    }

    public function show($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    public function applyVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'total_amount' => 'required|numeric',
        ]);

        $voucher = \App\Models\Voucher::where('code', $request->code)
            ->where('is_active', true)
            ->first();

        if (!$voucher) {
            return response()->json(['valid' => false, 'message' => 'Kode voucher tidak valid.']);
        }

        if ($voucher->expired_at && $voucher->expired_at->isPast()) {
            return response()->json(['valid' => false, 'message' => 'Voucher sudah kadaluarsa.']);
        }

        if ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit) {
            return response()->json(['valid' => false, 'message' => 'Kuota voucher sudah habis.']);
        }

        $discount = 0;
        if ($voucher->type === 'fixed') {
            $discount = $voucher->amount;
        } else {
            $discount = ($request->total_amount * $voucher->amount) / 100;
        }

        // Ensure discount doesn't exceed total amount
        $discount = min($discount, $request->total_amount);

        return response()->json([
            'valid' => true,
            'discount_amount' => $discount,
            'code' => $voucher->code,
            'message' => 'Voucher berhasil digunakan!'
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Checkout store called', ['request' => $request->all()]);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
            'payment_method' => 'required',
        ]);

        $cart = session()->get('cart');
        Log::info('Cart data', ['cart' => $cart]);

        if(!$cart) {
            Log::warning('Cart is empty');
            return redirect()->route('products.index')->with('error', 'Keranjang kosong!');
        }

        // Validate stock availability before processing
        foreach($cart as $productId => $details) {
            $product = Product::find($productId);
            if (!$product) {
                return redirect()->back()->with('error', 'Produk "' . $details['name'] . '" tidak ditemukan.');
            }
            if ($product->stock < $details['quantity']) {
                return redirect()->back()->with('error', 'Stok "' . $product->name . '" tidak mencukupi. Tersedia: ' . $product->stock);
            }
        }

        $shipping_amount = 0; // Free shipping
        
        $subtotal = 0;
        foreach($cart as $id => $details) {
            $subtotal += $details['price'] * $details['quantity'];
        }
        
        // Calculate Discount
        $discount_amount = 0;
        $voucher_code = null;

        if ($request->voucher_code) {
            $voucher = \App\Models\Voucher::where('code', $request->voucher_code)
                ->where('is_active', true)
                ->first();
            
            if ($voucher) {
                // Secondary validation
                $isValid = true;
                if ($voucher->expired_at && $voucher->expired_at->isPast()) $isValid = false;
                if ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit) $isValid = false;

                if ($isValid) {
                    if ($voucher->type === 'fixed') {
                        $discount_amount = $voucher->amount;
                    } else {
                        $discount_amount = ($subtotal * $voucher->amount) / 100;
                    }
                    $discount_amount = min($discount_amount, $subtotal);
                    $voucher_code = $voucher->code;
                    
                    // Increment usage
                    $voucher->increment('used_count');
                }
            }
        }

        $total = ($subtotal - $discount_amount) + $shipping_amount; 
        Log::info('Calculated total including shipping and discount', ['subtotal' => $subtotal, 'discount' => $discount_amount, 'total' => $total, 'shipping' => $shipping_amount]);

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => auth()->id(), // Associated with logged-in user or null for guest
                'grand_total' => $total,
                'discount_amount' => $discount_amount,
                'voucher_code' => $voucher_code,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending', // Default to pending
                'status' => 'new',
                'shipping_amount' => $shipping_amount,
                'shipping_method' => 'Standard',
                'notes' => "Name: " . $request->name . " | Email: " . $request->email . " | Phone: " . ($request->phone ?? '-') . " | Address: " . $request->address . ", " . $request->city . " " . $request->postal_code . " | Customer Notes: " . ($request->notes ?? '-'),
                'customer_name' => $request->name,
                'customer_email' => $request->email,
                'customer_phone' => $request->phone,
            ]);
            Log::info('Order created', ['order_id' => $order->id]);

            foreach($cart as $productId => $details) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $details['quantity'],
                    'unit_amount' => $details['price'],
                    'total_amount' => $details['price'] * $details['quantity'],
                ]);

                // Reduce product stock
                $product = Product::find($productId);
                if ($product) {
                    $product->decrement('stock', $details['quantity']);
                    Log::info('Stock reduced', ['product_id' => $productId, 'reduced_by' => $details['quantity'], 'new_stock' => $product->fresh()->stock]);
                }
            }
            Log::info('Order items created and stock reduced for order', ['order_id' => $order->id]);

            DB::commit();
            session()->forget('cart');
            Log::info('Order committed successfully', ['order_id' => $order->id]);

            // Check payment method - if COD, Bank Transfer, or E-Wallet, send confirmation email and go to success page
            if (in_array($request->payment_method, ['cash_on_delivery', 'bank_transfer', 'e_wallet'])) {
                // Send order confirmation email
                if ($order->customer_email) {
                    try {
                        Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
                        Log::info('Order confirmation email sent', ['order_id' => $order->id, 'email' => $order->customer_email]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send order confirmation email', ['order_id' => $order->id, 'error' => $e->getMessage()]);
                    }
                }
                return view('checkout.success', compact('order'));
            }

            // Fallback (should not happen if only these methods are available)
            return view('checkout.success', compact('order'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating order', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error creating order: ' . $e->getMessage());
        }
    }

    public function trackingForm()
    {
        return view('orders.tracking');
    }

    public function track(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Search orders by customer email
        $orders = Order::with('items.product')
            ->where('customer_email', $request->email)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($orders->isEmpty()) {
            return back()->with('error', 'Tidak ada pesanan ditemukan dengan email tersebut.');
        }

        // If only one order, show detail directly
        if ($orders->count() === 1) {
            $order = $orders->first();
            return view('orders.tracking-result', compact('order'));
        }

        // If multiple orders, show list
        return view('orders.tracking-list', compact('orders'));
    }
}
