<x-layout>
    {{-- Auto-refresh every 30 seconds for real-time status updates --}}
    @push('head')
        <meta http-equiv="refresh" content="30">
    @endpush

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50">
        <div class="max-w-4xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold text-gray-900">
                    Status Pesanan <span class="bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">#{{ $order->id }}</span>
                </h1>
                <p class="mt-3 text-gray-500">Pesanan dibuat pada {{ $order->created_at->format('d M Y, H:i') }}</p>
                
                <!-- Auto-refresh indicator -->
                <div class="mt-4 inline-flex items-center px-3 py-1.5 rounded-full bg-green-50 border border-green-200">
                    <span class="relative flex h-2 w-2 mr-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    <span class="text-xs font-medium text-green-700">Live Update - Refresh otomatis setiap 30 detik</span>
                </div>
            </div>

            <!-- Info Pemesan -->
            <div class="bg-gradient-to-r from-rose-500 to-purple-600 rounded-2xl shadow-xl p-6 mb-8 text-white">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-white/20 rounded-full p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-white/70">Penerima</p>
                            <p class="text-xl font-bold">{{ $order->customer_name }}</p>
                            <p class="text-sm text-white/80 mt-1">{{ $order->customer_email }}</p>
                            <p class="text-sm text-white/80">{{ $order->customer_phone }}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-white/20 rounded-full p-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-white/70">Alamat Pengiriman</p>
                            <p class="text-sm font-medium leading-relaxed">{{ $order->customer_address }}</p>
                            <p class="text-sm font-bold mt-1">{{ $order->customer_city }}, {{ $order->customer_postal_code }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Status Timeline -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Status Pesanan</h2>
                </div>
                <div class="p-6">
                    @php
                        $statuses = ['new', 'processing', 'shipped', 'delivered'];
                        $currentIndex = array_search($order->status, $statuses);
                        if ($order->status === 'cancelled') {
                            $currentIndex = -1;
                        }
                    @endphp

                    @if($order->status === 'cancelled')
                        <div class="flex items-center justify-center py-8">
                            <div class="text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4">
                                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-red-600">Pesanan Dibatalkan</h3>
                                <p class="text-gray-500 mt-2">Pesanan ini telah dibatalkan</p>
                            </div>
                        </div>
                    @else
                        <!-- Timeline -->
                        <div class="relative">
                            <!-- Progress Line -->
                            <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                            <div class="absolute left-8 top-0 w-0.5 bg-gradient-to-b from-rose-500 to-purple-600 transition-all duration-500"
                                 style="height: {{ $currentIndex >= 0 ? (($currentIndex / 3) * 100) : 0 }}%"></div>

                            <!-- Timeline Items -->
                            <div class="space-y-8">
                                @php
                                    $statusLabels = [
                                        'new' => ['label' => 'Pesanan Baru', 'desc' => 'Pesanan Anda telah diterima', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                        'processing' => ['label' => 'Diproses', 'desc' => 'Pesanan sedang diproses dan disiapkan', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'],
                                        'shipped' => ['label' => 'Dikirim', 'desc' => 'Pesanan dalam perjalanan menuju alamat Anda', 'icon' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0'],
                                        'delivered' => ['label' => 'Terkirim', 'desc' => 'Pesanan telah sampai di tujuan', 'icon' => 'M5 13l4 4L19 7'],
                                    ];
                                @endphp

                                @foreach($statuses as $index => $status)
                                    @php
                                        $isCompleted = $currentIndex >= $index;
                                        $isCurrent = $currentIndex === $index;
                                        $info = $statusLabels[$status];
                                    @endphp
                                    <div class="relative flex items-start">
                                        <!-- Circle -->
                                        <div class="flex-shrink-0 relative z-10">
                                            <div class="flex items-center justify-center w-16 h-16 rounded-full 
                                                {{ $isCompleted ? 'bg-gradient-to-r from-rose-500 to-purple-600' : 'bg-gray-200' }}
                                                {{ $isCurrent ? 'ring-4 ring-rose-100' : '' }}
                                                transition-all duration-300">
                                                <svg class="w-6 h-6 {{ $isCompleted ? 'text-white' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $info['icon'] }}"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        
                                        <!-- Content -->
                                        <div class="ml-6 pt-3">
                                            <h4 class="text-base font-semibold {{ $isCompleted ? 'text-gray-900' : 'text-gray-400' }}">
                                                {{ $info['label'] }}
                                            </h4>
                                            <p class="mt-1 text-sm {{ $isCompleted ? 'text-gray-500' : 'text-gray-400' }}">
                                                {{ $info['desc'] }}
                                            </p>
                                            @if($isCurrent)
                                                <span class="inline-flex items-center mt-2 px-3 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-rose-500 to-purple-600 text-white">
                                                    <span class="relative flex h-2 w-2 mr-2">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                                                    </span>
                                                    Status Saat Ini
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Order Items -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h2 class="text-lg font-semibold text-gray-900">Item Pesanan</h2>
                    </div>
                    <ul role="list" class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                        <li class="px-6 py-4 flex items-center hover:bg-gray-50 transition">
                            <div class="flex-shrink-0">
                                @if($item->product && $item->product->images)
                                    <img class="h-14 w-14 rounded-xl object-cover shadow-sm" src="{{ Storage::url($item->product->images[0]) }}" alt="{{ $item->product->name }}">
                                @else
                                    <div class="h-14 w-14 rounded-xl bg-gray-100 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-900">{{ $item->product->name ?? 'Produk Tidak Tersedia' }}</h5>
                                        <p class="text-xs text-gray-500">{{ $item->quantity }}x @ IDR {{ number_format($item->unit_amount, 0, ',', '.') }}</p>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900">IDR {{ number_format($item->total_amount, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Order Summary -->
                <div class="space-y-6">
                    <!-- Payment & Shipping Info -->
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pesanan</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Metode Pembayaran</dt>
                                <dd class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Status Pembayaran</dt>
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
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Metode Pengiriman</dt>
                                <dd class="font-medium text-gray-900">{{ $order->shipping_method }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Total -->
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan</h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Subtotal</dt>
                                <dd class="font-medium text-gray-900">IDR {{ number_format($order->grand_total - $order->shipping_amount, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Ongkos Kirim</dt>
                                <dd class="font-medium text-gray-900">IDR {{ number_format($order->shipping_amount, 0, ',', '.') }}</dd>
                            </div>
                            <div class="border-t border-gray-200 pt-3 flex justify-between">
                                <dt class="text-base font-bold text-gray-900">Total</dt>
                                <dd class="text-base font-bold bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">
                                    IDR {{ number_format($order->grand_total, 0, ',', '.') }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('orders.tracking') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Lacak Pesanan Lain
                </a>
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-gradient-to-r from-rose-500 to-purple-600 hover:from-rose-600 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Lanjutkan Belanja
                </a>
            </div>
        </div>
    </div>
</x-layout>
