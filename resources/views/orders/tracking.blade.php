<x-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50">
        <div class="max-w-lg mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-r from-rose-500 to-purple-600 mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold text-gray-900">
                    Lacak <span class="bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">Pesanan</span>
                </h1>
                <p class="mt-3 text-gray-500">Masukkan email Anda untuk melacak status pesanan</p>
            </div>

            <!-- Error Message -->
            @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
            @endif

            <!-- Tracking Form -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-8">
                    <form action="{{ route('orders.track') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <!-- Order ID Input -->
                        <div>
                            <label for="order_id" class="block text-sm font-medium text-gray-700 mb-2">
                                ID Pesanan (Opsional)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                    </svg>
                                </div>
                                <input 
                                    type="text" 
                                    name="order_id" 
                                    id="order_id" 
                                    placeholder="Contoh: 12345"
                                    value="{{ old('order_id') }}"
                                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all duration-200"
                                >
                            </div>
                            @error('order_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input 
                                    type="email" 
                                    name="email" 
                                    id="email" 
                                    required
                                    placeholder="email@contoh.com"
                                    value="{{ old('email') }}"
                                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all duration-200"
                                >
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit"
                            class="w-full flex items-center justify-center px-6 py-4 border border-transparent text-base font-semibold rounded-xl text-white bg-gradient-to-r from-rose-500 to-purple-600 hover:from-rose-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 transform hover:scale-[1.02] transition-all duration-200 shadow-lg hover:shadow-xl"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Lacak Pesanan
                        </button>
                    </form>
                </div>

                <!-- Help Section -->
                <div class="bg-gray-50 px-8 py-6 border-t border-gray-100">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-gray-500">
                            Masukkan email yang Anda gunakan saat checkout untuk melihat semua pesanan Anda.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Back to Home -->
            <div class="mt-8 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</x-layout>
