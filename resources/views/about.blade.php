<x-layout>
    <div class="bg-gradient-to-b from-gray-50 to-white min-h-screen">
        <!-- Hero Section -->
        <section class="relative py-20 bg-gradient-to-r from-rose-500 to-purple-600 overflow-hidden">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4">Tentang Kami</h1>
                <p class="text-xl text-white/90 max-w-2xl mx-auto">Mengenal lebih dekat dengan {{ config('app.name') }}</p>
            </div>
        </section>

        <!-- Content -->
        <div class="max-w-4xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
            <!-- Our Story -->
            <div class="mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="w-10 h-10 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </span>
                    Cerita Kami
                </h2>
                <div class="prose prose-lg text-gray-600 max-w-none">
                    <p>{{ config('app.name') }} hadir untuk memberikan pengalaman berbelanja online yang menyenangkan dan terpercaya. Kami berkomitmen menyediakan produk berkualitas tinggi dengan harga yang kompetitif.</p>
                    <p>Didirikan dengan semangat untuk memberikan yang terbaik, kami terus berkembang dan berinovasi untuk memenuhi kebutuhan pelanggan kami.</p>
                </div>
            </div>

            <!-- Our Values -->
            <div class="mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-8 flex items-center">
                    <span class="w-10 h-10 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </span>
                    Nilai-Nilai Kami
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Kualitas Terjamin</h3>
                        <p class="text-gray-600 text-sm">Setiap produk melalui seleksi ketat untuk memastikan kualitas terbaik.</p>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Pengiriman Cepat</h3>
                        <p class="text-gray-600 text-sm">Pesanan diproses dengan cepat dan dikirim ke seluruh Indonesia.</p>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition">
                        <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Pelayanan Prima</h3>
                        <p class="text-gray-600 text-sm">Tim kami siap membantu dan memberikan pengalaman terbaik untuk Anda.</p>
                    </div>
                </div>
            </div>

            <!-- Contact CTA -->
            <div class="bg-gradient-to-r from-rose-500 to-purple-600 rounded-2xl p-8 text-center text-white">
                <h3 class="text-2xl font-bold mb-4">Ada Pertanyaan?</h3>
                <p class="text-white/90 mb-6">Jangan ragu untuk menghubungi kami. Tim kami siap membantu Anda.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('products.index') }}" class="px-6 py-3 bg-white text-rose-600 font-semibold rounded-full hover:bg-gray-100 transition">
                        Mulai Belanja
                    </a>
                    <a href="{{ route('orders.tracking') }}" class="px-6 py-3 border-2 border-white text-white font-semibold rounded-full hover:bg-white/10 transition">
                        Lacak Pesanan
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layout>
