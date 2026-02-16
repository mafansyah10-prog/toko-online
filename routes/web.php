<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\Category;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;


// Home
Route::get('/', function () {
    $products = Product::where('is_active', true)->where('is_featured', true)->take(8)->get();
    $categories = Category::where('is_active', true)->take(4)->get();
    $userWishlistIds = auth()->check() ? auth()->user()->wishlists()->pluck('product_id')->toArray() : [];
    return view('home', compact('products', 'categories', 'userWishlistIds'));
})->name('home');

// About
Route::get('/about', function () {
    return view('about');
})->name('about');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/add-to-cart/{id}', [CartController::class, 'add'])->name('cart.add');
Route::get('/remove-from-cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update/{id}', [CartController::class, 'updateQuantity'])->name('cart.update');

// Checkout & Orders
Route::get('/checkout', [OrderController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
Route::post('/checkout/apply-voucher', [OrderController::class, 'applyVoucher'])->name('checkout.apply-voucher'); // Added Route
Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
Route::get('/track-order', [OrderController::class, 'trackingForm'])->name('orders.tracking');
Route::post('/track-order', [OrderController::class, 'track'])->name('orders.track');

// Wishlist
Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle')->middleware('auth');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index')->middleware('auth');

// POS Receipt Print
Route::get('/admin/pos/print/{id}', function ($id) {
    if (!auth()->check() || (!auth()->user()->can('access_pos') && !auth()->user()->can('manage_orders'))) {
        abort(403);
    }
    $order = \App\Models\Order::with('items.product')->findOrFail($id);
    return view('filament.pages.pos-receipt', compact('order'));
})->name('pos.print')->middleware('auth');
