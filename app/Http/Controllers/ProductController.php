<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        // Filter by min price
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        // Filter by max price
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
        }

        $sortBy = $request->get('sort', 'newest');
        $cacheKey = 'products_index_'.md5(json_encode($request->all()));

        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($query) {
            return [
                'products' => $query->with('category')->paginate(12)->withQueryString(),
                'categories' => Category::where('is_active', true)->withCount('products')->get(),
            ];
        });

        $products = $data['products'];
        $categories = $data['categories'];
        $userWishlistIds = auth()->check()
            ? auth()->user()->wishlists()->pluck('product_id')->toArray()
            : session()->get('wishlist', []);

        return view('products.index', compact('products', 'categories', 'userWishlistIds'));
    }

    public function show($slug)
    {
        $product = Product::where('is_active', true)->where('slug', $slug)->firstOrFail();
        $userWishlistIds = auth()->check()
            ? auth()->user()->wishlists()->pluck('product_id')->toArray()
            : session()->get('wishlist', []);

        return view('products.show', compact('product', 'userWishlistIds'));
    }

    public function liveSearch(Request $request)
    {
        $search = $request->get('q');
        if (! $search || strlen($search) < 2) {
            return response()->json([]);
        }

        $products = Product::where('is_active', true)
            ->where('name', 'like', '%'.$search.'%')
            ->take(5)
            ->get(['id', 'name', 'slug', 'price', 'images']);

        $results = $products->map(function ($product) {
            return [
                'name' => $product->name,
                'price' => 'IDR '.number_format($product->price, 0, ',', '.'),
                'url' => route('products.show', $product->slug),
                'image' => $product->images ? \Illuminate\Support\Facades\Storage::url($product->images[0]) : 'https://via.placeholder.com/50',
            ];
        });

        return response()->json($results);
    }
}
