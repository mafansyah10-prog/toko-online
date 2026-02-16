<x-layout>
    <!-- Hero Section -->
    <section class="relative bg-gray-900 text-white overflow-hidden h-[80vh] flex items-center">
        <div class="absolute inset-0">
            <img class="w-full h-full object-cover opacity-60" src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Hero Background">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="max-w-2xl" x-data="{ show: false }" x-init="setTimeout(() => show = true, 500)">
                <h1 class="text-5xl md:text-7xl font-bold tracking-tight mb-6 transform transition-all duration-1000 translate-y-10 opacity-0" :class="{ '!translate-y-0 !opacity-100': show }">
                    Sajian Berkualitas <span class="bg-clip-text text-transparent bg-gradient-to-r from-rose-400 to-purple-400">Untuk Setiap Momen</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-200 mb-10 transform transition-all duration-1000 delay-300 translate-y-10 opacity-0" :class="{ '!translate-y-0 !opacity-100': show }">
                    Makanan praktis dengan cita rasa rumahan,
bikin waktu makan jadi lebih mudah.
                </p>
                <div class="flex space-x-4 transform transition-all duration-1000 delay-500 translate-y-10 opacity-0" :class="{ '!translate-y-0 !opacity-100': show }">
                    <a href="{{ route('products.index') }}" class="px-8 py-4 bg-rose-500 hover:bg-rose-600 text-white rounded-full font-semibold transition-all hover:scale-105 hover:shadow-[0_0_20px_rgba(244,63,94,0.5)]">
                        Lihat Produk
                    </a>
                    <a href="#categories" class="px-8 py-4 bg-white/10 backdrop-blur-md border border-white/20 text-white hover:bg-white/20 rounded-full font-semibold transition-all">
                        Jelajahi Kategori
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-base text-rose-500 font-semibold tracking-wide uppercase">Koleksi</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">Belanja per Kategori</p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">Jelajahi katalog produk premium kami.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="group relative overflow-hidden rounded-3xl shadow-lg aspect-[4/5] cursor-pointer">
                    <img src="{{ $category->image ? Storage::url($category->image) : 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60' }}" 
                         alt="{{ $category->name }}" 
                         class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute bottom-0 left-0 p-6 transform transition-transform duration-500 translate-y-2 group-hover:translate-y-0">
                        <h3 class="text-white text-2xl font-bold mb-2">{{ $category->name }}</h3>
                        <p class="text-gray-300 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 transform translate-y-4 group-hover:translate-y-0">Lihat Koleksi &rarr;</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900">Produk Unggulan</h2>
                    <p class="mt-2 text-gray-500">Pilihan terbaik kami untuk Anda.</p>
                </div>
                <a href="{{ route('products.index') }}" class="text-rose-500 hover:text-rose-700 font-medium flex items-center group">
                    Lihat semua produk 
                    <span class="ml-2 transform group-hover:translate-x-1 transition-transform">&rarr;</span>
                </a>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-y-12 gap-x-8">
                @foreach($products as $product)
                <div class="group relative" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                    <div class="relative w-full aspect-[1/1] overflow-hidden rounded-2xl bg-gray-100 mb-4 shadow-sm group-hover:shadow-md transition-shadow">
                        @if($product->images && count($product->images) > 0)
                            <img src="{{ Storage::url($product->images[0]) }}" alt="{{ $product->name }}" class="w-full h-full object-cover object-center transform transition-transform duration-500 group-hover:scale-105">
                        @else
                           <img src="https://via.placeholder.com/500?text=No+Image" alt="{{ $product->name }}" class="w-full h-full object-cover object-center">
                        @endif
                        
                        <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="absolute top-2 right-2 z-10">
                            @csrf
                            <button type="submit" class="p-2 bg-white/80 backdrop-blur-sm rounded-full shadow-sm text-gray-500 hover:text-rose-500 transition {{ in_array($product->id, $userWishlistIds ?? []) ? '!text-rose-500' : '' }}">
                                <svg class="w-5 h-5" fill="{{ in_array($product->id, $userWishlistIds ?? []) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            </button>
                        </form>
                        
                        <!-- Quick Actions -->
                        <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                            <a href="{{ route('products.show', $product->slug) }}" class="p-3 bg-white text-gray-900 rounded-full shadow-lg hover:bg-rose-500 hover:text-white transition-colors" title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            <a href="{{ route('cart.add', $product->id) }}" class="p-3 bg-white text-gray-900 rounded-full shadow-lg hover:bg-rose-500 hover:text-white transition-colors" title="Tambah ke Keranjang">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </a>
                        </div>
                        
                        @if($product->stock <= 0)
                            <div class="absolute top-2 right-2 bg-gray-900/80 text-white text-xs font-bold px-2 py-1 rounded">
                                Stok Habis
                            </div>
                        @elseif($product->is_featured)
                            <div class="absolute top-2 left-2 bg-rose-500 text-white text-xs font-bold px-2 py-1 rounded-sm">
                                Laris
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 group-hover:text-rose-600 transition-colors">
                                    <a href="{{ route('products.show', $product->slug) }}">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">{{ $product->category?->name ?? 'Tak Berkategori' }}</p>
                            </div>
                            <p class="text-lg font-bold text-gray-900">IDR {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layout>
