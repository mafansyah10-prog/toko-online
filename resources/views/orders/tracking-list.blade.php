<x-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50">
        <div class="max-w-4xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            
            <!-- Header -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-r from-rose-500 to-purple-600 mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold text-gray-900">
                    Daftar <span class="bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">Pesanan</span>
                </h1>
                <p class="mt-3 text-gray-500">Ditemukan {{ $orders->count() }} pesanan dengan email Anda</p>
            </div>

            <!-- Orders List -->
            <div class="space-y-4">
                @foreach($orders as $order)
                @php
                    $statusColors = [
                        'new' => 'bg-blue-100 text-blue-800',
                        'processing' => 'bg-yellow-100 text-yellow-800',
                        'shipped' => 'bg-purple-100 text-purple-800',
                        'delivered' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                    ];
                    $statusLabels = [
                        'new' => 'Pesanan Baru',
                        'processing' => 'Diproses',
                        'shipped' => 'Dikirim',
                        'delivered' => 'Terkirim',
                        'cancelled' => 'Dibatalkan',
                    ];
                @endphp
                <a href="{{ route('orders.show', $order->id) }}" class="block bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-r from-rose-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                                    #{{ $order->id }}
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900 group-hover:text-rose-600 transition">
                                        Pesanan #{{ $order->id }}
                                    </h3>
                                    <p class="text-sm text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                            <div class="mt-4 sm:mt-0 flex items-center space-x-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                </span>
                                <span class="text-lg font-bold bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">
                                    IDR {{ number_format($order->grand_total, 0, ',', '.') }}
                                </span>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-rose-500 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Order Items Preview -->
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="flex items-center -space-x-2">
                                @foreach($order->items->take(4) as $item)
                                    @if($item->product && $item->product->images)
                                        <img class="w-10 h-10 rounded-full border-2 border-white object-cover" src="{{ Storage::url($item->product->images[0]) }}" alt="{{ $item->product->name }}">
                                    @else
                                        <div class="w-10 h-10 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                @endforeach
                                @if($order->items->count() > 4)
                                    <div class="w-10 h-10 rounded-full border-2 border-white bg-gray-100 flex items-center justify-center text-xs font-medium text-gray-500">
                                        +{{ $order->items->count() - 4 }}
                                    </div>
                                @endif
                                <span class="ml-4 text-sm text-gray-500">{{ $order->items->count() }} item</span>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Action Buttons -->
            <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('orders.tracking') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Cari dengan Email Lain
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
