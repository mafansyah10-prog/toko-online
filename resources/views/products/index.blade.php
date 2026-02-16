<x-layout>
    <div class="bg-gradient-to-b from-gray-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">Koleksi</span> Produk
                </h1>
                <p class="mt-4 max-w-xl mx-auto text-xl text-gray-500">Temukan pilihan produk premium pilihan kami.</p>
            </div>

            <!-- Category Filter -->
            @if(isset($categories) && $categories->count() > 0)
            <div class="mb-10 flex flex-wrap justify-center gap-3">
                <a href="{{ route('products.index') }}" 
                   class="px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-200 {{ !request('category') ? 'bg-gradient-to-r from-rose-500 to-purple-600 text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-200 hover:border-rose-300 hover:text-rose-600' }}">
                    Semua Produk
                </a>
                @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" 
                   class="px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-200 {{ request('category') == $category->slug ? 'bg-gradient-to-r from-rose-500 to-purple-600 text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-200 hover:border-rose-300 hover:text-rose-600' }}">
                    {{ $category->name }} <span class="ml-1 text-xs opacity-70">({{ $category->products_count }})</span>
                </a>
                @endforeach
            </div>
            @endif

            <!-- Products Grid -->
            <div class="grid grid-cols-1 gap-y-10 sm:grid-cols-2 gap-x-6 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
                @forelse($products as $product)
                <div class="group relative" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                    <div class="relative w-full aspect-[1/1] overflow-hidden rounded-2xl bg-gray-100 shadow-sm group-hover:shadow-xl transition-all duration-300">
                        @if($product->images && count($product->images) > 0)
                            <img src="{{ Storage::url($product->images[0]) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover object-center transform transition-transform duration-500 group-hover:scale-110">
                        @else
                            <img src="https://via.placeholder.com/500?text=No+Image" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover object-center">
                        @endif
                        
                        <!-- Overlay gradient on hover -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <!-- Badges -->
                        @if($product->stock <= 0)
                            <div class="absolute top-3 right-3 bg-gray-900/90 text-white text-xs font-bold px-3 py-1.5 rounded-full backdrop-blur-sm">
                                Stok Habis
                            </div>
                        @elseif($product->is_featured)
                            <div class="absolute top-3 left-3 bg-gradient-to-r from-rose-500 to-pink-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                                ✨ HOT
                            </div>
                        @endif
                        
                        <!-- Quick Actions -->
                        <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-3 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                            <a href="{{ route('products.show', $product->slug) }}" 
                               class="p-3 bg-white text-gray-900 rounded-full shadow-lg hover:bg-rose-500 hover:text-white transition-all duration-200 hover:scale-110" 
                               title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-3 bg-white text-gray-900 rounded-full shadow-lg hover:bg-rose-500 hover:text-white transition-all duration-200 hover:scale-110 {{ in_array($product->id, $userWishlistIds ?? []) ? '!bg-rose-500 !text-white' : '' }}" title="Wishlist">
                                    <svg class="w-5 h-5" fill="{{ in_array($product->id, $userWishlistIds ?? []) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                            </form>
                            @if($product->stock > 0)
                            <a href="{{ route('cart.add', $product->id) }}" 
                               class="p-3 bg-white text-gray-900 rounded-full shadow-lg hover:bg-rose-500 hover:text-white transition-all duration-200 hover:scale-110" 
                               title="Tambah ke Keranjang">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="mt-4 space-y-2">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-rose-600 transition-colors line-clamp-1">
                                    <a href="{{ route('products.show', $product->slug) }}">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-500">{{ $product->category?->name ?? 'Tak Berkategori' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">
                                IDR {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                            @if($product->stock > 0 && $product->stock <= 5)
                                <span class="text-xs text-orange-600 font-medium">Tersisa {{ $product->stock }} lagi!</span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-16">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Produk tidak ditemukan</h3>
                    <p class="mt-1 text-sm text-gray-500">Cek lagi nanti untuk produk terbaru!</p>
                </div>
                @endforelse
            </div>
            
            <!-- Pagination -->
            @if($products->hasPages())
            <div class="mt-12 flex justify-center">
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    {{ $products->links() }}
                </nav>
            </div>
            @endif
        </div>
    </div>
</x-layout>
