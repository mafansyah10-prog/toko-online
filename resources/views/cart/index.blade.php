<x-layout>
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 mb-12">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">Keranjang</span> Belanja
            </h1>

            {{-- Error/Success Messages --}}
            @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('error') }}
            </div>
            @endif

            @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
            @endif

            <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start xl:gap-x-16">
                <!-- Cart Items -->
                <section aria-labelledby="cart-heading" class="lg:col-span-7">
                    <h2 id="cart-heading" class="sr-only">Item dalam keranjang belanja Anda</h2>

                    @if(session('cart') && count(session('cart')) > 0)
                    <ul role="list" class="divide-y divide-gray-200 bg-white rounded-2xl shadow-sm overflow-hidden">
                        @foreach(session('cart') as $id => $details)
                        <li class="flex py-6 px-6 hover:bg-gray-50 transition-colors">
                            <div class="flex-shrink-0">
                                <img src="{{ isset($details['image']) ? Storage::url($details['image']) : 'https://via.placeholder.com/200' }}" 
                                     alt="{{ $details['name'] }}" 
                                     class="w-24 h-24 rounded-xl object-center object-cover sm:w-32 sm:h-32 shadow-sm">
                            </div>

                            <div class="ml-4 flex-1 flex flex-col justify-between sm:ml-6">
                                <div class="relative pr-9 sm:grid sm:grid-cols-2 sm:gap-x-6 sm:pr-0">
                                    <div>
                                        <div class="flex justify-between">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                <a href="{{ route('products.show', Str::slug($details['name'])) }}" class="hover:text-rose-600 transition">{{ $details['name'] }}</a>
                                            </h3>
                                        </div>
                                        <p class="mt-2 text-lg font-bold text-rose-600">IDR {{ number_format($details['price'], 0, ',', '.') }}</p>
                                    </div>

                                    <div class="mt-4 sm:mt-0 sm:pr-9">
                                        <!-- Quantity Controls -->
                                        @php 
                                            $maxStock = $details['stock'] ?? 999;
                                            $isMaxStock = $details['quantity'] >= $maxStock;
                                        @endphp
                                        <div class="flex items-center border border-gray-200 rounded-full w-fit">
                                            <form action="{{ route('cart.update', $id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="action" value="decrease">
                                                <button type="submit" class="p-2 text-gray-600 hover:text-rose-600 hover:bg-gray-100 rounded-l-full transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                            <span class="px-4 py-1 text-gray-900 font-semibold min-w-[40px] text-center">{{ $details['quantity'] }}</span>
                                            <form action="{{ route('cart.update', $id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="action" value="increase">
                                                <button type="submit" 
                                                    class="p-2 rounded-r-full transition {{ $isMaxStock ? 'text-gray-300 cursor-not-allowed bg-gray-50' : 'text-gray-600 hover:text-rose-600 hover:bg-gray-100' }}"
                                                    {{ $isMaxStock ? 'disabled' : '' }}
                                                    title="{{ $isMaxStock ? 'Stok maksimal tercapai' : 'Tambah quantity' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <!-- Stock Info -->
                                        @if($isMaxStock)
                                        <p class="mt-2 text-xs text-orange-600 font-medium">Stok maksimal: {{ $maxStock }}</p>
                                        @elseif($maxStock <= 5)
                                        <p class="mt-2 text-xs text-orange-500">Tersisa {{ $maxStock }} stok</p>
                                        @endif

                                        <div class="absolute top-0 right-0">
                                            <a href="{{ route('cart.remove', ['id' => $id]) }}" class="-m-2 p-2 inline-flex text-gray-400 hover:text-rose-500 transition">
                                                <span class="sr-only">Hapus</span>
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Item Subtotal -->
                                <div class="mt-4 flex justify-end">
                                    <p class="text-sm text-gray-500">Subtotal: <span class="font-semibold text-gray-900">IDR {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</span></p>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="text-center py-16 bg-white rounded-2xl shadow-sm">
                        <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">Keranjang Anda kosong</h3>
                        <p class="mt-2 text-gray-500">Mulai tambahkan produk ke keranjang Anda!</p>
                        <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-full text-white bg-gradient-to-r from-rose-500 to-purple-600 hover:shadow-lg transform hover:scale-105 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                                Start Belanja
                            </a>
                            <a href="{{ route('orders.tracking') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 hover:border-rose-300 hover:text-rose-600 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                                Lacak Pesanan
                            </a>
                        </div>
                    </div>
                    @endif
                </section>

                <!-- Order Summary -->
                @if(session('cart') && count(session('cart')) > 0)
                <section aria-labelledby="summary-heading" class="mt-16 bg-white rounded-2xl shadow-lg px-6 py-8 lg:mt-0 lg:col-span-5 sticky top-24">
                    <h2 id="summary-heading" class="text-xl font-bold text-gray-900 mb-6">Ringkasan Pesanan</h2>

                    @php $total = 0; @endphp
                    @foreach(session('cart') as $id => $details)
                        @php $total += $details['price'] * $details['quantity'] @endphp
                    @endforeach

                    <dl class="space-y-4">
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-600">Items ({{ array_sum(array_column(session('cart'), 'quantity')) }})</dt>
                            <dd class="font-medium text-gray-900">IDR {{ number_format($total, 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-gray-600">Pengiriman</dt>
                            <dd class="font-medium text-gray-900">Dihitung saat checkout</dd>
                        </div>
                        <div class="border-t border-gray-200 pt-4 flex items-center justify-between">
                            <dt class="text-lg font-bold text-gray-900">Subtotal</dt>
                            <dd class="text-lg font-bold bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">IDR {{ number_format($total, 0, ',', '.') }}</dd>
                        </div>
                    </dl>

                    <div class="mt-8">
                        <a href="{{ route('checkout.index') }}" class="w-full bg-gradient-to-r from-rose-500 to-purple-600 border border-transparent rounded-full shadow-lg py-4 px-4 text-base font-bold text-white hover:shadow-xl hover:scale-[1.02] transform transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 flex justify-center items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                            Lanjut ke Checkout
                        </a>
                    </div>
                    
                    <div class="mt-6 text-center">
                        <a href="{{ route('products.index') }}" class="text-sm text-gray-500 hover:text-rose-600 transition">
                            atau lanjut belanja →
                        </a>
                    </div>

                    <!-- Track Order Link -->
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <a href="{{ route('orders.tracking') }}" class="flex items-center justify-center px-4 py-3 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-rose-300 hover:text-rose-600 transition-all group">
                            <svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-rose-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            Lacak Pesanan
                        </a>
                        <p class="mt-2 text-xs text-center text-gray-400">Punya pesanan? Cek status pengirimannya</p>
                    </div>
                </section>
                @endif
            </div>
        </div>
    </div>
</x-layout>
