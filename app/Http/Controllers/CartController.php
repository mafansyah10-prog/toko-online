<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        return view('cart.index', compact('cart'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        $quantity = (int) $request->input('quantity', 1);
        if ($quantity < 1) {
            $quantity = 1;
        }

        // Check current quantity in cart
        $currentQty = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;

        // Validate stock availability
        if (($currentQty + $quantity) > $product->stock) {
            return redirect()->back()->with('error', 'Maaf, stok produk tidak mencukupi. Stok tersedia: '.$product->stock.'. Anda sudah punya '.$currentQty.' di keranjang.');
        }

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
            $cart[$id]['stock'] = $product->stock; // Update stock info
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'slug' => $product->slug,
                'quantity' => $quantity,
                'price' => $product->price,
                'image' => $product->images ? $product->images[0] : null,
                'stock' => $product->stock,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function addAjax(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);
        $quantity = (int) $request->input('quantity', 1);

        $currentQty = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;

        if (($currentQty + $quantity) > $product->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi (Tersedia: '.$product->stock.')',
            ], 422);
        }

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'slug' => $product->slug,
                'quantity' => $quantity,
                'price' => $product->price,
                'image' => $product->images ? $product->images[0] : null,
                'stock' => $product->stock,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil ditambahkan!',
            'cart_count' => array_sum(array_column($cart, 'quantity')),
            'cart_data' => $this->formatCartData($cart),
        ]);
    }

    public function getCartData()
    {
        $cart = session()->get('cart', []);

        return response()->json([
            'cart_count' => array_sum(array_column($cart, 'quantity')),
            'cart_data' => $this->formatCartData($cart),
        ]);
    }

    private function formatCartData($cart)
    {
        $items = [];
        $total = 0;
        foreach ($cart as $id => $details) {
            $itemTotal = $details['price'] * $details['quantity'];
            $total += $itemTotal;
            $items[] = [
                'id' => $id,
                'name' => $details['name'],
                'quantity' => $details['quantity'],
                'price' => 'IDR '.number_format($details['price'], 0, ',', '.'),
                'subtotal' => 'IDR '.number_format($itemTotal, 0, ',', '.'),
                'image' => $details['image'] ? \Illuminate\Support\Facades\Storage::url($details['image']) : 'https://via.placeholder.com/100',
                'url' => route('products.show', $details['slug'] ?? \Illuminate\Support\Str::slug($details['name'])),
            ];
        }

        return [
            'items' => $items,
            'total' => 'IDR '.number_format($total, 0, ',', '.'),
        ];
    }

    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }

            return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang!');
        }
    }

    public function updateQuantity(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $action = $request->input('action', 'increase');

            // Get fresh stock data from database
            $product = Product::find($id);
            $maxStock = $product ? $product->stock : ($cart[$id]['stock'] ?? 999);

            // Update stock info in cart
            if ($product) {
                $cart[$id]['stock'] = $product->stock;
            }

            if ($action === 'increase') {
                // Validate stock before increasing
                if ($cart[$id]['quantity'] >= $maxStock) {
                    return redirect()->back()->with('error', 'Maaf, stok maksimal sudah tercapai. Stok tersedia: '.$maxStock);
                }
                $cart[$id]['quantity']++;
            } elseif ($action === 'decrease') {
                if ($cart[$id]['quantity'] > 1) {
                    $cart[$id]['quantity']--;
                } else {
                    unset($cart[$id]);
                }
            }

            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Keranjang berhasil diperbarui!');
    }
}
