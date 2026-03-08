<x-layout>
    <div class="bg-gray-50 min-h-screen py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10 px-4 pt-10">
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-gray-900">
                    Daftar <span class="bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">Favorit</span> Anda
                </h1>
                <p class="mt-4 max-w-xl mx-auto text-base sm:text-xl text-gray-500">Produk-produk yang Anda sukai ada di sini.</p>
            </div>

            @if($wishlists->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-8">
                    @foreach($wishlists as $wishlist)
                        <div class="group relative bg-white rounded-[2rem] overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-gray-100 flex flex-col active:scale-[0.98] tap-highlight-transparent hover:-translate-y-2"
                             data-reveal style="transition-delay: {{ $loop->index * 50 }}ms"
                             x-data="{
                                 removed: false,
                                 loading: false,
                                 removeItem() {
                                     if (this.loading) return;
                                     this.loading = true;

                                     fetch('{{ route('wishlist.toggle', $wishlist->product->id) }}', {
                                         method: 'POST',
                                         headers: {
                                             'Content-Type': 'application/json',
                                             'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                             'X-Requested-With': 'XMLHttpRequest'
                                         }
                                     })
                                     .then(res => res.json())
                                     .then(data => {
                                         if (data.status === 'removed') {
                                             this.removed = true;
                                         }
                                         this.loading = false;
                                     })
                                     .catch(err => {
                                         this.loading = false;
                                     });
                                 }
                             }"
                             x-show="!removed"
                             x-transition:leave="transition ease-in duration-300"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95">
                            <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden bg-gray-200 group-hover:opacity-75 relative">
                                @if($wishlist->product->images && count($wishlist->product->images) > 0)
                                    <img src="{{ Storage::url($wishlist->product->images[0]) }}" alt="{{ $wishlist->product->name }}" class="h-full w-full object-cover object-center">
                                @else
                                    <img src="https://via.placeholder.com/500?text=No+Image" alt="{{ $wishlist->product->name }}" class="h-full w-full object-cover object-center">
                                @endif

                                <button type="button" @click.prevent="removeItem()"
                                        class="absolute top-2 right-2 p-2 bg-white rounded-full shadow-md text-rose-500 hover:bg-gray-100 transition"
                                        :class="{ 'opacity-50 cursor-not-allowed': loading }">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                </button>
                            </div>
                            <div class="p-4 sm:p-6 flex flex-col flex-1">
                                <div class="flex-1">
                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 group-hover:text-rose-600 transition-colors line-clamp-3">
                                        <a href="{{ route('products.show', $wishlist->product->slug) }}">
                                            {{ $wishlist->product->name }}
                                        </a>
                                    </h3>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ $wishlist->product->category?->name ?? 'Tak Berkategori' }}</p>
                                    <div class="mt-3 flex items-center justify-between">
                                        <span class="text-lg sm:text-xl font-black bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">
                                            IDR {{ number_format($wishlist->product->price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-4 sm:mt-6 grid grid-cols-1 gap-2 sm:gap-3">
                                    <button @click="$dispatch('add-to-cart', { id: {{ $wishlist->product->id }}, quantity: 1 })"
                                            class="w-full bg-rose-500 hover:bg-rose-600 text-white py-2.5 sm:py-3 px-4 rounded-full text-xs sm:text-sm font-bold transition-all active:scale-95 shadow-md flex items-center justify-center gap-2 tap-highlight-transparent">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Ke Keranjang
                                    </button>

                                    <form action="{{ route('wishlist.toggle', $wishlist->product->id) }}" method="POST" @submit.prevent="loading = true; $el.submit()">
                                        @csrf
                                        <button type="submit"
                                            class="w-full bg-gray-50 hover:bg-rose-50 text-gray-500 hover:text-rose-600 py-2.5 sm:py-3 px-4 rounded-full text-xs sm:text-sm font-bold transition-all active:scale-95 flex items-center justify-center gap-2 tap-highlight-transparent"
                                            :class="{ 'opacity-50 pointer-events-none': loading }">
                                            <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            <svg x-show="loading" class="animate-spin h-4 w-4 text-rose-600" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Hapus
                                        </button>
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
