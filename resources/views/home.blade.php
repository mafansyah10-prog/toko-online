<x-layout>
    <!-- Hero Section -->
    <section class="relative bg-gray-900 text-white overflow-hidden h-[60vh] md:h-[80vh] flex items-center">
        <div class="absolute inset-0">
            <img class="w-full h-full object-cover opacity-60" src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=75&fm=webp" alt="Hero Background" fetchpriority="high">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="max-w-2xl" x-data="{ show: false }" x-init="setTimeout(() => show = true, 500)">
                <h1 class="text-3xl sm:text-4xl md:text-7xl font-bold tracking-tight mb-4 md:mb-6 transform transition-all duration-1000 translate-y-10 opacity-0" :class="{ '!translate-y-0 !opacity-100': show }">
                    Sajian Berkualitas <span class="bg-clip-text text-transparent bg-gradient-to-r from-rose-400 to-purple-400">Untuk Setiap Momen</span>
                </h1>
                <p class="text-base sm:text-lg md:text-2xl text-gray-200 mb-8 md:mb-10 transform transition-all duration-1000 delay-300 translate-y-10 opacity-0" :class="{ '!translate-y-0 !opacity-100': show }">
                    Makanan praktis dengan cita rasa rumahan,
                    bikin waktu makan jadi lebih mudah.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 transform transition-all duration-1000 delay-500 translate-y-10 opacity-0" :class="{ '!translate-y-0 !opacity-100': show }">
                    <a href="{{ route('products.index') }}" class="px-6 py-3 md:px-8 md:py-4 bg-rose-500 hover:bg-rose-600 text-white rounded-full font-semibold transition-all text-center active:scale-95 tap-highlight-transparent hover:scale-105 hover:shadow-[0_0_20px_rgba(244,63,94,0.5)]">
                        Lihat Produk
                    </a>
                    <a href="#categories" class="px-6 py-3 md:px-8 md:py-4 bg-white/10 backdrop-blur-md border border-white/20 text-white hover:bg-white/20 rounded-full font-semibold transition-all text-center active:scale-95 tap-highlight-transparent">
                        Jelajahi Kategori
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section id="categories" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10 md:mb-16" data-reveal>
            <span class="inline-block px-4 py-1.5 rounded-full bg-rose-50 text-rose-500 text-[10px] md:text-xs font-bold tracking-widest uppercase mb-4 shadow-sm border border-rose-100">KOLEKSI</span>
            <h2 class="text-3xl md:text-5xl font-black text-gray-900 mb-4">Belanja per <span class="bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">Kategori</span></h2>
            <p class="text-base md:text-xl text-gray-500 max-w-2xl mx-auto px-4">Jajaki berbagai pilihan menu terbaik kami yang siap memanjakan lidah Anda.</p>
        </div>
            
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8">
                @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" 
                   class="group relative overflow-hidden rounded-[2.5rem] shadow-lg aspect-[4/5] cursor-pointer bg-gray-200 skeleton active:scale-95 transition-all duration-500 tap-highlight-transparent hover:shadow-2xl hover:-translate-y-2"
                   data-reveal style="transition-delay: {{ $loop->index * 100 }}ms">
                    <img src="{{ $category->image ? Storage::url($category->image) : 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=75&fm=webp' }}" 
                         alt="{{ $category->name }}" 
                         loading="lazy"
                         decoding="async"
                         class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-110"
                         onload="this.parentElement.classList.remove('skeleton')">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-90 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute bottom-0 left-0 p-4 md:p-6 transform transition-transform duration-500 translate-y-2 group-hover:translate-y-0 text-left">
                        <h3 class="text-white text-base sm:text-lg md:text-2xl font-bold mb-1 md:mb-2">{{ $category->name }}</h3>
                        <p class="text-gray-300 text-[10px] md:text-sm opacity-100 group-hover:opacity-100 transition-opacity duration-300 transform translate-y-0">Lihat Koleksi &rarr;</p>
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
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
                @foreach($products->take(10) as $product)
                <div class="group relative bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-gray-100 flex flex-col active:scale-[0.98] tap-highlight-transparent hover:-translate-y-2"
                     data-reveal style="transition-delay: {{ $loop->index * 50 }}ms">
                    <div class="relative w-full aspect-[1/1] overflow-hidden rounded-2xl bg-gray-100 mb-4 shadow-sm group-hover:shadow-md transition-shadow skeleton">
                        @if($product->images && count($product->images) > 0)
                            <img src="{{ Storage::url($product->images[0]) }}" 
                                 alt="{{ $product->name }}" 
                                 loading="lazy"
                                 decoding="async"
                                 class="w-full h-full object-cover object-center transform transition-transform duration-500 group-hover:scale-105"
                                 onload="this.parentElement.classList.remove('skeleton')">
                        @else
                           <img src="https://via.placeholder.com/500?text=No+Image" alt="{{ $product->name }}" class="w-full h-full object-cover object-center" onload="this.parentElement.classList.remove('skeleton')">
                        @endif
                        
                        <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" class="absolute top-2 right-2 z-10"
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
                                          window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Gagal memproses wishlist', type: 'error' } }));
                                      });
                                  }
                              }" @submit.prevent="toggle()">
                            @csrf
                            <button type="submit" 
                                    class="p-2 bg-white/80 backdrop-blur-sm rounded-full shadow-sm text-gray-500 hover:text-rose-500 transition duration-300"
                                    :class="{ 'text-rose-500 scale-110': liked, 'opacity-50': loading }">
                                <svg class="w-5 h-5 transition-all duration-300" :fill="liked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>
                        </form>
                        
                        <!-- Quick Actions -->
                        <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2 opacity-100 md:opacity-0 group-hover:opacity-100 transform translate-y-0 md:translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                            <a href="{{ route('products.show', $product->slug) }}" class="p-3 bg-white text-gray-900 rounded-full shadow-lg hover:bg-rose-500 hover:text-white transition-colors active:scale-90 tap-highlight-transparent" title="Lihat Detail">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            <button @click="$dispatch('add-to-cart', { id: {{ $product->id }}, quantity: 1 })" 
                                class="p-3 bg-white text-gray-900 rounded-full shadow-lg hover:bg-rose-500 hover:text-white transition-colors active:scale-90 tap-highlight-transparent" title="Tambah ke Keranjang">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </button>
                        </div>
                        
                        @if($product->stock <= 0)
                            <div class="absolute top-2 right-2 bg-gray-900/80 text-white text-[10px] md:text-xs font-bold px-2 py-1 rounded">
                                Stok Habis
                            </div>
                        @elseif($product->is_featured)
                            <div class="absolute top-2 left-2 bg-rose-500 text-white text-[10px] md:text-xs font-bold px-2 py-1 rounded-sm">
                                Laris
                            </div>
                        @endif
                    </div>
                    <div class="px-4 pb-4 pt-3 w-full">
                        <h3 class="text-sm sm:text-base font-semibold text-gray-900 group-hover:text-rose-600 transition-colors line-clamp-1" title="{{ $product->name }}">
                            <a href="{{ route('products.show', $product->slug) }}">
                                {{ $product->name }}
                            </a>
                        </h3>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center w-full mt-1.5 gap-1">
                            <p class="text-[10px] md:text-xs text-gray-500 uppercase tracking-widest">{{ $product->category?->name ?? 'Menu' }}</p>
                            <p class="text-xs md:text-sm font-black text-rose-500 whitespace-nowrap">
                                IDR {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layout>
