<x-layout>
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900">
                        Pesanan <span class="bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">#{{ $order->id }}</span>
                    </h1>
                    <p class="mt-1 text-gray-500">Dibuat pada {{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="mt-4 sm:mt-0 flex gap-2">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                        @if($order->status == 'new') bg-blue-100 text-blue-800
                        @elseif($order->status == 'processing') bg-yellow-100 text-yellow-800
                        @elseif($order->status == 'shipped') bg-purple-100 text-purple-800
                        @elseif($order->status == 'delivered') bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Order Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Order Items -->
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900">Item Pesanan</h2>
                        </div>
                        <ul role="list" class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                            <li class="px-6 py-4 flex items-center hover:bg-gray-50 transition">
                                <div class="flex-shrink-0">
                                    @if($item->product && $item->product->images)
                                        <img class="h-16 w-16 rounded-xl object-cover shadow-sm" src="{{ Storage::url($item->product->images[0]) }}" alt="{{ $item->product->name }}">
                                    @else
                                        <div class="h-16 w-16 rounded-xl bg-gray-100 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h5 class="text-sm font-medium text-gray-900">{{ $item->product->name ?? 'Produk Tidak Tersedia' }}</h5>
                                            <p class="text-sm text-gray-500">{{ $item->quantity }}x @ IDR {{ number_format($item->unit_amount, 0, ',', '.') }}</p>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-900">IDR {{ number_format($item->total_amount, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Payment Info -->
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Info Pembayaran</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Metode</dt>
                                <dd class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Status</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($order->payment_status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->payment_status == 'paid') bg-green-100 text-green-800
                                        @elseif($order->payment_status == 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Order Total -->
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Total Pesanan</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Subtotal</dt>
                                <dd class="font-medium text-gray-900">IDR {{ number_format($order->grand_total - $order->shipping_amount, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Pengiriman ({{ $order->shipping_method }})</dt>
                                <dd class="font-medium text-gray-900">IDR {{ number_format($order->shipping_amount, 0, ',', '.') }}</dd>
                            </div>
                            <div class="border-t border-gray-200 pt-3 flex justify-between">
                                <dt class="text-base font-bold text-gray-900">Total</dt>
                                <dd class="text-base font-bold bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">IDR {{ number_format($order->grand_total, 0, ',', '.') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Notes -->
                    @if($order->notes)
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Catatan Pesanan</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $order->notes }}</p>
                    </div>
                    @endif

                    <!-- Continue Shopping -->
                    <a href="{{ route('home') }}" class="w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-full text-white bg-gradient-to-r from-rose-500 to-purple-600 hover:shadow-lg transform hover:scale-105 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layout>
