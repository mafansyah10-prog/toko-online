<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $wishlists = Wishlist::where('user_id', Auth::id())
                ->with('product')
                ->latest()
                ->paginate(12);
        } else {
            $wishlistIds = session()->get('wishlist', []);

            // Manual pagination for session wishlist
            $currentPage = request()->get('page', 1);
            $perPage = 12;

            $products = Product::whereIn('id', $wishlistIds)
                ->where('is_active', true)
                ->latest()
                ->get();

            // Transform consistent with database model for view
            $items = $products->map(function ($product) {
                return (object) ['product' => $product, 'product_id' => $product->id];
            });

            $pagedData = $items->slice(($currentPage - 1) * $perPage, $perPage)->all();

            $wishlists = new \Illuminate\Pagination\LengthAwarePaginator(
                $pagedData,
                count($items),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        return view('wishlist.index', compact('wishlists'));
    }

    public function toggle(Request $request, Product $product)
    {
        if (Auth::check()) {
            $wishlist = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->first();

            if ($wishlist) {
                $wishlist->delete();
                $status = 'removed';
            } else {
                Wishlist::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                ]);
                $status = 'added';
            }
        } else {
            $wishlist = session()->get('wishlist', []);

            if (in_array($product->id, $wishlist)) {
                $wishlist = array_diff($wishlist, [$product->id]);
                $status = 'removed';
            } else {
                $wishlist[] = $product->id;
                $status = 'added';
            }

            session()->put('wishlist', array_values($wishlist));
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => $status,
                'message' => $status === 'added' ? 'Produk ditambahkan ke favorit.' : 'Produk dihapus dari favorit.',
            ]);
        }

        return back()->with('success', $status === 'added' ? 'Produk ditambahkan ke favorit.' : 'Produk dihapus dari favorit.');
    }
}
