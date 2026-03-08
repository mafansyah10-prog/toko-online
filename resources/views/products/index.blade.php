<x-layout>
    <div class="bg-gradient-to-b from-gray-50 to-white min-h-screen">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8 px-4">
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-gray-900">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">Koleksi</span> Produk
                </h1>
                <p class="mt-4 max-w-xl mx-auto text-base sm:text-xl text-gray-500">Temukan pilihan produk premium pilihan kami.</p>
            </div>

            <!-- Category Filter Pills -->
            @if(isset($categories) && $categories->count() > 0)
            <div class="mb-12 overflow-x-auto pb-4 -mx-4 px-4 sm:mx-0 sm:px-0 flex flex-nowrap sm:flex-wrap justify-start sm:justify-center gap-3 no-scrollbar scroll-smooth" data-reveal>
                <a href="{{ route('products.index') }}" 
                   class="flex-shrink-0 px-6 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all duration-300 active:scale-95 tap-highlight-transparent border-2 {{ !request('category') ? 'bg-gray-900 border-gray-900 text-white shadow-xl' : 'bg-white text-gray-500 border-gray-100 hover:border-rose-500 hover:text-rose-500 shadow-sm' }}">
                    Semua Produk
                </a>
                @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" 
                   class="flex-shrink-0 px-6 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all duration-300 active:scale-95 tap-highlight-transparent border-2 {{ request('category') == $category->slug ? 'bg-gradient-to-r from-rose-500 to-purple-600 border-transparent text-white shadow-xl' : 'bg-white text-gray-500 border-gray-100 hover:border-rose-500 hover:text-rose-500 shadow-sm' }}">
                    {{ $category->name }}
                </a>
                @endforeach
            </div>
            @endif

            <!-- Products Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-10">
                @if(isset($products) && count($products) > 0)
                    @foreach($products as $product)
                <div class="group relative bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-gray-100 flex flex-col active:scale-[0.98] tap-highlight-transparent hover:-translate-y-2"
                     data-reveal style="transition-delay: {{ $loop->index * 50 }}ms">
                    <div class="relative w-full aspect-[1/1] overflow-hidden rounded-2xl bg-gray-100 shadow-sm group-hover:shadow-xl transition-all duration-300 skeleton">
                        @if($product->images && count($product->images) > 0)
                            <img src="{{ Storage::url($product->images[0]) }}" 
                                 alt="{{ $product->name }}" 
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full h-full object-cover object-center transform transition-transform duration-500 group-hover:scale-110"
                                 onload="this.parentElement.classList.remove('skeleton')">
                        @else
                            <img src="https://via.placeholder.com/500?text=No+Image" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover object-center"
                                 onload="this.parentElement.classList.remove('skeleton')">
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
                        <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-3 opacity-100 md:opacity-0 group-hover:opacity-100 transform translate-y-0 md:translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                            <a href="{{ route('products.show', $product->slug) }}" 
                               class="p-3 bg-white text-gray-900 rounded-full shadow-lg hover:bg-rose-500 hover:text-white transition-all duration-200 active:scale-90 tap-highlight-transparent" 
                               title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST"
                                  x-data="{ 
                                      liked: {{ in_array($product->id, $userWishlistIds ?? []) ? 'true' : 'false' }},
                                      loading: false,
                                      toggle() {
                                          if (this.loading) return;
                                          this.loading = true;
                                          const prevState = this.liked;
                                          this.liked = !this.liked;
 
                                          fetch('{{ route('wishlist.toggle', $product->id) }}', {
                                              method: 'POST',
                                              headers: {
                                                  'Content-Type': 'application/json',
                                                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                  'X-Requested-With': 'XMLHttpRequest'
                                              }
                                          })
                                          .then(res => res.json())
                                          .then(data => {
                                              if (data.status === 'added') this.liked = true;
                                              else if (data.status === 'removed') this.liked = false;
                                              this.loading = false;
                                          })
                                          .catch(err => {
                                              this.liked = prevState;
                                              this.loading = false;
                                          });
                                      }
                                  }" @submit.prevent="toggle()">
                                @csrf
                                <button type="submit" 
                                        class="p-3 bg-white text-gray-900 rounded-full shadow-lg hover:bg-rose-500 hover:text-white transition-all duration-300 active:scale-90 tap-highlight-transparent" 
                                        :class="{ '!bg-rose-500 !text-white': liked, 'opacity-50': loading }"
                                        title="Wishlist">
                                    <svg class="w-5 h-5 transition-all duration-300" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                            </form>
                            @if($product->stock > 0)
                            <button @click="$dispatch('add-to-cart', { id: {{ $product->id }}, quantity: 1 })"
                                class="p-3 bg-white text-gray-900 rounded-full shadow-lg hover:bg-rose-500 hover:text-white transition-all duration-200 active:scale-90 tap-highlight-transparent" 
                                title="Tambah ke Keranjang">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="px-5 pb-6 pt-2 w-full">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 group-hover:text-rose-600 transition-colors">
                            <a href="{{ route('products.show', $product->slug) }}">
                                {{ $product->name }}
                            </a>
                        </h3>
                        <div class="flex justify-between items-center w-full mt-2 gap-2">
                            <div class="flex flex-col">
                                <p class="text-[10px] md:text-xs text-gray-500">{{ $product->category?->name ?? 'Menu' }}</p>
                                @if($product->stock > 0 && $product->stock <= 5)
                                    <span class="text-[8px] sm:text-[10px] text-orange-600 font-medium">Sisa {{ $product->stock }}!</span>
                                @endif
                            </div>
                            <p class="text-xs md:text-base font-bold text-rose-500 whitespace-nowrap">
                                IDR {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="col-span-full text-center py-16">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Produk tidak ditemukan</h3>
                    <p class="mt-1 text-sm text-gray-500">Cek lagi nanti untuk produk terbaru!</p>
                </div>
                @endif
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
