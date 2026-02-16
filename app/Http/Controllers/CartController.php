<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

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
        if ($quantity < 1) $quantity = 1;

        // Check current quantity in cart
        $currentQty = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
        
        // Validate stock availability
        if (($currentQty + $quantity) > $product->stock) {
            return redirect()->back()->with('error', 'Maaf, stok produk tidak mencukupi. Stok tersedia: ' . $product->stock . '. Anda sudah punya ' . $currentQty . ' di keranjang.');
        }

        if(isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
            $cart[$id]['stock'] = $product->stock; // Update stock info
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $product->price,
                "image" => $product->images ? $product->images[0] : null,
                "stock" => $product->stock
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            return redirect()->back()->with('success', 'Product removed successfully!');
        }
    }

    public function updateQuantity(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        
        if(isset($cart[$id])) {
            $action = $request->input('action', 'increase');
            
            // Get fresh stock data from database
            $product = Product::find($id);
            $maxStock = $product ? $product->stock : ($cart[$id]['stock'] ?? 999);
            
            // Update stock info in cart
            if ($product) {
                $cart[$id]['stock'] = $product->stock;
            }
            
            if($action === 'increase') {
                // Validate stock before increasing
                if ($cart[$id]['quantity'] >= $maxStock) {
                    return redirect()->back()->with('error', 'Maaf, stok maksimal sudah tercapai. Stok tersedia: ' . $maxStock);
                }
                $cart[$id]['quantity']++;
            } elseif($action === 'decrease') {
                if($cart[$id]['quantity'] > 1) {
                    $cart[$id]['quantity']--;
                } else {
                    unset($cart[$id]);
                }
            }
            
            session()->put('cart', $cart);
        }
        
        return redirect()->back()->with('success', 'Cart updated successfully!');
    }
}
