<x-layout>
    <div class="bg-white" x-data="{ 
        activeImage: '{{ $product->images ? Storage::url($product->images[0]) : 'https://via.placeholder.com/600' }}',
        quantity: 1,
        stock: {{ $product->stock }}
    }">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-2 lg:gap-x-12 lg:items-start">
                
                <!-- Image Gallery -->
                <div class="flex flex-col-reverse">
                    <!-- Thumbnails -->
                    <div class="hidden mt-6 w-full max-w-2xl mx-auto sm:block lg:max-w-none">
                        <div class="grid grid-cols-4 gap-6">
                            @if($product->images)
                                @foreach($product->images as $image)
                                <button @click="activeImage = '{{ Storage::url($image) }}'" 
                                        class="relative h-24 bg-white rounded-md flex items-center justify-center text-sm font-medium uppercase text-gray-900 cursor-pointer hover:bg-gray-50 focus:outline-none focus:ring focus:ring-offset-4 focus:ring-opacity-50 ring-rose-500 transition-all transform hover:scale-105"
                                        :class="{ 'ring-2 ring-rose-500': activeImage === '{{ Storage::url($image) }}', 'border border-gray-200': activeImage !== '{{ Storage::url($image) }}' }">
                                    <span class="sr-only">Image view</span>
                                    <span class="absolute inset-0 rounded-md overflow-hidden">
                                        <img src="{{ Storage::url($image) }}" alt="" class="w-full h-full object-center object-cover">
                                    </span>
                                </button>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Main Image -->
                    <div class="w-full aspect-w-1 aspect-h-1 rounded-2xl overflow-hidden shadow-lg bg-gray-100 relative group">
                        <img :src="activeImage" alt="{{ $product->name }}" class="w-full h-full object-center object-cover transition-transform duration-500 group-hover:scale-110 cursor-zoom-in">
                        <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-gray-500 hover:text-rose-500 transition {{ in_array($product->id, $userWishlistIds ?? []) ? 'text-rose-500' : '' }}">
                                    <svg class="w-6 h-6" fill="{{ in_array($product->id, $userWishlistIds ?? []) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                </button>
                            </form>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="mt-10 px-4 sm:px-0 sm:mt-16 lg:mt-0">
                    <nav aria-label="Breadcrumb">
                        <ol role="list" class="flex items-center space-x-2">
                            <li><a href="{{ route('home') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">Beranda</a></li>
                            <li><svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" /></svg></li>
                            <li><a href="#" class="text-sm font-medium text-gray-500 hover:text-gray-900">{{ $product->category?->name ?? 'Produk' }}</a></li>
                        </ol>
                    </nav>

                    <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 mt-4">{{ $product->name }}</h1>

                    <div class="mt-3">
                        <h2 class="sr-only">Informasi Produk</h2>
                        <p class="text-3xl text-rose-500 font-bold">IDR {{ number_format($product->price, 0, ',', '.') }}</p>
                    </div>

                    <!-- Description -->
                    <div class="mt-8 prose prose-rose text-gray-500">
                        {!! $product->description !!}
                    </div>

                    <div class="mt-10 border-t border-gray-100 pt-10">
                        @if($product->stock > 0)
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center border border-gray-300 rounded-full">
                                    <button @click="if(quantity > 1) quantity--" class="p-3 text-gray-600 hover:text-rose-600 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                    </button>
                                    <input type="text" x-model="quantity" readonly class="w-12 text-center border-none bg-transparent focus:ring-0 text-gray-900 font-medium">
                                    <button @click="if(quantity < stock) quantity++" class="p-3 text-gray-600 hover:text-rose-600 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    </button>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <span class="font-medium text-green-600">Tersedia</span> ({{ $product->stock }} stok)
                                </div>
                            </div>
                            <form action="{{ route('cart.add', $product->id) }}" method="GET"> 
                                <input type="hidden" name="quantity" :value="quantity">
                                <button type="submit" class="w-full bg-rose-600 border border-transparent rounded-full py-4 px-8 flex items-center justify-center text-base font-bold text-white hover:bg-rose-700 hover:shadow-lg transform active:scale-95 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500">
                                    Tambah ke Keranjang
                                </button>
                            </form>
                        @else
                            <button disabled class="w-full bg-gray-200 border border-transparent rounded-full py-4 px-8 flex items-center justify-center text-base font-medium text-gray-400 cursor-not-allowed">
                                Stok Habis
                            </button>
                        @endif
                    </div>
                    
                    <!-- Features/Benefits (Static placeholders for premium feel) -->
                    <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                        <div class="flex items-center space-x-3 text-sm text-gray-500">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Bahan Berkualitas Premium</span>
                        </div>
                        <div class="flex items-center space-x-3 text-sm text-gray-500">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Pengiriman Cepat Tersedia</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
