<x-layout>
    <div class="bg-gray-50 min-h-screen py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Wishlist <span class="text-rose-500">Saya</span>
                </h1>
                <p class="mt-4 text-lg text-gray-500">Simpan produk favorit Anda untuk dibeli nanti.</p>
            </div>

            @if($wishlists->count() > 0)
                <div class="grid grid-cols-1 gap-y-10 sm:grid-cols-2 gap-x-6 lg:grid-cols-4 xl:gap-x-8">
                    @foreach($wishlists as $item)
                        <div class="group relative bg-white border border-gray-200 rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300">
                            <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden bg-gray-200 group-hover:opacity-75 relative">
                                @if($item->product->images && count($item->product->images) > 0)
                                    <img src="{{ Storage::url($item->product->images[0]) }}" alt="{{ $item->product->name }}" class="h-full w-full object-cover object-center">
                                @else
                                    <img src="https://via.placeholder.com/500?text=No+Image" alt="{{ $item->product->name }}" class="h-full w-full object-cover object-center">
                                @endif
                                
                                <form action="{{ route('wishlist.toggle', $item->product->id) }}" method="POST" class="absolute top-2 right-2">
                                    @csrf
                                    <button type="submit" class="p-2 bg-white rounded-full shadow-md text-rose-500 hover:bg-gray-100 transition">
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                    </button>
                                </form>
                            </div>
                            <div class="p-4">
                                <h3 class="text-sm font-medium text-gray-900">
                                    <a href="{{ route('products.show', $item->product->slug) }}">
                                        {{ $item->product->name }}
                                    </a>
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">{{ $item->product->category->name }}</p>
                                <div class="mt-3 flex items-center justify-between">
                                    <p class="text-lg font-bold text-gray-900">Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                                    <form action="{{ route('cart.add', $item->product->id) }}" method="GET">
                                        <button type="submit" class="text-sm text-rose-600 hover:text-rose-800 font-medium">Add to Cart</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-8">
                    {{ $wishlists->links() }}
                </div>
            @else
                <div class="text-center py-24">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Wishlist Anda kosong</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai belanaja dan simpan produk favorit Anda.</p>
                    <div class="mt-6">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-rose-600 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500">
                            Lihat Produk
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layout>
