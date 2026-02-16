<x-layout>
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <!-- Success Header -->
            <div class="text-center mb-12">
                <div class="mx-auto flex items-center justify-center w-20 h-20 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full shadow-lg mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-base font-semibold text-green-600 tracking-wide uppercase">Terima Kasih!</h2>
                <p class="mt-2 text-4xl font-extrabold text-gray-900 sm:text-5xl">Pesanan Berhasil Dibuat</p>
                <p class="max-w-xl mt-4 mx-auto text-xl text-gray-500">Pesanan Anda telah diterima dan sedang diproses.</p>
            </div>

            <!-- Order Info Card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-rose-500 to-purple-600">
                    <div class="flex justify-between items-center text-white">
                        <h3 class="text-lg font-bold">Pesanan #{{ $order->id }}</h3>
                        <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium backdrop-blur-sm">{{ ucfirst($order->status) }}</span>
                    </div>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Metode Pembayaran</p>
                            <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Status Pembayaran</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->payment_status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-100 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Pembayaran</span>
                            <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">IDR {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Instructions -->
            @if(in_array($order->payment_method, ['bank_transfer', 'e_wallet']) && $order->payment_status !== 'paid')
            <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-2xl p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 w-full">
                        <h3 class="text-lg font-medium text-yellow-900">Instruksi Pembayaran</h3>
                        <div class="mt-2 text-sm text-yellow-800">
                            <p class="mb-2">Silakan selesaikan pembayaran untuk memproses pesanan Anda.</p>
                            
                            @if($order->payment_method == 'bank_transfer')
                            <div class="bg-white p-4 rounded-xl border border-yellow-200 mt-3">
                                <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">Bank BCA</p>
                                <div class="flex justify-between items-center mt-1">
                                    <p class="text-xl font-mono font-bold text-gray-900">2190336380</p>
                                    <button onclick="navigator.clipboard.writeText('2190336380')" class="text-rose-600 text-sm font-medium hover:text-rose-700">Salin</button>
                                </div>
                                <p class="text-gray-600 text-sm mt-1">a.n. MUHAMMAD AFANSYAH</p>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-yellow-200 mt-3">
                                <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">Bank Mandiri</p>
                                <div class="flex justify-between items-center mt-1">
                                    <p class="text-xl font-mono font-bold text-gray-900">700011875460</p>
                                    <button onclick="navigator.clipboard.writeText('700011875460')" class="text-rose-600 text-sm font-medium hover:text-rose-700">Salin</button>
                                </div>
                                <p class="text-gray-600 text-sm mt-1">a.n. NAILA AZ ZAHRA</p>
                            </div>
                            @elseif($order->payment_method == 'e_wallet')
                            <div class="bg-white p-4 rounded-xl border border-yellow-200 mt-3">
                                <p class="text-gray-500 text-xs uppercase tracking-wide font-semibold">GoPay / OVO / DANA</p>
                                <div class="flex justify-between items-center mt-1">
                                    <p class="text-xl font-mono font-bold text-gray-900">0896 0190 5406</p>
                                    <button onclick="navigator.clipboard.writeText('089601905406')" class="text-rose-600 text-sm font-medium hover:text-rose-700">Salin</button>
                                </div>
                                <p class="text-gray-600 text-sm mt-1">a.n. MUHAMMAD AFANSYAH</p>
                            </div>
                            @endif

                            <div class="mt-4 p-3 bg-yellow-100 rounded-lg">
                                <p class="font-medium">Total yang harus ditransfer:</p>
                                <p class="text-2xl font-bold text-rose-600">IDR {{ number_format($order->grand_total, 0, ',', '.') }}</p>
                            </div>
                            
                            <p class="mt-4">Setelah transfer, mohon kirimkan bukti transfer ke WhatsApp admin kami di <a href="https://wa.me/6289601905406?text=Konfirmasi%20Pesanan%20%23{{ $order->id }}" target="_blank" class="font-bold underline text-green-700">0896 0190 5406</a> dengan menyertakan ID Pesanan Anda.</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('orders.show', $order->id) }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Lihat Detail Pesanan
                </a>
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-full text-white bg-gradient-to-r from-rose-500 to-purple-600 hover:shadow-lg transform hover:scale-105 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Lanjut Belanja
                </a>
            </div>
        </div>
    </div>
</x-layout>
