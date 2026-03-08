<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $description ?? config('app.name') . ' - Toko online terpercaya dengan produk berkualitas dan harga terbaik. Belanja mudah, pengiriman cepat ke seluruh Indonesia.' }}">
    <meta name="keywords" content="toko online, belanja online, produk berkualitas, {{ config('app.name') }}">
    <meta name="author" content="{{ config('app.name') }}">
    <title>{{ $title ?? config('app.name') . ' - Toko Online Terpercaya' }}</title>
    
    <!-- PWA & Mobile Meta Tags -->
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#f43f5e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="TokoOnline">
    <link rel="apple-touch-icon" href="https://images.unsplash.com/photo-1591085686350-798c0f993407?ixlib=rb-1.2.1&auto=format&fit=crop&w=192&h=192&q=80">

    <!-- Resource Hints -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://images.unsplash.com">
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://images.unsplash.com">

    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Outfit', sans-serif; }
        
        /* Smooth scrolling */
        html { scroll-behavior: smooth; }

        /* Custom Scrollbar for premium feel */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f9fafb; }
        ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 20px; }
        ::-webkit-scrollbar-thumb:hover { background: #d1d5db; }

        /* Skeleton Loading Effect */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
        }
        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Responsive Container Padding */
        @media (max-width: 640px) {
            .container-px { padding-left: 1rem; padding-right: 1rem; }
        }

        /* iOS Safe Area Helper */
        .pb-safe { padding-bottom: env(safe-area-inset-bottom); }
        .pt-safe { padding-top: env(safe-area-inset-top); }

        /* Touch Feedback */
        .tap-highlight-transparent { -webkit-tap-highlight-color: transparent; }

        /* Premium Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Modern Navigation Active Indicator */
        .nav-active-dot {
            width: 4px;
            height: 4px;
            background: #f43f5e;
            border-radius: 50%;
            margin-top: 2px;
            box-shadow: 0 0 8px rgba(244, 63, 94, 0.8);
        }

        /* Reveal Animations */
        [data-reveal] {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }
        [data-reveal].revealed {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
    @php
        $wishlistCount = Auth::check() 
            ? auth()->user()->wishlists()->count() 
            : count(session()->get('wishlist', []));
    @endphp
    <script src="https://instant.page/5.2.0" type="module" integrity="sha384-jnZyxPjiipSvoPKYmNo6IydGcaKY4tY1y+7W71o30T6Y27qSWh08719m3G8+W69d"></script>
    @stack('head')
</head>
<body class="h-full text-gray-900 antialiased selection:bg-rose-500 selection:text-white flex flex-col" 
      x-data="globalApp" 
      @scroll.window="scrolled = (window.pageYOffset > 20)"
      @add-to-cart.window="addToCart($event.detail.id, $event.detail.quantity)"
      @open-cart.window="cartOpen = true">

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('globalApp', () => ({
                mobileMenuOpen: false,
                scrolled: false,
                cartOpen: false,
                cartCount: {{ session('cart') ? array_sum(array_column(session('cart'), 'quantity')) : 0 }},
                cartData: null,
                searchQuery: '',
                searchResults: [],
                searchLoading: false,

                init() {
                    this.fetchCart();
                },

                fetchCart() {
                    fetch('{{ route('cart.data') }}')
                        .then(res => res.json())
                        .then(data => {
                            this.cartCount = data.cart_count;
                            this.cartData = data.cart_data;
                        });
                },

                addToCart(id, quantity = 1) {
                    fetch(`/add-to-cart-ajax/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ quantity })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            this.cartCount = data.cart_count;
                            this.cartData = data.cart_data;
                            this.cartOpen = true;
                        } else {
                            window.dispatchEvent(new CustomEvent('toast', { detail: { message: data.message, type: 'error' } }));
                        }
                    });
                },

                removeFromCart(id) {
                    fetch(`/remove-from-cart/${id}`)
                        .then(() => this.fetchCart());
                },

                search() {
                    if (this.searchQuery.length < 2) {
                        this.searchResults = [];
                        return;
                    }
                    this.searchLoading = true;
                    fetch(`/api/live-search?q=${this.searchQuery}`)
                        .then(res => res.json())
                        .then(data => {
                            this.searchResults = data;
                            this.searchLoading = false;
                        });
                }
            }));
        });

        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>

    <!-- Toast Notifications -->
    @if(session('success'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 4000)"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-20 right-4 z-50 max-w-sm w-full bg-white shadow-2xl rounded-xl pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900">Berhasil!</p>
                    <p class="mt-1 text-sm text-gray-500">{{ session('success') }}</p>
                </div>
                <div class="ml-4 flex flex-shrink-0">
                    <button @click="show = false" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Progress bar -->
        <div class="h-1 bg-gray-200">
            <div class="h-full bg-gradient-to-r from-green-400 to-green-500 animate-[shrink_4s_linear_forwards]" style="animation: shrink 4s linear forwards;"></div>
        </div>
    </div>
    <style>
        @keyframes shrink {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 4000)"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-20 right-4 z-50 max-w-sm w-full bg-white shadow-2xl rounded-xl pointer-events-auto ring-1 ring-red-200 overflow-hidden">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900">Gagal</p>
                    <p class="mt-1 text-sm text-gray-500">{{ session('error') }}</p>
                </div>
                <div class="ml-4 flex flex-shrink-0">
                    <button @click="show = false" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 8000)"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-20 right-4 z-50 max-w-sm w-full bg-white shadow-2xl rounded-xl pointer-events-auto ring-1 ring-red-200 overflow-hidden">
        <div class="p-4 border-l-4 border-red-500">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-bold text-red-600">Perhatian!</p>
                    <ul class="mt-1 text-xs text-gray-500 list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="ml-4 flex flex-shrink-0">
                    <button @click="show = false" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="h-1 bg-red-100">
            <div class="h-full bg-red-500" style="animation: shrink 8s linear forwards;"></div>
        </div>
    </div>
    @endif

    <!-- Desktop Navbar -->
    <nav class="sticky top-0 z-40 transition-all duration-300 glass border-b border-gray-100/50 shadow-sm" x-data="{ atTop: true }" @scroll.window="atTop = (window.pageYOffset > 10 ? false : true)" :class="{ 'py-1': !atTop, 'py-2': atTop }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center mr-8">
                    <span class="text-xl md:text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">
                        {{ config('app.name') }}
                    </span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8 flex-1">
                    <a href="{{ route('home') }}" class="font-medium transition duration-200 relative group {{ request()->routeIs('home') ? 'text-rose-500' : 'text-gray-700 hover:text-rose-500' }}">
                        Beranda
                        <span class="absolute -bottom-1 left-0 h-0.5 bg-rose-500 transition-all duration-300 {{ request()->routeIs('home') ? 'w-full' : 'w-0 group-hover:w-full' }}"></span>
                    </a>
                    <a href="{{ route('products.index') }}" class="font-medium transition duration-200 relative group {{ request()->routeIs('products.*') ? 'text-rose-500' : 'text-gray-700 hover:text-rose-500' }}">
                        Produk
                        <span class="absolute -bottom-1 left-0 h-0.5 bg-rose-500 transition-all duration-300 {{ request()->routeIs('products.*') ? 'w-full' : 'w-0 group-hover:w-full' }}"></span>
                    </a>
                    <a href="{{ route('orders.tracking') }}" class="font-medium transition duration-200 relative group {{ request()->routeIs('orders.*') ? 'text-rose-500' : 'text-gray-700 hover:text-rose-500' }}">
                        Lacak Pesanan
                        <span class="absolute -bottom-1 left-0 h-0.5 bg-rose-500 transition-all duration-300 {{ request()->routeIs('orders.*') ? 'w-full' : 'w-0 group-hover:w-full' }}"></span>
                    </a>
                </div>

                <!-- Live Search -->
                <div class="hidden lg:block flex-1 max-w-sm mx-8 relative" x-cloak>
                    <div class="relative">
                        <input type="text" 
                               x-model="searchQuery" 
                               @input.debounce.300ms="search()"
                               placeholder="Cari produk..." 
                               class="w-full bg-gray-100 border-none rounded-full py-2 px-10 focus:ring-2 focus:ring-rose-500 transition-all text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <div x-show="searchLoading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="animate-spin h-4 w-4 text-rose-500" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div x-show="searchResults.length > 0" 
                         @click.away="searchResults = []"
                         class="absolute mt-2 w-full bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50">
                        <template x-for="product in searchResults" :key="product.url">
                            <a :href="product.url" class="flex items-center p-3 hover:bg-gray-50 transition border-b border-gray-50 last:border-0">
                                <img :src="product.image" class="w-10 h-10 rounded-lg object-cover mr-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900" x-text="product.name"></p>
                                    <p class="text-xs text-rose-500 font-bold" x-text="product.price"></p>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>

                <!-- Icons -->
                <div class="flex items-center space-x-2 md:space-x-4">
                    <a href="{{ route('wishlist.index') }}" class="hidden md:inline-flex relative p-2 transition duration-200 rounded-full {{ request()->routeIs('wishlist.*') ? 'text-rose-500 bg-rose-50' : 'text-gray-700 hover:text-rose-500 hover:bg-gray-100' }} group active:scale-90 tap-highlight-transparent">
                        <svg class="w-6 h-6 transform group-hover:-translate-y-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        @if($wishlistCount > 0)
                            <span class="absolute -top-1 -right-1 bg-gradient-to-r from-rose-500 to-purple-600 text-white text-[10px] font-bold w-5 h-4 rounded-full flex items-center justify-center shadow-lg">{{ $wishlistCount }}</span>
                        @endif
                    </a>
                    <a href="javascript:void(0)" @click="cartOpen = true" class="hidden md:inline-flex relative p-2 transition duration-200 rounded-full {{ request()->routeIs('cart.*') ? 'text-rose-500 bg-rose-50' : 'text-gray-700 hover:text-rose-500 hover:bg-gray-100' }} group active:scale-90 tap-highlight-transparent">
                        <svg class="w-6 h-6 transform group-hover:-translate-y-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <template x-if="cartCount > 0">
                            <span class="absolute -top-1 -right-1 bg-gradient-to-r from-rose-500 to-purple-600 text-white text-[10px] font-bold w-5 h-4 rounded-full flex items-center justify-center shadow-lg" x-text="cartCount"></span>
                        </template>
                    </a>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden text-gray-700 hover:text-rose-500 p-2 transition rounded-md active:scale-90 tap-highlight-transparent">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!mobileMenuOpen"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak x-show="mobileMenuOpen"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden bg-white border-t border-gray-100 absolute w-full shadow-lg" x-cloak @click.away="mobileMenuOpen = false">
            <div class="px-4 pt-4 pb-2 border-b border-gray-50">
                <div class="relative">
                    <input type="text" 
                           x-model="searchQuery" 
                           @input.debounce.300ms="search()"
                           placeholder="Cari produk..." 
                           class="w-full bg-gray-100 border-none rounded-xl py-2 px-10 focus:ring-2 focus:ring-rose-500 transition-all text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
                <!-- Mobile Search Results -->
                <div x-show="searchResults.length > 0" class="mt-2 space-y-2 pb-2">
                    <template x-for="product in searchResults" :key="product.url">
                        <a :href="product.url" class="flex items-center p-2 hover:bg-gray-50 rounded-lg transition">
                            <img :src="product.image" class="w-8 h-8 rounded object-cover mr-3">
                            <div>
                                <p class="text-xs font-semibold text-gray-900" x-text="product.name"></p>
                                <p class="text-[10px] text-rose-500 font-bold" x-text="product.price"></p>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('home') ? 'text-rose-500 bg-rose-50' : 'text-gray-700 hover:text-rose-500 hover:bg-gray-50' }} transition">Beranda</a>
                <a href="{{ route('products.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('products.*') ? 'text-rose-500 bg-rose-50' : 'text-gray-700 hover:text-rose-500 hover:bg-gray-50' }} transition">Produk</a>
                <a href="{{ route('orders.tracking') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('orders.*') ? 'text-rose-500 bg-rose-50' : 'text-gray-700 hover:text-rose-500 hover:bg-gray-50' }} transition">Lacak Pesanan</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow pt-16 pb-20 md:pb-0">
        {{ $slot }}
    </main>

    <!-- Mobile Bottom Navigation -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 glass border-t border-white/20 z-50 px-4 py-3 pb-safe shadow-[0_-8px_30px_rgba(0,0,0,0.08)]">
        <div class="flex justify-between items-center max-w-lg mx-auto">
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-0.5 transition active:scale-90 tap-highlight-transparent {{ request()->routeIs('home') ? 'text-rose-500' : 'text-gray-400 hover:text-rose-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                <span class="text-[9px] uppercase tracking-tighter font-medium">Beranda</span>
                <div class="{{ request()->routeIs('home') ? 'nav-active-dot' : 'h-1' }}"></div>
            </a>
            <a href="{{ route('products.index') }}" class="flex flex-col items-center gap-0.5 transition active:scale-90 tap-highlight-transparent {{ request()->routeIs('products.*') ? 'text-rose-500' : 'text-gray-400 hover:text-rose-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                <span class="text-[9px] uppercase tracking-tighter font-medium">Toko</span>
                <div class="{{ request()->routeIs('products.*') ? 'nav-active-dot' : 'h-1' }}"></div>
            </a>
            <a href="{{ route('wishlist.index') }}" class="flex flex-col items-center gap-0.5 relative transition active:scale-90 tap-highlight-transparent {{ request()->routeIs('wishlist.*') ? 'text-rose-500' : 'text-gray-400 hover:text-rose-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                @if($wishlistCount > 0)
                    <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[8px] font-bold w-4 h-4 rounded-full flex items-center justify-center shadow-lg border border-white">{{ $wishlistCount }}</span>
                @endif
                <span class="text-[9px] uppercase tracking-tighter font-medium">Favorit</span>
                <div class="{{ request()->routeIs('wishlist.*') ? 'nav-active-dot' : 'h-1' }}"></div>
            </a>
            <button @click="cartOpen = true" class="flex flex-col items-center gap-0.5 relative text-gray-400 hover:text-rose-500 transition active:scale-90 tap-highlight-transparent">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                <template x-if="cartCount > 0">
                    <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[8px] font-bold w-4 h-4 rounded-full flex items-center justify-center shadow-lg border border-white" x-text="cartCount"></span>
                </template>
                <span class="text-[9px] uppercase tracking-tighter font-medium">Keranjang</span>
                <div class="h-1"></div>
            </button>
            <a href="{{ route('orders.tracking') }}" class="flex flex-col items-center gap-0.5 transition active:scale-90 tap-highlight-transparent {{ request()->routeIs('orders.tracking') ? 'text-rose-500' : 'text-gray-400 hover:text-rose-500' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                <span class="text-[9px] uppercase tracking-tighter font-medium">Lacak</span>
                <div class="{{ request()->routeIs('orders.tracking') ? 'nav-active-dot' : 'h-1' }}"></div>
            </a>
        </div>
    </div>

    <!-- Side Cart Drawer -->
    <div x-show="cartOpen" class="fixed inset-0 overflow-hidden z-[100]" x-cloak>
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="cartOpen = false" x-show="cartOpen" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
            
            <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                <div class="w-screen max-w-md" x-show="cartOpen" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                    <div class="h-full flex flex-col bg-white shadow-xl overflow-y-scroll">
                        <div class="flex-1 py-6 overflow-y-auto px-4 sm:px-6">
                            <div class="flex items-start justify-between">
                                <h2 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">Keranjang Belanja</h2>
                                <div class="ml-3 h-7 flex items-center">
                                    <button @click="cartOpen = false" class="text-gray-400 hover:text-rose-500 transition">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-8">
                                <div class="flow-root">
                                    <template x-if="cartCount > 0 && cartData">
                                        <ul role="list" class="-my-6 divide-y divide-gray-100">
                                            <template x-for="item in cartData.items" :key="item.id">
                                                <li class="py-6 flex">
                                                    <div class="flex-shrink-0 w-20 h-20 border border-gray-100 rounded-xl overflow-hidden shadow-sm">
                                                        <img :src="item.image" :alt="item.name" class="w-full h-full object-center object-cover">
                                                    </div>
                                                    <div class="ml-4 flex-1 flex flex-col">
                                                        <div>
                                                            <div class="flex justify-between text-base font-semibold text-gray-900">
                                                                <h3><a :href="item.url" x-text="item.name" class="hover:text-rose-600 transition"></a></h3>
                                                                <p class="ml-4 text-rose-500" x-text="item.subtotal"></p>
                                                            </div>
                                                        </div>
                                                        <div class="flex-1 flex items-end justify-between text-sm">
                                                            <p class="text-gray-500" x-text="'Qty ' + item.quantity"></p>
                                                            <div class="flex">
                                                                <button @click="removeFromCart(item.id)" class="font-medium text-gray-400 hover:text-rose-500 transition">Hapus</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </template>
                                        </ul>
                                    </template>
                                    <template x-if="cartCount === 0">
                                        <div class="text-center py-20">
                                            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                            <p class="mt-4 text-gray-500">Keranjang Anda masih kosong</p>
                                            <a href="{{ route('products.index') }}" @click="cartOpen = false" class="mt-6 inline-block text-rose-600 font-bold hover:text-rose-700">Mulai Belanja &rarr;</a>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 py-6 px-4 sm:px-6" x-show="cartCount > 0">
                            <div class="flex justify-between text-base font-bold text-gray-900">
                                <p>Total</p>
                                <p class="text-xl bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600" x-text="cartData?.total"></p>
                            </div>
                            <p class="mt-0.5 text-sm text-gray-500 italic">Sudah termasuk pajak. Pengiriman dihitung saat checkout.</p>
                            <div class="mt-6">
                                <a href="{{ route('checkout.index') }}" class="flex justify-center items-center px-6 py-3 border border-transparent rounded-full shadow-lg text-base font-bold text-white bg-gradient-to-r from-rose-500 to-purple-600 hover:shadow-xl transform hover:scale-[1.02] transition-all">Checkout Sekarang</a>
                            </div>
                            <div class="mt-6 flex justify-center text-sm text-center text-gray-500">
                                <p>atau <button @click="cartOpen = false" class="text-rose-600 font-bold hover:text-rose-700 ml-1">Lanjut Belanja &rarr;</button></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Draggable Floating Emergency Contact Button -->
    <div x-data="{ 
            isDragging: false, 
            x: window.innerWidth - 80, 
            y: window.innerHeight - 80,
            startX: 0, 
            startY: 0,
            drag(e) {
                if (!this.isDragging) return;
                const clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
                const clientY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
                
                this.x = Math.min(window.innerWidth - 60, Math.max(20, clientX - 30));
                this.y = Math.min(window.innerHeight - 60, Math.max(20, clientY - 30));
            },
            startDrag(e) {
                this.isDragging = true;
                const clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
                const clientY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
                this.startX = clientX;
                this.startY = clientY;
            },
            stopDrag(e) {
                if (!this.isDragging) return;
                const clientX = e.type.includes('touch') ? e.changedTouches[0].clientX : e.clientX;
                const clientY = e.type.includes('touch') ? e.changedTouches[0].clientY : e.clientY;
                
                if (Math.abs(clientX - this.startX) < 5 && Math.abs(clientY - this.startY) < 5) {
                    window.open('https://wa.me/6289601905406?text=Halo%20Admin%2C%20saya%20butuh%20bantuan%20mengenai%20pesanan%20saya', '_blank');
                }
                this.isDragging = false;
            }
         }"
         x-init="$nextTick(() => { x = window.innerWidth - 100; y = window.innerHeight - 150 })"
         @mousemove.window="drag"
         @touchmove.window.passive="drag"
         @mouseup.window="stopDrag"
         @touchend.window="stopDrag"
         class="fixed z-50 touch-none select-none flex flex-col items-center group"
         :style="`left: ${x}px; top: ${y}px; cursor: isDragging ? 'grabbing' : 'grab'`"
         @mousedown="startDrag"
         @touchstart.passive="startDrag">
        
        <!-- Label Bantuan -->
        <div class="mb-2 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-full shadow-lg border border-rose-100 opacity-100 group-hover:scale-105 transition-all duration-300">
            <span class="text-[10px] font-bold bg-clip-text text-transparent bg-gradient-to-r from-rose-600 to-purple-600 whitespace-nowrap px-1">Butuh Bantuan?</span>
        </div>

        <div class="bg-gradient-to-br from-rose-500 to-purple-600 text-white p-4 rounded-full shadow-[0_8px_25px_rgba(225,29,72,0.4)] hover:shadow-[0_12px_35px_rgba(225,29,72,0.6)] transform hover:scale-110 active:scale-95 transition-all duration-300 flex items-center justify-center pointer-events-none ring-4 ring-white/20">
            <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <h3 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-rose-400 to-purple-400 mb-4">{{ config('app.name') }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">Produk premium berkualitas untuk gaya hidup modern Anda. Dirancang untuk mereka yang menghargai detail terbaik.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-6 text-white tracking-wider uppercase text-xs">Belanja</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="{{ route('products.index') }}" class="hover:text-rose-400 transition-colors duration-200">Semua Produk</a></li>
                        <li><a href="{{ route('home') }}" class="hover:text-rose-400 transition-colors duration-200">Produk Unggulan</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-6 text-white tracking-wider uppercase text-xs">Bantuan</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="{{ route('orders.tracking') }}" class="hover:text-rose-400 transition-colors duration-200">Lacak Pesanan</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-rose-400 transition-colors duration-200">Tentang Kami</a></li>
                    </ul>
                </div>
                <div>
                <h4 class="font-semibold mb-6 text-white tracking-wider uppercase text-xs">Ikuti Kami</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition transform hover:scale-110">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.468 4.66c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition transform hover:scale-110">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition transform hover:scale-110">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Hak cipta dilindungi.</p>
            </div>
        </div>
    </footer>
    <script>
        // Simple Scroll Reveal Effect
        document.addEventListener('DOMContentLoaded', () => {
            const reveals = document.querySelectorAll('[data-reveal]');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                    }
                });
            }, { threshold: 0.1 });

            reveals.forEach(el => observer.observe(el));
        });
    </script>
</body>
</html>
