<div class="h-screen w-screen overflow-hidden bg-[#0f172a]" 
    wire:poll.keep-alive.10s="checkNewOrders" 
    x-data="{ 
        paymentModalOpen: false, 
        successModalOpen: false, 
        historyModalOpen: false,
        newOrdersModalOpen: false,
        orderDetailModalOpen: false, // Added order detail modal state
        orderId: null,
        changeAmount: 0,
        paymentMethod: @entangle('paymentMethod')
    }"
    x-on:order-completed.window="successModalOpen = true; orderId = $event.detail.orderId; changeAmount = $event.detail.changeAmount"
    x-on:open-new-orders-modal.window="newOrdersModalOpen = true"
    x-on:trigger-order-detail.window="$wire.loadOrderDetail($event.detail.orderId)" 
    x-on:open-order-detail-modal.window="orderDetailModalOpen = true">
    
    {{-- Audio Effects --}}
    <audio id="beep-sound" src="{{ asset('sounds/beep.mp3') }}" preload="auto"></audio>
    <audio id="cash-sound" src="{{ asset('sounds/cash.mp3') }}" preload="auto"></audio>

    {{-- Main Container (Full Screen, Fixed) --}}
    <div class="fixed inset-0 pt-0 lg:pt-0 bg-[#0f172a] text-white overflow-hidden z-0 flex flex-col lg:flex-row font-sans antialiased selection:bg-rose-500 selection:text-white">


        
        {{-- COLUMN 1: SIDEBAR CATEGORIES (Fixed Left/Top) --}}
        <div class="w-full lg:w-32 xl:w-40 h-20 lg:h-full flex flex-row lg:flex-col items-center bg-[#1e293b]/50 backdrop-blur-xl border-b lg:border-b-0 lg:border-r border-white/5 py-2 lg:py-6 px-4 lg:px-0 space-x-4 lg:space-x-0 lg:space-y-4 overflow-x-auto lg:overflow-x-hidden lg:overflow-y-auto no-scrollbar z-20 shrink-0">
            {{-- Back to Dashboard --}}
            <a href="/admin"
                class="group flex flex-col items-center justify-center p-2 lg:p-3 w-16 h-16 lg:w-24 lg:h-24 rounded-2xl bg-white/5 hover:bg-rose-500 hover:shadow-[0_0_20px_rgba(244,63,94,0.4)] border border-white/5 transition-all duration-300 shrink-0">
                <div class="text-gray-400 group-hover:text-white mb-1 lg:mb-2 transition-colors">
                    <x-heroicon-o-arrow-left-on-rectangle class="w-6 h-6 lg:w-8 lg:h-8" />
                </div>
                <span class="text-[8px] lg:text-[10px] md:text-xs font-bold uppercase tracking-wider text-center leading-tight text-gray-400 group-hover:text-white">
                    Keluar
                </span>
            </a>

            {{-- History Button --}}
            <button @click="historyModalOpen = true"
                class="group flex flex-col items-center justify-center p-2 lg:p-3 w-16 h-16 lg:w-24 lg:h-24 rounded-2xl bg-white/5 hover:bg-sky-500 hover:shadow-[0_0_20px_rgba(14,165,233,0.4)] border border-white/5 transition-all duration-300 shrink-0">
                <div class="text-gray-400 group-hover:text-white mb-1 lg:mb-2 transition-colors">
                    <x-heroicon-o-clock class="w-6 h-6 lg:w-8 lg:h-8" />
                </div>
                <span class="text-[8px] lg:text-[10px] md:text-xs font-bold uppercase tracking-wider text-center leading-tight text-gray-400 group-hover:text-white">
                    Riwayat
                </span>
            </button>

            {{-- New Orders Bell --}}
            <button @click="newOrdersModalOpen = true"
                class="group flex flex-col items-center justify-center p-2 lg:p-3 w-16 h-16 lg:w-24 lg:h-24 rounded-2xl transition-all duration-300 relative overflow-hidden shrink-0
                {{ $newOrdersCount > 0 ? 'bg-rose-500 hover:bg-rose-600 shadow-[0_0_20px_rgba(244,63,94,0.4)] animate-pulse' : 'bg-white/5 hover:bg-white/10 border border-white/5' }}">
                <div class="text-white mb-1 lg:mb-2 transition-colors relative">
                    <x-heroicon-o-bell class="w-6 h-6 lg:w-8 lg:h-8" />
                    @if($newOrdersCount > 0)
                        <span class="absolute -top-1 -right-1 bg-white text-rose-600 text-[10px] font-black w-4 h-4 rounded-full flex items-center justify-center">
                            {{ $newOrdersCount }}
                        </span>
                    @endif
                </div>
                <span class="text-[8px] lg:text-[10px] md:text-xs font-bold uppercase tracking-wider text-center leading-tight {{ $newOrdersCount > 0 ? 'text-white' : 'text-gray-400 group-hover:text-white' }}">
                    Pesanan
                </span>
            </button>

            <button wire:click="$set('selectedCategory', 'all')"
                class="group flex flex-col items-center justify-center p-2 lg:p-3 w-16 h-16 lg:w-24 lg:h-24 rounded-2xl transition-all duration-300 relative overflow-hidden shrink-0
                {{ $this->selectedCategory === 'all' 
                    ? 'bg-gradient-to-br from-rose-500 to-pink-600 shadow-[0_0_20px_rgba(244,63,94,0.4)] scale-105' 
                    : 'bg-white/5 hover:bg-white/10 hover:scale-105 border border-white/5' }}">
                <div class="{{ $this->selectedCategory === 'all' ? 'text-white' : 'text-rose-400 group-hover:text-rose-300' }} mb-1 lg:mb-2">
                    <x-heroicon-s-squares-2x2 class="w-6 h-6 lg:w-8 lg:h-8" />
                </div>
                <span class="text-[8px] lg:text-[10px] md:text-xs font-bold uppercase tracking-wider text-center leading-tight {{ $this->selectedCategory === 'all' ? 'text-white' : 'text-gray-400 group-hover:text-white' }}">
                    Semua
                </span>
            </button>
            
            {{-- ... existing categories loop ... --}}
            @foreach($this->getViewData()['categories'] as $category)
                <button wire:click="$set('selectedCategory', '{{ $category->slug }}')"
                    class="group flex flex-col items-center justify-center p-0 w-20 h-20 md:w-24 md:h-24 rounded-2xl transition-all duration-300 relative overflow-hidden shrink-0 shadow-sm
                    {{ $this->selectedCategory === $category->slug 
                        ? 'ring-2 ring-rose-500 scale-105' 
                        : 'border border-white/5 hover:scale-105 hover:border-white/20' }}">
                    
                    @if($category->image)
                        <img src="{{ Storage::url($category->image) }}" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-80 transition-opacity" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                    @else
                        <div class="absolute inset-0 bg-[#0f172a] group-hover:bg-[#1e293b] transition-colors"></div>
                        <div class="{{ $this->selectedCategory === $category->slug ? 'text-rose-500' : 'text-gray-400 group-hover:text-rose-400' }} mb-2 relative z-10 p-3 pb-0">
                             {{-- Placeholder Icons based on ID modulo for variety --}}
                            @if($category->id % 4 == 0) <x-heroicon-o-gift class="w-8 h-8" />
                            @elseif($category->id % 4 == 1) <x-heroicon-o-tag class="w-8 h-8" />
                            @elseif($category->id % 4 == 2) <x-heroicon-o-sparkles class="w-8 h-8" />
                            @else <x-heroicon-o-shopping-bag class="w-8 h-8" />
                            @endif
                        </div>
                    @endif

                    <span class="relative z-10 text-[10px] md:text-xs font-bold uppercase tracking-wider text-center leading-tight line-clamp-2 px-1
                        {{ $this->selectedCategory === $category->slug ? 'text-white' : 'text-gray-300 group-hover:text-white' }}">
                        {{ $category->name }}
                    </span>
                </button>
            @endforeach
        </div>

        {{-- ... existing code ... --}}

    {{-- NEW ORDERS MODAL --}}
    <div x-show="newOrdersModalOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;">
        
        <div class="bg-[#1e293b] w-full max-w-4xl h-[80vh] rounded-3xl shadow-2xl overflow-hidden border border-white/10 flex flex-col"
             @click.away="newOrdersModalOpen = false">
             
             {{-- Modal Header --}}
             <div class="bg-rose-600 px-6 py-4 flex items-center justify-between shrink-0">
                 <h3 class="text-xl font-bold text-white flex items-center gap-2">
                     <x-heroicon-o-bell class="w-6 h-6 text-white" />
                     Pesanan Baru Masuk
                     @if($newOrdersCount > 0)
                        <span class="bg-white text-rose-600 text-xs font-black px-2 py-0.5 rounded-full ml-2">{{ $newOrdersCount }}</span>
                     @endif
                 </h3>
                 <button @click="newOrdersModalOpen = false" class="text-white/80 hover:text-white">
                     <x-heroicon-o-x-mark class="w-6 h-6" />
                 </button>
             </div>

             {{-- Modal Body --}}
             <div class="p-0 overflow-y-auto custom-scrollbar flex-1 bg-[#0f172a] flex flex-col">
                 
                 {{-- Tabs --}}
                 <div class="flex border-b border-white/10 bg-[#1e293b] sticky top-0 z-10">
                     <button wire:click="$set('activeTab', 'new')" 
                         class="flex-1 py-4 text-sm font-bold uppercase tracking-wider transition-all relative {{ $activeTab === 'new' ? 'text-rose-500' : 'text-gray-400 hover:text-gray-200' }}">
                         Baru
                         @if($this->newOrdersCount > 0)
                            <span class="ml-2 bg-rose-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $this->newOrdersCount }}</span>
                         @endif
                         @if($activeTab === 'new') <div class="absolute bottom-0 left-0 w-full h-0.5 bg-rose-500"></div> @endif
                     </button>
                     <button wire:click="$set('activeTab', 'processing')" 
                         class="flex-1 py-4 text-sm font-bold uppercase tracking-wider transition-all relative {{ $activeTab === 'processing' ? 'text-amber-500' : 'text-gray-400 hover:text-gray-200' }}">
                         Diproses
                         @if($this->processingOrdersCount > 0)
                            <span class="ml-2 bg-amber-500 text-black text-[10px] px-1.5 py-0.5 rounded-full">{{ $this->processingOrdersCount }}</span>
                         @endif
                         @if($activeTab === 'processing') <div class="absolute bottom-0 left-0 w-full h-0.5 bg-amber-500"></div> @endif
                     </button>
                     <button wire:click="$set('activeTab', 'ready')" 
                         class="flex-1 py-4 text-sm font-bold uppercase tracking-wider transition-all relative {{ $activeTab === 'ready' ? 'text-emerald-500' : 'text-gray-400 hover:text-gray-200' }}">
                         Siap
                         @if($this->readyOrdersCount > 0)
                            <span class="ml-2 bg-emerald-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $this->readyOrdersCount }}</span>
                         @endif
                         @if($activeTab === 'ready') <div class="absolute bottom-0 left-0 w-full h-0.5 bg-emerald-500"></div> @endif
                     </button>
                 </div>

                 <div class="space-y-4 p-6 flex-1 overflow-y-auto custom-scrollbar">
                    @forelse($this->ordersByTab as $order)
                        <div class="bg-[#1e293b] rounded-2xl p-5 border border-white/5 flex flex-col lg:flex-row gap-6 relative overflow-hidden group hover:border-white/20 transition-all">
                            
                            {{-- Order Info --}}
                            <div class="flex-1 space-y-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-lg font-bold text-white flex items-center gap-2">
                                            #{{ $order->id }}
                                            <span class="text-xs font-normal text-gray-400 bg-white/5 px-2 py-0.5 rounded">{{ $order->created_at->diffForHumans() }}</span>
                                        </h4>
                                        <div class="text-sm text-gray-400 mt-1 flex flex-col">
                                            <span>{{ $order->customer_name }}</span>
                                            <span class="text-xs opacity-70">{{ $order->customer_phone ?? '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-black text-white">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</div>
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $order->payment_method_label }}</span>
                                            
                                            {{-- Payment Status Toggle --}}
                                            @if($order->payment_status === 'paid')
                                                <span class="text-[10px] font-bold bg-emerald-500/10 text-emerald-400 px-2 py-0.5 rounded border border-emerald-500/20">LUNAS</span>
                                            @else
                                                <button wire:click="processOrder({{ $order->id }}, 'mark_paid')" 
                                                    class="text-[10px] font-bold bg-red-500/10 text-red-400 px-2 py-0.5 rounded border border-red-500/20 hover:bg-red-500 hover:text-white transition-colors cursor-pointer animate-pulse">
                                                    BELUM LUNAS (Klik utk Lunas)
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Ordered Items Summary --}}
                                <div class="bg-black/20 rounded-xl p-3 text-sm text-gray-300">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach($order->items->take(3) as $item)
                                            <li>{{ $item->product ? $item->product->name : 'Produk Terhapus' }} <span class="text-gray-500">x{{ $item->quantity }}</span></li>
                                        @endforeach
                                        @if($order->items->count() > 3)
                                            <li class="list-none text-xs text-gray-500 italic">+ {{ $order->items->count() - 3 }} produk lainnya...</li>
                                        @endif
                                    </ul>
                                </div>
                                
                                {{-- Notes --}}
                                @if($order->notes)
                                <div class="text-xs text-amber-400/80 italic">
                                    "{{ Str::limit($order->notes, 100) }}"
                                </div>
                                @endif
                            </div>

                            {{-- Actions Based on Tab --}}
                            <div class="flex flex-row lg:flex-col gap-2 shrink-0 justify-center min-w-[170px]">
                                @if($activeTab === 'new')
                                    <button wire:click="processOrder({{ $order->id }}, 'process')"
                                        class="flex-1 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold py-2 px-4 rounded-xl shadow-lg shadow-emerald-600/20 transform active:scale-95 transition-all flex items-center justify-center gap-2 text-sm">
                                        <x-heroicon-o-arrow-path class="w-5 h-5" />
                                        Terima / Proses
                                    </button>
                                @elseif($activeTab === 'processing')
                                    <button wire:click="processOrder({{ $order->id }}, 'ready')"
                                        class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white font-bold py-2 px-4 rounded-xl shadow-lg shadow-amber-600/20 transform active:scale-95 transition-all flex items-center justify-center gap-2 text-sm">
                                        <x-heroicon-o-check-circle class="w-5 h-5" />
                                        Pesanan Siap
                                    </button>
                                @elseif($activeTab === 'ready')
                                    <button wire:click="processOrder({{ $order->id }}, 'complete')"
                                        class="flex-1 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 text-white font-bold py-2 px-4 rounded-xl shadow-lg shadow-sky-600/20 transform active:scale-95 transition-all flex items-center justify-center gap-2 text-sm">
                                        <x-heroicon-o-archive-box-arrow-down class="w-5 h-5" />
                                        Selesai / Diambil
                                    </button>
                                @endif

                                <button wire:click="processOrder({{ $order->id }}, 'print')"
                                    class="flex-1 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold py-2 px-4 rounded-xl transition-all flex items-center justify-center gap-2 text-sm">
                                    <x-heroicon-o-printer class="w-5 h-5" />
                                    Cetak Struk
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-64 text-center text-gray-500">
                            @if($activeTab === 'new')
                                <x-heroicon-o-check-badge class="w-20 h-20 mb-4 opacity-30 text-emerald-500" />
                                <p class="text-xl font-bold">Semua Aman!</p>
                                <p class="text-sm opacity-60">Tidak ada pesanan baru yang menunggu.</p>
                            @elseif($activeTab === 'processing')
                                <x-heroicon-o-clock class="w-20 h-20 mb-4 opacity-30 text-amber-500" />
                                <p class="text-xl font-bold">Dapur Sepi</p>
                                <p class="text-sm opacity-60">Tidak ada pesanan yang sedang diproses.</p>
                            @else
                                <x-heroicon-o-shopping-bag class="w-20 h-20 mb-4 opacity-30 text-sky-500" />
                                <p class="text-xl font-bold">Siap Saji Kosong</p>
                                <p class="text-sm opacity-60">Belum ada pesanan yang siap diambil.</p>
                            @endif
                        </div>
                    @endforelse
                 </div>
             </div>
        </div>
    </div>
    
    {{-- Script --}}
    <script>
        // ... (existing scripts) ...
        
    </script>
        <div class="flex-1 h-full flex flex-col relative bg-[#0f172a] overflow-hidden">
             {{-- Top Bar: Search & Status --}}
             <div class="h-16 lg:h-20 shrink-0 px-4 lg:px-6 flex items-center justify-between bg-[#0f172a]/80 backdrop-blur-md sticky top-0 z-30 border-b border-white/5">
                <div class="flex items-center gap-4 w-full max-w-2xl">
                    <div class="relative w-full group">
                        <div class="absolute inset-y-0 left-0 pl-3 lg:pl-4 flex items-center pointer-events-none">
                            <x-heroicon-m-magnifying-glass class="h-5 w-5 lg:h-6 lg:w-6 text-gray-500 group-focus-within:text-rose-500 transition-colors" />
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="search" id="search-input"
                            class="block w-full pl-10 lg:pl-12 pr-10 py-2.5 lg:py-3.5 bg-[#1e293b] border-transparent focus:border-rose-500 focus:ring-rose-500 rounded-xl lg:rounded-2xl text-sm lg:text-base text-white placeholder-gray-500 shadow-inner transition-all duration-300"
                            placeholder="Cari produk (Tekan 'F')...">
                        
                        @if($search)
                            <button wire:click="$set('search', '')" class="absolute inset-y-0 right-10 flex items-center pr-2 text-gray-500 hover:text-white transition-colors">
                                <x-heroicon-s-x-circle class="w-5 h-5" />
                            </button>
                        @endif

                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-[10px] lg:text-xs font-bold text-gray-500 bg-[#334155] px-1.5 lg:px-2 py-0.5 lg:py-1 rounded">F</span>
                        </div>
                    </div>
                </div>
                
                {{-- Date/Time Display (Optional) --}}
                <div class="hidden xl:flex flex-col items-end text-right">
                    <span class="text-xl font-bold text-white tracking-tight" x-data x-text="new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})"></span>
                    <span class="text-xs text-gray-400 uppercase font-medium tracking-wider" x-data x-text="new Date().toLocaleDateString('id-ID', {weekday: 'long', day: 'numeric', month: 'long'})"></span>
                </div>
             </div>

             {{-- Grid Content --}}
             <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                {{-- Loading --}}
                <div wire:loading.flex wire:target="search, selectedCategory" class="absolute inset-0 bg-[#0f172a]/60 backdrop-blur-sm z-40 items-center justify-center">
                    <div class="flex flex-col items-center animate-pulse">
                        <div class="w-16 h-16 border-4 border-rose-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                        <span class="text-rose-400 font-bold tracking-widest uppercase">Memuat...</span>
                    </div>
                </div>

                 <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 lg:gap-6 pb-24">
                    @forelse($this->getViewData()['products'] as $product)
                        <div wire:click="addToCart({{ $product->id }})"
                            class="group relative bg-[#1e293b] rounded-3xl overflow-hidden shadow-lg border border-white/5 hover:border-rose-500/50 hover:shadow-rose-500/20 active:scale-95 transition-all duration-300 cursor-pointer h-full flex flex-col animate-scaleIn">
                            
                            {{-- Image Area --}}
                            <div class="aspect-[4/3] w-full bg-[#0f172a] relative overflow-hidden">
                                @if($product->images && count($product->images) > 0)
                                    <img src="{{ asset('storage/' . $product->images[0]) }}" 
                                        class="w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-110 transition-transform duration-700 ease-out"
                                        loading="lazy">
                                @else
                                    <div class="flex items-center justify-center h-full text-gray-700">
                                        <x-heroicon-o-photo class="w-16 h-16 opacity-20" />
                                    </div>
                                @endif
                                
                                {{-- Stock Floating Badge --}}
                                <div class="absolute top-3 left-3">
                                    <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider rounded-lg text-white shadow-lg backdrop-blur-md
                                        {{ $product->stock > 10 ? 'bg-emerald-500/80' : ($product->stock > 0 ? 'bg-amber-500/80' : 'bg-red-500/80') }}">
                                        {{ $product->stock }} Item
                                    </span>
                                </div>
                                
                                {{-- Price Tag Overlay --}}
                                <div class="absolute bottom-0 right-0 bg-[#0f172a]/90 backdrop-blur px-4 py-2 rounded-tl-2xl border-t border-l border-white/10">
                                    <span class="text-rose-400 font-black text-lg">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Footer Info --}}
                            <div class="p-4 flex-1 flex flex-col justify-center">
                                <h3 class="font-bold text-gray-200 text-sm md:text-base leading-snug line-clamp-2 group-hover:text-white transition-colors text-center">
                                    {{ $product->name }}
                                </h3>
                            </div>
                        </div>
                    @empty
                         <div class="col-span-full flex flex-col items-center justify-center h-80 text-gray-500">
                            <x-heroicon-o-face-frown class="w-20 h-20 mb-4 opacity-30" />
                            <p class="text-xl font-bold">Tidak ada produk ditemukan</p>
                            <p class="text-sm opacity-60">Coba kata kunci lain</p>
                        </div>
                    @endforelse

                     <div class="col-span-full py-8 flex justify-center">
                        <div x-intersect="$wire.loadMoreProducts()" class="flex flex-col items-center gap-2">
                             <div wire:loading wire:target="loadMoreProducts">
                                <svg class="animate-spin h-8 w-8 text-rose-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                             </div>
                             @if($this->getViewData()['products']->hasMorePages())
                                 <span wire:loading wire:target="loadMoreProducts" class="text-gray-400 text-sm font-medium animate-pulse">Memuat produk lainnya...</span>
                             @else
                                 <span class="text-gray-500 text-sm">Semua produk telah ditampilkan.</span>
                             @endif
                        </div>
                    </div>
                </div>
             </div>
        </div>

        {{-- COLUMN 3: CART PANEL (Fixed Right / Stacked Bottom) --}}
        <div class="w-full lg:w-96 2xl:w-[450px] bg-[#1e293b] h-auto lg:h-full shadow-2xl border-t lg:border-t-0 lg:border-l border-white/10 flex flex-col z-30 shrink-0" 
            x-data="{ open: false }">
            
            {{-- Cart Header (Always Visible) --}}
            <div class="h-14 lg:h-20 shrink-0 px-4 lg:px-6 bg-[#0f172a]/50 flex items-center justify-between border-b border-white/5 cursor-pointer lg:cursor-default"
                @click="open = !open">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 lg:w-10 lg:h-10 rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center shadow-lg shadow-rose-500/20">
                        <x-heroicon-m-shopping-bag class="w-4 h-4 lg:w-6 lg:h-6 text-white" />
                    </div>
                    <div>
                        <h2 class="font-bold text-base lg:text-lg text-white leading-none">Keranjang</h2>
                        <span class="text-[10px] lg:text-xs text-rose-400 font-medium">{{ count($cart) }} Item dipilih</span>
                    </div>
                </div>
            </div>

                {{-- Cart Content Wrapper (Overlay on Mobile, Vertical Flex on Desktop) --}}
            <div class="flex-col lg:flex flex-1 overflow-hidden"
                 :class="{ 'fixed inset-0 z-50 bg-[#1e293b] flex pt-0': open, 'hidden lg:flex': !open }">
                
                {{-- Mobile Overlay Header --}}
                <div class="lg:hidden h-16 shrink-0 flex items-center justify-between px-4 border-b border-white/5 bg-[#0f172a]">
                    <span class="font-bold text-lg">Detail Pesanan</span>
                    <button @click="open = false" class="p-2 text-gray-400 hover:text-white bg-white/5 rounded-lg">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>

                {{-- Cart List --}}
                <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                    @forelse($cart as $id => $item)
                        <div class="group relative bg-[#0f172a] rounded-2xl p-3 border border-white/5 flex gap-4 transition-all hover:border-white/10">
                            {{-- Image Thumb --}}
                            <div class="w-16 h-16 rounded-xl bg-[#1e293b] overflow-hidden shrink-0">
                                @if(isset($item['image']))
                                    <img src="{{ asset('storage/' . $item['image']) }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100">
                                @else
                                    <div class="w-full h-full flex items-center justify-center"><x-heroicon-o-photo class="w-6 h-6 text-gray-700"/></div>
                                @endif
                            </div>
                            
                            {{-- Info --}}
                            <div class="flex-1 min-w-0 flex flex-col justify-between py-0.5">
                                <div class="flex justify-between items-start">
                                    <h4 class="text-sm font-bold text-gray-200 truncate pr-2">{{ $item['name'] }}</h4>
                                    <span class="text-sm font-bold text-white">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-end">
                                    <span class="text-xs text-gray-500">Rp {{ number_format($item['price'], 0, ',', '.') }} / unit</span>
                                    
                                    {{-- Qty Control --}}
                                    <div class="flex items-center bg-[#1e293b] rounded-lg border border-white/5">
                                        <button wire:click="updateQty({{ $id }}, {{ $item['qty'] - 1 }})" class="p-1 hover:text-rose-400 transition-colors"><x-heroicon-s-minus class="w-4 h-4"/></button>
                                        <span class="text-xs font-bold w-6 text-center text-white">{{ $item['qty'] }}</span>
                                        <button wire:click="updateQty({{ $id }}, {{ $item['qty'] + 1 }})" class="p-1 hover:text-emerald-400 transition-colors"><x-heroicon-s-plus class="w-4 h-4"/></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-64 text-center mt-12 opacity-40">
                            <x-heroicon-o-shopping-cart class="w-24 h-24 text-gray-600 mb-6" />
                            <h3 class="text-xl font-bold text-gray-400">Keranjang Kosong</h3>
                            <p class="text-sm text-gray-600 mt-2">Pilih produk di menu sebelah kiri</p>
                        </div>
                    @endforelse
                </div>

                {{-- Voucher & Footer & Checkout Trigger --}}
                <div class="bg-[#0f172a] border-t border-white/10 shrink-0">
                    
                    {{-- Voucher Input --}}
                    <div class="px-4 py-3 border-b border-white/5 bg-[#1e293b]/50">
                         <div class="flex gap-2">
                             <input wire:model="voucherCode" type="text" placeholder="Kode Voucher" 
                                class="w-full bg-[#0f172a] border border-white/10 rounded-lg px-3 py-2 text-sm text-white focus:border-rose-500 focus:ring-0 placeholder-gray-600">
                             <button wire:click="applyVoucher" wire:loading.attr="disabled"
                                class="bg-white/10 hover:bg-white/20 text-white px-3 py-2 rounded-lg text-xs font-bold transition-colors">
                                 Gunakan
                             </button>
                         </div>
                         @if($discount > 0)
                            <div class="flex justify-between items-center mt-2 text-emerald-400 text-xs font-bold">
                                <span>Diskon Voucher</span>
                                <div class="flex items-center gap-2">
                                    <span>- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                                    <button wire:click="removeVoucher" class="text-red-400 hover:text-red-300">
                                        <x-heroicon-s-x-mark class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                         @endif
                    </div>

                    <div class="p-4 lg:p-6">
                        <div class="space-y-3 mb-6 font-mono text-sm">
                            <div class="flex justify-between text-gray-500">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($total + $discount, 0, ',', '.') }}</span>
                            </div>
                            @if($discount > 0)
                            <div class="flex justify-between text-emerald-500">
                                <span>Diskon</span>
                                <span>- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-white text-2xl font-bold border-t border-white/10 pt-4">
                                <span>Total</span>
                                <span class="text-rose-500">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <button @click="paymentModalOpen = true" 
                            class="w-full bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-500 hover:to-pink-500 text-white font-black py-4 rounded-2xl shadow-xl shadow-rose-600/20 transform active:scale-95 transition-all text-lg flex items-center justify-center gap-3 disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed"
                            {{ empty($cart) ? 'disabled' : '' }}>
                            <x-heroicon-s-banknotes class="w-6 h-6" />
                            <span>BAYAR</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PAYMENT MODAL --}}
    <div x-show="paymentModalOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;">
        
        <div class="bg-[#1e293b] w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden border border-white/10"
             @click.away="paymentModalOpen = false">
             
             {{-- Modal Header --}}
             <div class="bg-[#0f172a] px-6 py-4 border-b border-white/10 flex items-center justify-between">
                 <h3 class="text-xl font-bold text-white">Pembayaran</h3>
                 <button @click="paymentModalOpen = false" class="text-gray-400 hover:text-white">
                     <x-heroicon-o-x-mark class="w-6 h-6" />
                 </button>
             </div>

             {{-- Modal Body --}}
             <div class="p-5 space-y-4">
                 {{-- Total Amount Large Display --}}
                 <div class="text-center">
                     <span class="text-gray-400 text-xs uppercase tracking-wider">Total Tagihan</span>
                     <div class="text-3xl font-black text-white">Rp {{ number_format($total, 0, ',', '.') }}</div>
                 </div>

                 {{-- Payment Method Selection --}}
                 <div>
                     <label class="block text-xs font-bold text-gray-400 mb-1.5">Metode Pembayaran</label>
                     <div class="grid grid-cols-2 gap-2">
                         @foreach(['cash' => 'Tunai', 'qris' => 'QRIS', 'transfer' => 'Transfer', 'debit' => 'Debit'] as $key => $label)
                             <button type="button" x-on:click="paymentMethod = '{{ $key }}'"
                                 @if($key !== 'cash') x-on:click.debounce.50ms="$wire.set('receivedAmount', {{ $total }})" @else x-on:click.debounce.50ms="$wire.set('receivedAmount', 0)" @endif
                                 class="py-2 rounded-lg border font-bold text-xs transition-all"
                                 :class="paymentMethod === '{{ $key }}'
                                     ? 'bg-rose-500 border-rose-500 text-white shadow-md shadow-rose-500/20' 
                                     : 'bg-[#0f172a] border-white/10 text-gray-400 hover:text-white hover:bg-white/5'">
                                 {{ $label }}
                             </button>
                         @endforeach
                     </div>
                 </div>

                 {{-- Payment Input --}}
                 <div>
                     <label class="block text-xs font-bold text-gray-400 mb-1.5">Uang Diterima</label>
                     <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">Rp</span>
                        <input type="number" wire:model.live="receivedAmount" id="payment-input-modal"
                            class="w-full bg-[#0f172a] border-2 border-white/10 rounded-xl py-2 pl-10 pr-4 text-lg font-bold text-white focus:border-rose-500 focus:ring-0 placeholder-gray-600 appearance-none"
                            placeholder="0" autofocus>
                    </div>
                 </div>

                 {{-- Quick Amount Buttons --}}
                 <div class="grid grid-cols-3 gap-2">
                    @foreach([20000, 50000, 100000] as $amount)
                        <button x-on:click="$wire.set('receivedAmount', {{ $amount }})" 
                            class="bg-[#0f172a] hover:bg-white/10 border border-white/5 rounded-lg py-1.5 text-xs font-bold text-gray-300 transition-all">
                            {{ number_format($amount/1000, 0) }}k
                        </button>
                    @endforeach
                 </div>
                 <button x-on:click="$wire.set('receivedAmount', {{ $total }})" 
                    class="w-full bg-[#0f172a] hover:bg-white/10 border border-white/5 dashed border-rose-500/30 rounded-lg py-1.5 text-xs font-bold text-rose-400 transition-all">
                    Uang Pas
                </button>

                 {{-- Change Display --}}
                 <div class="flex justify-between items-center bg-[#0f172a] p-3 rounded-xl border {{ $change < 0 ? 'border-red-500/30' : 'border-emerald-500/30' }}">
                    <span class="text-xs font-bold {{ $change < 0 ? 'text-red-400' : 'text-emerald-400' }}">
                        {{ $change < 0 ? 'Kurang Bayar' : 'Kembalian' }}
                    </span>
                    <span class="text-xl font-black {{ $change < 0 ? 'text-red-500' : 'text-emerald-500' }}">
                        Rp {{ number_format(abs($change), 0, ',', '.') }}
                    </span>
                </div>
             </div>

             {{-- Modal Footer --}}
             <div class="p-5 pt-0">
                <button wire:click="checkout" wire:loading.attr="disabled"
                    class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black py-3 rounded-2xl shadow-lg shadow-emerald-600/20 transform active:scale-95 transition-all text-base flex items-center justify-center gap-2 disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed"
                    {{ empty($cart) || ($receivedAmount < $total) ? 'disabled' : '' }}>
                    <span wire:loading.remove>PROSES TRANSAKSI</span>
                    <span wire:loading>MEMPROSES...</span>
                </button>
             </div>
        </div>
    </div>


    {{-- SUCCESS MODAL --}}
    <div x-show="successModalOpen" 
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/90 backdrop-blur-md"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        style="display: none;">
        
        <div class="bg-[#1e293b] w-full max-w-md rounded-3xl shadow-2xl overflow-hidden border border-emerald-500/20 text-center p-8">
            <div class="w-20 h-20 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <x-heroicon-s-check class="w-10 h-10 text-emerald-500" />
            </div>
            
            <h2 class="text-3xl font-black text-white mb-2">Transaksi Berhasil!</h2>
            <p class="text-gray-400 mb-8">Pembayaran telah diterima.</p>

            <div class="bg-[#0f172a] rounded-2xl p-6 mb-8 border border-white/5">
                <span class="text-gray-400 text-sm uppercase tracking-wider block mb-2">Uang Kembalian</span>
                <span class="text-4xl font-black text-emerald-400 block">
                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(changeAmount)"></span>
                </span>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <button @click="$dispatch('print-receipt', { orderId: orderId })" 
                    class="w-full bg-white/10 hover:bg-white/20 text-white font-bold py-3 rounded-xl border border-white/10 transition-all flex items-center justify-center gap-2">
                    <x-heroicon-o-printer class="w-5 h-5" />
                    Cetak Struk
                </button>
                
                <button wire:click="resetCart" @click="successModalOpen = false; paymentModalOpen = false;"
                    class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center gap-2">
                    <x-heroicon-o-plus-circle class="w-5 h-5" />
                    Pesanan Baru
                </button>
            </div>
        </div>
    </div>


    {{-- HISTORY MODAL --}}
    <div x-show="historyModalOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;">
        
        <div class="bg-[#1e293b] w-full max-w-4xl h-[80vh] rounded-3xl shadow-2xl overflow-hidden border border-white/10 flex flex-col"
             @click.away="historyModalOpen = false">
             
             {{-- Modal Header --}}
             <div class="bg-[#0f172a] px-6 py-4 border-b border-white/10 flex items-center justify-between shrink-0">
                 <h3 class="text-xl font-bold text-white flex items-center gap-2">
                     <x-heroicon-o-clock class="w-6 h-6 text-sky-500" />
                     Riwayat Transaksi Terakhir
                 </h3>
                 <button @click="historyModalOpen = false" class="text-gray-400 hover:text-white">
                     <x-heroicon-o-x-mark class="w-6 h-6" />
                 </button>
             </div>

             {{-- Modal Body --}}
             <div class="p-0 overflow-y-auto custom-scrollbar flex-1">
                 <table class="w-full text-left border-collapse">
                     <thead class="bg-[#0f172a] text-gray-400 text-xs uppercase sticky top-0 z-10">
                         <tr>
                             <th class="px-6 py-4 font-bold">No. Pesanan</th>
                             <th class="px-6 py-4 font-bold">Waktu</th>
                             <th class="px-6 py-4 font-bold">Total</th>
                             <th class="px-6 py-4 font-bold">Metode</th>
                             <th class="px-6 py-4 font-bold">Status</th>
                             <th class="px-6 py-4 font-bold text-right">Aksi</th>
                         </tr>
                     </thead>
                     <tbody class="divide-y divide-white/5 text-sm text-gray-300">
                         @forelse($this->recentOrders as $order)
                             <tr class="hover:bg-white/5 transition-colors">
                                 <td class="px-6 py-4 font-mono text-sky-400">#{{ $order->id }}</td>
                                 <td class="px-6 py-4">{{ $order->created_at->locale('id')->isoFormat('D MMMM Y, HH:mm') }}</td>
                                 <td class="px-6 py-4 font-bold text-white">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                                 <td class="px-6 py-4">
                                     @php
                                        $pmLabels = [
                                            'cash' => 'Tunai', 
                                            'qris' => 'QRIS', 
                                            'transfer' => 'Transfer', 
                                            'debit' => 'Debit'
                                        ];
                                        $pmColors = [
                                            'cash' => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
                                            'qris' => 'text-sky-400 bg-sky-500/10 border-sky-500/20',
                                            'transfer' => 'text-violet-400 bg-violet-500/10 border-violet-500/20',
                                            'debit' => 'text-amber-400 bg-amber-500/10 border-amber-500/20',
                                        ];
                                        $pmKey = $order->payment_method ?? 'cash';
                                     @endphp
                                     <span class="px-2 py-1 rounded text-xs font-bold border {{ $pmColors[$pmKey] ?? 'text-gray-400 border-gray-500/20' }}">
                                         {{ $pmLabels[$pmKey] ?? ucfirst($pmKey) }}
                                     </span>
                                 </td>
                                 <td class="px-6 py-4">
                                     <span class="px-2 py-1 rounded text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/20">
                                         {{ $order->status }}
                                     </span>
                                 </td>
                                 <td class="px-6 py-4 text-right">
                                     <button @click="$dispatch('print-receipt', { orderId: {{ $order->id }} })"
                                         class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg transition-all text-xs font-bold text-white">
                                         <x-heroicon-o-printer class="w-4 h-4" />
                                         Cetak
                                     </button>
                                 </td>
                             </tr>
                         @empty
                             <tr>
                                 <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                     Belum ada riwayat transaksi hari ini.
                                 </td>
                             </tr>
                         @endforelse
                         
                         @if($this->recentOrders->hasMorePages())
                             <tr>
                                 <td colspan="6" class="px-6 py-4 text-center">
                                     <div x-intersect="$wire.loadMoreRecentOrders()" class="flex justify-center items-center gap-2 text-rose-400 font-bold text-xs uppercase tracking-wider animate-pulse">
                                         <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                         Memuat data lainnya...
                                     </div>
                                 </td>
                             </tr>
                         @endif
                     </tbody>
                 </table>
             </div>
        </div>
    </div>

    {{-- ORDER DETAIL MODAL --}}
    <div x-show="orderDetailModalOpen" 
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;">
        
        <div class="bg-[#1e293b] w-full max-w-2xl h-[85vh] rounded-3xl shadow-2xl overflow-hidden border border-white/10 flex flex-col"
             @click.away="orderDetailModalOpen = false">
             
             {{-- Modal Header --}}
             <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between shrink-0">
                 <h3 class="text-xl font-bold text-white flex items-center gap-2">
                     <x-heroicon-o-document-text class="w-6 h-6 text-white" />
                     Detail Pesanan #{{ $this->selectedOrder?->id }}
                 </h3>
                 <button @click="orderDetailModalOpen = false" class="text-white/80 hover:text-white">
                     <x-heroicon-o-x-mark class="w-6 h-6" />
                 </button>
             </div>

             {{-- Modal Body --}}
             <div class="p-6 overflow-y-auto custom-scrollbar flex-1 bg-[#0f172a] space-y-6">
                 @if($this->selectedOrder)
                    {{-- Customer Info --}}
                    <div class="bg-[#1e293b] rounded-xl p-4 border border-white/5 space-y-2">
                        <h4 class="text-indigo-400 font-bold uppercase tracking-wider text-xs mb-2">Informasi Pelanggan</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-400 block text-xs">Nama</span>
                                <span class="text-white font-semibold">{{ $this->selectedOrder->customer_name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-xs">Telepon</span>
                                <span class="text-white font-semibold">{{ $this->selectedOrder->customer_phone ?? '-' }}</span>
                            </div>
                            <div class="col-span-2 flex justify-between items-center">
                                <div>
                                    <span class="text-gray-400 block text-xs">Alamat / Catatan</span>
                                    <span class="text-white">{{ $this->selectedOrder->notes ?? '-' }}</span> 
                                </div>
                                <div class="text-right">
                                    <span class="text-gray-400 block text-xs mb-1">Status Pembayaran</span>
                                    @if($this->selectedOrder->payment_status === 'paid')
                                        <span class="text-xs font-bold bg-emerald-500/10 text-emerald-400 px-2 py-1 rounded border border-emerald-500/20">LUNAS</span>
                                    @else
                                        <button wire:click="processOrder({{ $this->selectedOrder->id }}, 'mark_paid')" 
                                            class="text-xs font-bold bg-red-500/10 text-red-400 px-2 py-1 rounded border border-red-500/20 hover:bg-red-500 hover:text-white transition-colors cursor-pointer animate-pulse">
                                            BELUM LUNAS (Klik utk Lunas)
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Order Items --}}
                    <div class="bg-[#1e293b] rounded-xl overflow-hidden border border-white/5">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-black/20 text-gray-400 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-3">Produk</th>
                                    <th class="px-4 py-3 text-center">Qty</th>
                                    <th class="px-4 py-3 text-right">Harga</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach($this->selectedOrder->items as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-white">
                                            <div class="font-medium">{{ $item->product->name }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center text-white">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-right text-gray-300">Rp {{ number_format($item->unit_amount, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right text-white font-bold">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-black/20 font-bold text-white">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right text-gray-400">Total Belanja</td>
                                    <td class="px-4 py-3 text-right text-lg">Rp {{ number_format($this->selectedOrder->grand_total ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center text-gray-500 py-10">Memuat data...</div>
                @endif
             </div>

             {{-- Modal Footer Actions --}}
             <div class="p-4 bg-[#1e293b] border-t border-white/5 flex gap-3">
                  @if($this->selectedOrder)
                     @if($this->selectedOrder->status === 'new')
                        <button wire:click="processOrder({{ $this->selectedOrder->id }}, 'process')"
                            wire:loading.attr="disabled"
                            class="flex-1 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 disabled:opacity-50 disabled:cursor-wait text-white font-bold py-3 px-4 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                            <x-heroicon-o-arrow-path class="w-5 h-5" wire:loading.remove wire:target="processOrder" />
                            <span wire:loading.remove wire:target="processOrder">Terima / Proses</span>
                            <span wire:loading wire:target="processOrder">Memproses...</span>
                        </button>
                     @elseif($this->selectedOrder->status === 'processing')
                        <button wire:click="processOrder({{ $this->selectedOrder->id }}, 'ready')"
                            wire:loading.attr="disabled"
                            class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 disabled:opacity-50 disabled:cursor-wait text-white font-bold py-3 px-4 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                            <x-heroicon-o-check-circle class="w-5 h-5" wire:loading.remove wire:target="processOrder" />
                            <span wire:loading.remove wire:target="processOrder">Pesanan Siap</span>
                            <span wire:loading wire:target="processOrder">Memproses...</span>
                        </button>
                     @elseif($this->selectedOrder->status === 'ready')
                        <button wire:click="processOrder({{ $this->selectedOrder->id }}, 'complete')"
                            wire:loading.attr="disabled"
                            class="flex-1 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-400 hover:to-blue-500 disabled:opacity-50 disabled:cursor-wait text-white font-bold py-3 px-4 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                            <x-heroicon-o-archive-box-arrow-down class="w-5 h-5" wire:loading.remove wire:target="processOrder" />
                            <span wire:loading.remove wire:target="processOrder">Selesai / Diambil</span>
                            <span wire:loading wire:target="processOrder">Memproses...</span>
                        </button>
                     @endif
                    
                    <button wire:click="processOrder({{ $this->selectedOrder->id }}, 'print')"
                        wire:loading.attr="disabled"
                        class="w-full sm:w-auto bg-white/5 hover:bg-white/10 border border-white/10 disabled:opacity-50 disabled:cursor-wait text-white font-bold py-3 px-6 rounded-xl transition-all flex items-center justify-center gap-2">
                        <x-heroicon-o-printer class="w-5 h-5" wire:loading.remove wire:target="processOrder({{ $this->selectedOrder->id }}, 'print')" />
                         <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" wire:loading wire:target="processOrder({{ $this->selectedOrder->id }}, 'print')">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="processOrder({{ $this->selectedOrder->id }}, 'print')">Cetak Struk</span>
                        <span wire:loading wire:target="processOrder({{ $this->selectedOrder->id }}, 'print')">Mencetak...</span>
                    </button>
                 @endif
             </div>
        </div>
    </div>

    {{-- Script --}}
    <script>
        // Global Error Handler for Debugging
        window.onerror = function(msg, url, line, col, error) {
            console.error("System Error: " + msg + "\nIn: " + url + ":" + line);
            return false;
        };

        document.addEventListener('livewire:initialized', () => {
            // Hotkeys
            document.addEventListener('keydown', (e) => {
                if(e.key === 'F' || e.key === 'f') {
                    if(document.activeElement.tagName !== 'INPUT') {
                        e.preventDefault();
                        document.getElementById('search-input').focus();
                    }
                }
                if(e.key === 'Enter') {
                    if(@this.cart.length > 0 && @this.receivedAmount >= @this.total) {
                        // Enter key behavior depends on modal state
                        if(this.paymentModalOpen) {
                             @this.checkout();
                        } else {
                            this.paymentModalOpen = true;
                        }
                    }
                }
            });

            // Sounds & Events
            @this.on('play-sound', (event) => {
                 const status = event.status || (event[0] ? event[0].status : null);
                 if(status === 'success') {
                     const beep = document.getElementById('beep-sound');
                     beep.currentTime = 0; beep.play().catch(e=>{});
                 } else if (status === 'checkout') {
                     const cash = document.getElementById('cash-sound');
                     cash.currentTime = 0; cash.play().catch(e=>{});
                 }
            });

            // Print
            @this.on('print-receipt', (event) => {
                const id = Array.isArray(event) ? event[0].orderId : event.orderId;
                if(id) {
                     // Update alpine data for the success modal print button
                    // document.querySelector('[x-data]').__x.$data.orderId = id; // Handled by event now

                    const url = `/admin/pos/print/${id}`;
                    const win = window.open(url, 'Print Invoice', 'height=600,width=400');
                    if(win) {
                        win.focus();
                    } else {
                        alert('Pop-up terblokir! Mohon izinkan pop-up untuk mencetak struk.');
                    }
                }
            });
        });
    </script>
    
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-scaleIn { animation: scaleIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</div>
