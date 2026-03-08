<div class="h-screen w-screen overflow-hidden bg-[#0f172a]" 
    wire:poll.keep-alive.10s="checkNewOrders" 
    x-data="{ 
        paymentModalOpen: @entangle('paymentModalOpen'), 
        successModalOpen: @entangle('successModalOpen'), 
        historyModalOpen: @entangle('historyModalOpen'),
        newOrdersModalOpen: @entangle('newOrdersModalOpen'),
        orderDetailModalOpen: @entangle('orderDetailModalOpen'),
        loginModalOpen: @entangle('loginModalOpen'),
        isShiftOpen: @entangle('isShiftOpen'),
        isLocked: @entangle('isLocked'),
        showSettlementModal: @entangle('showSettlementModal'),
        showVoucherCatalog: @entangle('showVoucherCatalog'),
        selectedCategory: @entangle('selectedCategory'),
        orderId: null,
        receivedAmount: 0,
        changeAmount: 0,
        paymentMethod: @entangle('paymentMethod'),
        localPin: ''
    }"
    x-on:order-completed.window="paymentModalOpen = false; successModalOpen = true; const d = Array.isArray($event.detail) ? $event.detail[0] : $event.detail; orderId = d.orderId; receivedAmount = d.receivedAmount; changeAmount = d.changeAmount"
    x-on:shift-opened.window="loginModalOpen = false; localPin = '';"
    x-on:open-new-orders-modal.window="newOrdersModalOpen = true"
    x-on:trigger-order-detail.window="const d = Array.isArray($event.detail) ? $event.detail[0] : $event.detail; $wire.loadOrderDetail(d.orderId)" 
    x-on:open-order-detail-modal.window="orderDetailModalOpen = true"
    x-on:keydown.window="
        // 1. Check if Login/Lock Modal is active
        const isLoginActive = loginModalOpen || !isShiftOpen || isLocked;
        
        if (isLoginActive) {
            // Handle numeric input for PIN only if not in an input field
            if ($event.target.tagName !== 'INPUT') {
                if ($event.key >= '0' && $event.key <= '9') {
                    if (localPin.length < 6) localPin += $event.key;
                }
                if ($event.key === 'Backspace') {
                    localPin = '';
                }
            }
            
            if ($event.key === 'Enter') {
                const submitBtn = document.getElementById('btn-aktifkan');
                if (submitBtn) submitBtn.click();
            }
            return; // STOP processing other shortcuts if login is active
        }

        // 2. Prevent shortcuts from triggering if user is pressing Modifiers (Ctrl, Alt, Meta)
        // This ensures browser shortcuts like Ctrl+F, Ctrl+C, etc. still work
        if ($event.ctrlKey || $event.altKey || $event.metaKey) {
            return;
        }

        if ($event.key.toUpperCase() === 'F') {
            if ($event.target.tagName !== 'INPUT' && $event.target.tagName !== 'TEXTAREA') {
                $event.preventDefault();
                document.getElementById('search-input').focus();
            }
        }
        
        if ($event.target.tagName !== 'INPUT' && $event.target.tagName !== 'TEXTAREA') {
            // Category shortcuts (1-9)
            if ($event.key >= '1' && $event.key <= '9') {
                const categories = @js($this->categories->pluck('slug'));
                if (categories[$event.key - 1]) selectedCategory = categories[$event.key - 1];
            }
            if ($event.key === '0') selectedCategory = 'all';

            // Cart shortcuts
            if ($event.key.toUpperCase() === 'C') {
                if (confirm('Kosongkan keranjang?')) $wire.clearCart();
            }

            // Payment shortcut
            if ($event.key === 'Enter') {
                if (paymentModalOpen) {
                    const bayarBtn = document.querySelector('button[wire\\:click=\'checkout\']');
                    if (bayarBtn && !bayarBtn.disabled) bayarBtn.click();
                } else if (!loginModalOpen && isShiftOpen && $wire.total > 0) {
                    paymentModalOpen = true;
                }
            }
        }

        if ($event.key === 'Escape') {
            showSettlementModal = $wire.set('showSettlementModal', false);
            showVoucherCatalog = $wire.set('showVoucherCatalog', false);
            orderDetailModalOpen = false;
            historyModalOpen = false;
            newOrdersModalOpen = false;
            paymentModalOpen = false;
            loginModalOpen = false;
        }
    ">
    
    {{-- Audio Effects --}}
    <audio id="beep-sound" src="{{ asset('sounds/beep.mp3') }}" preload="auto"></audio>
    <audio id="cash-sound" src="{{ asset('sounds/cash.mp3') }}" preload="auto"></audio>

    {{-- Main POS Layout --}}
    <div class="fixed inset-0 bg-[#0f172a] text-white overflow-hidden z-0 flex flex-col lg:flex-row font-sans antialiased">
        
        {{-- COLUMN 1: SIDEBAR (No Logout here) --}}
        <div class="w-full lg:w-32 xl:w-40 h-20 lg:h-full flex flex-row lg:flex-col items-center bg-[#1e293b]/50 backdrop-blur-xl border-b lg:border-b-0 lg:border-r border-white/5 py-2 lg:py-6 px-4 lg:px-0 space-x-4 lg:space-x-0 lg:space-y-4 overflow-x-auto lg:overflow-y-auto no-scrollbar z-20 shrink-0">
            
            <a href="/admin" class="group flex flex-col items-center justify-center p-2 lg:p-3 w-16 h-16 lg:w-24 lg:h-24 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/5 transition-all shrink-0">
                <x-heroicon-o-arrow-left-on-rectangle class="w-6 h-6 lg:w-8 lg:h-8 text-gray-400 group-hover:text-white" />
                <span class="text-[8px] lg:text-[10px] font-bold uppercase tracking-wider text-gray-400 group-hover:text-white text-center mt-1">Keluar</span>
            </a>

            <button type="button" @click="historyModalOpen = true" class="group flex flex-col items-center justify-center p-2 lg:p-3 w-16 h-16 lg:w-24 lg:h-24 rounded-2xl bg-white/5 hover:bg-sky-500 transition-all shrink-0">
                <x-heroicon-o-clock class="w-6 h-6 lg:w-8 lg:h-8 text-gray-400 group-hover:text-white" />
                <span class="text-[8px] lg:text-[10px] font-bold uppercase tracking-wider text-gray-400 group-hover:text-white text-center mt-1">Riwayat</span>
            </button>

            <button type="button" @click="newOrdersModalOpen = true" class="group flex flex-col items-center justify-center p-2 lg:p-3 w-16 h-16 lg:w-24 lg:h-24 rounded-2xl transition-all relative shrink-0 {{ $newOrdersCount > 0 ? 'bg-rose-500 animate-pulse' : 'bg-white/5 border border-white/5' }}">
                <x-heroicon-o-bell class="w-6 h-6 lg:w-8 lg:h-8 text-white" />
                <span class="text-[8px] lg:text-[10px] font-bold uppercase tracking-wider text-white text-center mt-1">Order</span>
                @if($newOrdersCount > 0) <span class="absolute top-1 right-1 bg-white text-rose-600 text-[10px] font-black w-4 h-4 rounded-full flex items-center justify-center">{{ $newOrdersCount }}</span> @endif
            </button>

            <button type="button" wire:click="openSettlementModal" class="group flex flex-col items-center justify-center p-2 lg:p-3 w-16 h-16 lg:w-24 lg:h-24 rounded-2xl bg-white/5 hover:bg-emerald-500 transition-all shrink-0">
                <x-heroicon-o-banknotes class="w-6 h-6 lg:w-8 lg:h-8 text-gray-400 group-hover:text-white" />
                <span class="text-[8px] lg:text-[10px] font-bold uppercase tracking-wider text-gray-400 group-hover:text-white text-center mt-1">Setelmen</span>
            </button>

            <div class="lg:my-2 w-full h-[1px] bg-white/5"></div>

            <button type="button" wire:click="$set('selectedCategory', 'all')" class="group flex flex-col items-center justify-center p-2 w-20 h-20 lg:w-24 lg:h-24 rounded-2xl transition-all {{ $selectedCategory === 'all' ? 'bg-rose-500 shadow-lg' : 'bg-white/5' }}">
                <x-heroicon-s-squares-2x2 class="w-6 h-6 lg:w-8 lg:h-8 {{ $selectedCategory === 'all' ? 'text-white' : 'text-rose-400' }}" />
                <span class="text-[8px] lg:text-[10px] font-black uppercase text-white mt-1">Semua</span>
            </button>

            @foreach($this->categories as $category)
                <button type="button" wire:click="$set('selectedCategory', '{{ $category->slug }}')" class="group flex flex-col items-center justify-center p-2 w-20 h-20 lg:w-24 lg:h-24 rounded-2xl transition-all relative overflow-hidden shrink-0 {{ $selectedCategory === $category->slug ? 'ring-2 ring-rose-500 scale-105 shadow-xl' : 'bg-white/5' }}">
                    @if($category->image)
                        <img src="{{ Storage::url($category->image) }}" class="absolute inset-0 w-full h-full object-cover opacity-60" />
                        <div class="absolute inset-0 bg-black/60"></div>
                    @endif
                    <span class="relative z-10 text-[10px] font-black uppercase tracking-wider text-white text-center line-clamp-2 px-1">{{ $category->name }}</span>
                </button>
            @endforeach
        </div>

        {{-- MAIN COLUMN --}}
        <div class="flex-1 h-full flex flex-col relative bg-[#0f172a] overflow-hidden">
            
            {{-- Header Bar (Primary Actions) --}}
            <div class="h-16 lg:h-20 shrink-0 px-4 lg:px-6 flex items-center justify-between bg-[#0f172a]/80 backdrop-blur-md border-b border-white/5 sticky top-0 z-30">
                <div class="flex items-center gap-4 w-full max-w-xl">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-heroicon-m-magnifying-glass class="h-5 w-5 text-gray-500" />
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="search" id="search-input" class="block w-full pl-12 pr-4 py-3 bg-[#1e293b] border-transparent focus:border-rose-500 focus:ring-rose-500 rounded-2xl text-white placeholder-gray-500" placeholder="Cari produk (Tekan 'F')...">
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    @if(!$isShiftOpen || $isLocked)
                        <button type="button" wire:click="$set('loginModalOpen', true)" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black px-6 py-2 rounded-xl shadow-lg transition-all transform active:scale-95">
                            <x-heroicon-s-key class="w-5 h-5" />
                            <span class="text-xs uppercase tracking-widest">LOGIN POS</span>
                        </button>
                    @else
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="lock" class="flex items-center gap-2 bg-rose-500/20 hover:bg-rose-500/30 text-rose-500 font-black px-4 py-2 rounded-xl border border-rose-500/30 transition-all transform active:scale-95">
                                <x-heroicon-s-lock-closed class="w-4 h-4" />
                                <span class="text-[10px] uppercase tracking-widest">KUNCI</span>
                            </button>
                            <button type="button" wire:click="openSettlementModal" class="flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white font-black px-4 py-2 rounded-xl shadow-lg transition-all transform active:scale-95">
                                <x-heroicon-s-banknotes class="w-4 h-4" />
                                <span class="text-[10px] uppercase tracking-widest">TUTUP SHIFT</span>
                            </button>
                        </div>
                    @endif

                    <div class="hidden xl:flex flex-col items-end border-l border-white/10 pl-4">
                        <span class="text-xl font-bold text-white tracking-tight" x-data x-text="new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'})"></span>
                    </div>
                </div>
            </div>

            {{-- Products Area (With Overlay if not in shift or locked) --}}
            <div class="flex-1 relative flex flex-col lg:flex-row overflow-hidden">
                <div class="flex-1 overflow-y-auto p-6 custom-scrollbar relative">
                    {{-- Protection Overlay --}}
                    @if(!$isShiftOpen || $isLocked)
                    <div class="absolute inset-0 z-20 bg-[#0f172a]/40 backdrop-blur-sm flex items-center justify-center">
                        <div class="bg-black/60 p-8 rounded-[2rem] text-center border border-white/5">
                            <div class="w-16 h-16 bg-rose-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-rose-500/30">
                                <x-heroicon-s-lock-closed class="w-8 h-8 text-rose-500" />
                            </div>
                            <h3 class="text-xl font-black text-white uppercase tracking-widest">Akses Terbatas</h3>
                            <p class="text-gray-400 text-xs font-bold mt-2">Silakan LOGIN di bagian atas untuk mulai berjualan.</p>
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6 pb-24">
                        @forelse($this->products as $product)
                            <button type="button" {!! $isShiftOpen ? 'wire:click="addToCart('.$product->id.')" wire:loading.class="opacity-50 pointer-events-none scale-95" wire:target="addToCart('.$product->id.')"' : '' !!} class="group relative bg-[#1e293b] rounded-3xl overflow-hidden shadow-lg border border-white/5 {{ $isShiftOpen ? 'hover:border-rose-500/50 cursor-pointer active:scale-95' : 'opacity-40 grayscale cursor-not-allowed' }} transition-all flex flex-col h-full text-left">
                                <div class="aspect-[4/3] w-full bg-[#0f172a] relative overflow-hidden">
                                    @if($product->images && count($product->images) > 0)
                                        <img src="{{ asset('storage/' . $product->images[0]) }}" class="w-full h-full object-cover opacity-90 transition-transform duration-500" />
                                    @else
                                        <div class="flex items-center justify-center h-full opacity-10"><x-heroicon-o-photo class="w-16 h-16"/></div>
                                    @endif
                                    <div class="absolute bottom-0 right-0 bg-rose-600 px-3 py-1.5 rounded-tl-2xl font-black text-white shadow-xl">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                                </div>
                                <div class="p-4 flex-1 flex flex-col justify-center text-center">
                                    <h3 class="font-bold text-gray-200 group-hover:text-white transition-colors line-clamp-2">{{ $product->name }}</h3>
                                </div>
                            </button>
                        @empty
                            <div class="col-span-full py-20 text-center text-gray-500 italic">Produk tidak ditemukan</div>
                        @endforelse
                    </div>
                </div>

                {{-- CART PANEL --}}
                <div class="w-full lg:w-96 bg-[#1e293b] h-auto lg:h-full border-l border-white/5 flex flex-col z-30 shrink-0 relative">
                    {{-- Cart Protection --}}
                    @if(!$isShiftOpen || $isLocked)
                        <div class="absolute inset-0 z-40 bg-[#1e293b]/60 backdrop-blur-sm"></div>
                    @endif

                    <div class="h-16 lg:h-20 shrink-0 px-6 bg-[#0f172a]/50 flex items-center justify-between border-b border-white/5">
                        <div class="flex items-center gap-3">
                            <x-heroicon-m-shopping-bag class="w-6 h-6 text-rose-500" />
                            <h2 class="font-bold text-white">Keranjang</h2>
                        </div>
                        @if(!empty($cart))
                        <button type="button" wire:click="clearCart" class="text-[10px] font-black text-gray-500 hover:text-rose-500 transition-colors uppercase tracking-widest flex items-center gap-1 group">
                            <x-heroicon-o-trash class="w-3 h-3 group-hover:animate-bounce" />
                            Hapus Semua
                        </button>
                        @endif
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                        @forelse($cart as $id => $item)
                            <div class="bg-[#0f172a] rounded-2xl p-3 border border-white/5 flex gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start mb-1">
                                        <h4 class="text-xs font-bold text-white truncate pr-2">{{ $item['name'] }}</h4>
                                        <span class="text-xs font-black text-rose-400">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center bg-[#1e293b] rounded-lg p-1 gap-2">
                                        <div class="flex-1 flex justify-between items-center px-1">
                                            <button type="button" wire:loading.attr="disabled" wire:target="updateQty" wire:click="updateQty({{ $id }}, {{ $item['qty'] - 1 }})" class="p-1 text-gray-500 hover:text-white disabled:opacity-50"><x-heroicon-s-minus class="w-3 h-3"/></button>
                                            <span class="text-xs font-black text-white px-2">{{ $item['qty'] }}</span>
                                            <button type="button" wire:loading.attr="disabled" wire:target="updateQty" wire:click="updateQty({{ $id }}, {{ $item['qty'] + 1 }})" class="p-1 text-gray-500 hover:text-white disabled:opacity-50"><x-heroicon-s-plus class="w-3 h-3"/></button>
                                        </div>
                                        <button type="button" wire:loading.attr="disabled" wire:target="removeFromCart" wire:click="removeFromCart({{ $id }})" class="w-6 h-6 rounded flex items-center justify-center bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition-all disabled:opacity-50">
                                            <x-heroicon-o-x-mark class="w-3 h-3" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-20 text-center text-gray-600 italic">Keranjang Kosong</div>
                        @endforelse
                    </div>

                    <div class="bg-[#0f172a] p-6 border-t border-white/10">
                        <div class="flex justify-between items-center text-2xl font-black text-white mb-6">
                            <span class="text-xs text-gray-500 uppercase tracking-[0.2em]">Total</span>
                            <span class="text-rose-500">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <button type="button" {!! $isShiftOpen ? '@click="paymentModalOpen = true"' : '' !!} class="w-full bg-gradient-to-r from-rose-600 to-pink-600 text-white font-black py-4 rounded-2xl shadow-[0_10px_30px_rgba(225,29,72,0.3)] hover:shadow-[0_15px_40px_rgba(225,29,72,0.4)] hover:from-rose-500 hover:to-pink-500 transition-all text-lg disabled:opacity-30 disabled:shadow-none active:scale-95 border border-rose-400/20" x-bind:disabled="Object.keys($wire.cart).length === 0 || !$wire.isShiftOpen">BAYAR</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALS --}}

    {{-- PAYMENT MODAL (BAYAR) --}}
    <div x-show="paymentModalOpen" class="fixed inset-0 z-[130] flex items-center justify-center bg-black/90 p-4" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        <div class="bg-[#1e293b] w-full max-w-2xl rounded-[2rem] shadow-[0_20px_60px_rgba(0,0,0,0.5)] border border-white/5 flex flex-col max-h-[90vh]" @click.away="if(!showVoucherCatalog) paymentModalOpen = false">
            <div class="p-6 flex justify-between items-center bg-[#0f172a] border-b border-white/5 rounded-t-[2rem] shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-rose-500/10 rounded-xl flex items-center justify-center border border-rose-500/20">
                        <x-heroicon-o-credit-card class="w-5 h-5 text-rose-500" />
                    </div>
                    <div>
                        <h3 class="font-black text-lg uppercase tracking-tight text-white leading-tight">Pembayaran</h3>
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Selesaikan pesanan pelanggan</p>
                    </div>
                </div>
                <button @click="paymentModalOpen = false" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center transition-all group">
                    <x-heroicon-o-x-mark class="w-5 h-5 text-gray-500 group-hover:text-white transition-colors"/>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-[#1e293b] custom-scrollbar">
                {{-- Payment Methods Summary (Horizontal) --}}
                <div class="flex gap-2 p-1 bg-black/30 rounded-2xl border border-white/5 overflow-x-auto no-scrollbar">
                    @foreach(['cash' => 'Tunai', 'qris' => 'QRIS', 'transfer' => 'Transfer', 'debit' => 'Debit'] as $method => $label)
                        <button wire:click="$set('paymentMethod', '{{ $method }}')" class="flex-1 min-w-[100px] py-4 rounded-xl flex flex-col items-center justify-center gap-2 transition-all border {{ $paymentMethod === $method ? 'bg-gradient-to-br from-emerald-600 to-emerald-500 border-emerald-400/30 text-white shadow-lg' : 'bg-[#0f172a] border-white/5 text-gray-500 hover:text-white hover:bg-[#1e293b]' }}">
                            @if($method === 'cash') <x-heroicon-o-banknotes class="w-6 h-6" />
                            @elseif($method === 'qris') <x-heroicon-o-qr-code class="w-6 h-6" />
                            @elseif($method === 'transfer') <x-heroicon-o-arrow-path-rounded-square class="w-6 h-6" />
                            @else <x-heroicon-o-credit-card class="w-6 h-6" /> @endif
                            <span class="text-[10px] font-black uppercase tracking-widest">{{ $label }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Left Column: Amount Input --}}
                    <div class="space-y-4">
                        @if($paymentMethod === 'cash')
                            <div class="bg-black/20 p-5 rounded-2xl border border-white/5 shadow-sm">
                                <label class="text-[9px] font-black text-emerald-500 uppercase tracking-widest mb-3 block">Uang Diterima (Rp)</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-gray-500 text-xl">Rp</span>
                                    <input type="number" x-model="$wire.receivedAmount" class="w-full bg-[#0f172a] border-2 border-emerald-500/20 text-white font-black text-2xl h-16 rounded-xl pl-14 pr-4 focus:border-emerald-500 focus:ring-0 transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none placeholder-gray-700" placeholder="0">
                                </div>
                                <div class="grid grid-cols-3 gap-2 mt-4">
                                    @foreach([10000, 20000, 50000, 100000] as $preset)
                                        <button type="button" x-on:click="$wire.receivedAmount = {{ $preset }}" class="py-3 bg-[#0f172a] hover:bg-emerald-500 hover:text-white text-gray-400 rounded-lg text-xs font-black transition-all border border-white/5 {{ $preset == 100000 ? 'col-span-1' : '' }} active:scale-95">
                                            {{ number_format($preset/1000, 0) }}K
                                        </button>
                                    @endforeach
                                    <button type="button" x-on:click="$wire.receivedAmount = $wire.total" class="py-3 bg-emerald-500/10 hover:bg-emerald-500 text-emerald-500 hover:text-white col-span-2 rounded-lg text-[10px] uppercase tracking-widest font-black transition-all border border-emerald-500/20 active:scale-95">
                                        Uang Pas
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="bg-black/20 p-6 rounded-2xl border border-white/5 flex flex-col justify-center items-center h-full text-center min-h-[200px] shadow-sm">
                                <div class="w-16 h-16 bg-sky-500/10 rounded-full flex items-center justify-center mb-4 border border-sky-500/20">
                                    <x-heroicon-o-check-circle class="w-8 h-8 text-sky-500" />
                                </div>
                                <h4 class="font-black text-white uppercase tracking-widest text-sm mb-2">Non-Tunai</h4>
                                <p class="text-[10px] font-bold text-gray-500 tracking-widest uppercase">Pastikan pembayaran berhasil sebelum menyelesaikan pesanan.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Right Column: Summary --}}
                    <div class="bg-[#0f172a] p-5 rounded-2xl border border-white/5 flex flex-col justify-between shadow-sm">
                        <div class="space-y-4">
                            {{-- Voucher Toggle --}}
                            @if($voucherCode)
                                <div class="flex items-center justify-between bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-xl group/voucher transition-all">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-emerald-500 p-1.5 rounded-lg shadow-lg">
                                            <x-heroicon-s-ticket class="w-4 h-4 text-white" />
                                        </div>
                                        <div>
                                            <div class="font-black text-emerald-400 text-xs tracking-widest uppercase">{{ $voucherCode }}</div>
                                            <div class="text-[8px] text-gray-400 uppercase tracking-widest font-bold mt-0.5">Promo Aktif</div>
                                        </div>
                                    </div>
                                    <button wire:click="removeVoucher" class="text-xs text-rose-500 bg-rose-500/10 hover:bg-rose-500 hover:text-white px-3 py-1.5 rounded-lg font-black transition-all">HAPUS</button>
                                </div>
                            @else
                                <button type="button" @click="$wire.set('showVoucherCatalog', true)" class="w-full bg-[#1e293b] hover:bg-white/5 border border-white/5 p-4 rounded-xl flex items-center justify-between group transition-all">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-white/5 p-2 rounded-lg border border-white/5 group-hover:bg-emerald-500/20 group-hover:border-emerald-500/30 transition-all">
                                            <x-heroicon-o-ticket class="w-4 h-4 text-gray-500 group-hover:text-emerald-500" />
                                        </div>
                                        <span class="text-xs font-black text-gray-400 uppercase tracking-wider group-hover:text-white transition-colors">Gunakan Promo</span>
                                    </div>
                                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-600 group-hover:text-white" />
                                </button>
                            @endif

                            <div class="bg-black/30 w-full h-px"></div>

                            <div class="space-y-2 pt-2">
                                @php
                                    $subtotal = array_reduce($cart, function ($carry, $item) { return $carry + ($item['price'] * $item['qty']); }, 0);
                                @endphp
                                <div class="flex justify-between items-center text-xs font-bold text-gray-400">
                                    <span class="uppercase tracking-widest text-[9px]">Subtotal ({{ collect($cart)->sum('qty') }} item)</span>
                                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                @if($discount > 0)
                                <div class="flex justify-between items-center text-xs font-black text-emerald-500">
                                    <span class="uppercase tracking-widest text-[9px]">Diskon Promo</span>
                                    <span>- Rp {{ number_format($discount, 0, ',', '.') }}</span>
                                </div>
                                @endif
                                <div class="pt-4 mt-2 border-t border-white/5 flex justify-between items-end">
                                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Total Bayar</span>
                                    <span class="text-2xl font-black text-white tracking-tight">Rp {{ number_format($total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        @if($paymentMethod === 'cash')
                            <div class="mt-6 pt-5 border-t border-white/5 flex justify-between items-center">
                                <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Kembalian</span>
                                <span class="text-2xl font-black tracking-tight" x-bind:class="$wire.receivedAmount > $wire.total ? 'text-emerald-400' : 'text-gray-600'" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(Math.max(0, $wire.receivedAmount - $wire.total))">
                                    Rp {{ number_format(max(0, $change), 0, ',', '.') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6 bg-[#0f172a] border-t border-white/5 flex gap-4 shrink-0 rounded-b-[2rem]">
                <button type="button" @click="paymentModalOpen = false" class="flex-1 py-4 bg-[#1e293b] hover:bg-white/10 text-white font-black rounded-xl text-xs uppercase tracking-widest transition-all border border-white/5 shadow-sm active:scale-95">BATAL</button>
                <button type="button" wire:click="checkout" class="flex-[2] py-4 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black rounded-xl text-xs uppercase tracking-[0.2em] transition-all shadow-lg shadow-emerald-900/20 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3 active:scale-95" 
                    x-bind:disabled="($wire.paymentMethod === 'cash' && $wire.receivedAmount < $wire.total) || Object.keys($wire.cart).length === 0">
                    <span wire:loading.remove wire:target="checkout">SELESAIKAN PESANAN</span>
                    <span wire:loading wire:target="checkout" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        MEMPROSES...
                    </span>
                    <x-heroicon-o-arrow-right class="w-4 h-4" wire:loading.remove wire:target="checkout" />
                </button>
            </div>
        </div>
    </div>

    {{-- SUCCESS MODAL --}}
    <div x-show="successModalOpen" class="fixed inset-0 z-[150] flex items-center justify-center bg-black/90 p-4" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
        <div class="bg-[#1e293b] w-full max-w-sm rounded-[2rem] shadow-[0_20px_60px_rgba(16,185,129,0.2)] border border-emerald-500/20 overflow-hidden flex flex-col text-center p-8 relative">
            
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 via-teal-400 to-sky-500"></div>

            <div class="w-24 h-24 bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-6 shadow-[0_0_30px_rgba(16,185,129,0.3)] animate-pulse border border-emerald-500/20">
                <x-heroicon-s-check-circle class="w-14 h-14 text-emerald-500" />
            </div>

            <h2 class="text-2xl font-black text-white uppercase tracking-tight mb-2">Transaksi Sukses!</h2>
            <p class="text-[10px] text-gray-500 uppercase tracking-widest font-black mb-8" x-text="'ORDER #' + orderId"></p>

            <div class="bg-[#0f172a] rounded-2xl p-5 mb-8 border border-white/5">
                <div class="flex justify-between items-center mb-4 pb-4 border-b border-white/5">
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Diterima</span>
                    <span class="text-sm font-black text-white" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(receivedAmount)"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-500">Kembalian</span>
                    <span class="text-xl font-black text-emerald-400" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(changeAmount)"></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button type="button" @click="$dispatch('print-receipt', { orderId: orderId })" class="py-4 bg-[#0f172a] hover:bg-[#1e293b] border border-white/5 text-gray-400 hover:text-white font-black rounded-xl transition-all flex items-center justify-center gap-2 group text-[10px] uppercase tracking-widest shadow-sm">
                    <x-heroicon-o-printer class="w-4 h-4 group-hover:scale-110 transition-transform"/> Print
                </button>
                <button type="button" @click="successModalOpen = false; $wire.resetCart()" class="py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-xl transition-all shadow-lg active:scale-95 text-[10px] uppercase tracking-widest">
                    Pesanan Baru
                </button>
            </div>
        </div>
    </div>
    
    {{-- SOLID COLORED HORIZONTAL PIN PAD --}}
    <div x-show="loginModalOpen || (!isShiftOpen || isLocked)" 
         class="fixed inset-0 z-[110] flex items-center justify-center bg-[#0f172a] p-4" 
         x-cloak 
         x-transition
         wire:key="solid-row-login-wrapper">
        <div class="w-full max-w-6xl flex flex-col items-center gap-16 animate-scaleIn">
            
            {{-- PIN Status Section --}}
            <div class="text-center space-y-6">
                <div class="flex justify-center gap-6" wire:ignore>
                    <template x-for="i in 6" :key="i">
                        <div class="w-4 h-4 rounded-full transition-all duration-300" :class="localPin.length >= i ? 'bg-emerald-500 scale-125 shadow-[0_0_15px_rgba(16,185,129,0.8)]' : 'bg-white/10'"></div>
                    </template>
                </div>
                <h2 class="text-xs font-black text-white/40 uppercase tracking-[0.5em] flex items-center justify-center gap-3">
                    @if($isShiftOpen && $isLocked)
                        <x-heroicon-o-lock-closed class="w-4 h-4 text-rose-500" />
                        SESI TERKUNCI
                    @else
                        <x-heroicon-o-shield-check class="w-4 h-4 text-emerald-500" />
                        VERIFIKASI KASIR
                    @endif
                </h2>
            </div>

            {{-- Horizontal Numeric Pad (Solid Buttons) --}}
            <div class="flex flex-row items-center justify-center gap-4 lg:gap-8 w-full py-8 bg-white/5 rounded-[3rem] border border-white/5 shadow-2xl" wire:ignore>
                @foreach([1, 2, 3, 4, 5, 6, 7, 8, 9, 0] as $digit)
                    <button type="button" 
                            @click="if(localPin.length < 6) localPin += '{{ $digit }}'" 
                            class="w-16 h-16 lg:w-20 lg:h-20 rounded-2xl bg-[#1e293b] text-4xl font-bold text-white hover:bg-emerald-600 active:scale-95 transition-all flex items-center justify-center shadow-lg border border-white/5">
                        {{ $digit }}
                    </button>
                @endforeach
                
                <div class="w-px h-12 bg-white/10 mx-4"></div>

                <button type="button" 
                        @click="localPin = ''" 
                        class="w-16 h-16 lg:w-20 lg:h-20 rounded-2xl bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white flex items-center justify-center transition-all border border-rose-500/20">
                    <x-heroicon-o-backspace class="w-8 h-8" />
                </button>
            </div>

            <div class="w-full max-w-xl h-40 flex flex-col items-center justify-center gap-6" x-show="localPin.length >= 4" x-cloak>
                <div class="w-full" x-show="!isShiftOpen">
                    <div class="w-full flex items-center bg-[#1e293b] rounded-[2rem] p-2 border border-white/5 shadow-2xl animate-fadeIn">
                        <div class="flex-1 flex items-center pl-6">
                            <span class="text-emerald-500 font-black text-xl mr-4 select-none">Rp</span>
                            <input type="number" wire:model="openingCash" placeholder="Ketik Modal Awal..." 
                                   class="w-full bg-transparent border-none p-0 py-4 text-2xl font-bold text-white focus:ring-0 focus:outline-none placeholder:text-gray-600 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        </div>
                        <button type="button" @click="$wire.set('pin', localPin); $wire.openShift()" id="btn-aktifkan" wire:loading.attr="disabled"
                                class="px-10 h-16 rounded-[1.5rem] bg-emerald-600 text-white font-black hover:bg-emerald-500 transition-all text-sm tracking-widest uppercase shadow-xl active:scale-95 disabled:opacity-50 shrink-0">
                            <span wire:loading.remove wire:target="openShift">AKTIFKAN</span>
                            <span wire:loading wire:target="openShift">PROSES...</span>
                        </button>
                    </div>
                </div>
                
                <div class="w-full" x-show="isShiftOpen">
                    <button type="button" @click="$wire.set('pin', localPin); $wire.openShift()" id="btn-aktifkan-unlock" wire:loading.attr="disabled"
                            class="w-full h-16 rounded-[1.5rem] bg-rose-600 text-white font-black hover:bg-rose-500 transition-all text-sm tracking-widest uppercase shadow-xl active:scale-95 disabled:opacity-50 animate-fadeIn">
                        <span wire:loading.remove wire:target="openShift">BUKA KUNCI POS</span>
                        <span wire:loading wire:target="openShift">MEMPROSES...</span>
                    </button>
                </div>
            </div>
            <div class="mt-8 flex justify-center opacity-30 hover:opacity-100 transition-opacity">
                <button type="button" @click="$wire.realLogout()" class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] flex items-center gap-2">
                    <x-heroicon-o-power class="w-3 h-3"/> Logout Akun
                </button>
            </div>

        </div>
    </div>

    {{-- SETTLEMENT MODAL (TUTUP SHIFT) --}}
    <div x-show="showSettlementModal" class="fixed inset-0 z-[125] flex items-center justify-center bg-black/90 p-4" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        <div class="bg-[#1e293b] w-full max-w-xl rounded-[2rem] shadow-[0_20px_60px_rgba(0,0,0,0.5)] border border-white/5 overflow-hidden flex flex-col max-h-[90vh]" @click.away="$wire.set('showSettlementModal', false)">
            
            {{-- Compact Opaque Header --}}
            <div class="p-5 flex justify-between items-center bg-[#0f172a] border-b border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center border border-emerald-500/20">
                        <x-heroicon-o-banknotes class="w-5 h-5 text-emerald-500" />
                    </div>
                    <div>
                        <h3 class="font-black text-lg uppercase tracking-tight text-white leading-tight">Laporan Shift</h3>
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">{{ now()->format('l, d M Y') }}</p>
                    </div>
                </div>
                <button @click="$wire.set('showSettlementModal', false)" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center transition-all group">
                    <x-heroicon-o-x-mark class="w-5 h-5 text-gray-500 group-hover:text-white transition-colors"/>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar bg-[#1e293b]">
                
                {{-- Compact Opaque Summary Cards --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-[#0f172a] p-4 rounded-2xl border border-white/5 shadow-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <x-heroicon-o-presentation-chart-line class="w-3.5 h-3.5 text-emerald-500"/>
                            <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Penjualan</span>
                        </div>
                        <div class="text-xl font-black text-white tracking-tight">Rp {{ number_format($settlementData['total_sales'] ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="bg-[#0f172a] p-4 rounded-2xl border border-white/5 shadow-sm">
                        <div class="flex items-center gap-2 mb-2">
                            <x-heroicon-o-shopping-cart class="w-3.5 h-3.5 text-sky-500"/>
                            <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Transaksi</span>
                        </div>
                        <div class="text-xl font-black text-white tracking-tight">{{ $settlementData['count'] ?? 0 }} <span class="text-[10px] text-gray-500 font-bold ml-1 uppercase">Order</span></div>
                    </div>
                </div>

                {{-- Compact Activity Chart --}}
                <div class="space-y-3">
                    <div class="flex justify-between items-center px-1">
                        <h4 class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Grafik Aktivitas</h4>
                    </div>
                    <div class="h-20 bg-black/20 rounded-2xl border border-white/5 flex items-end justify-between px-4 py-2 gap-1 group/chart">
                        @foreach($settlementData['hourly_trend'] ?? [] as $hour => $percent)
                            <div class="group relative flex-1 flex flex-col items-center gap-2 h-full justify-end">
                                <div class="w-full bg-emerald-500/30 rounded-sm transition-all duration-300 group-hover:bg-emerald-500/60" style="height: {{ max($percent, 8) }}%"></div>
                                <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-[#0f172a] text-[8px] font-black text-white px-1.5 py-0.5 rounded border border-white/10 shadow-2xl scale-0 group-hover:scale-100 transition-all origin-bottom z-50">{{ $hour }}:00</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Compact Breakdowns Grid --}}
                <div class="grid grid-cols-2 gap-3">
                    @foreach(['cash_sales' => ['Tunai', 'bg-emerald-500'], 'qris_sales' => ['QRIS', 'bg-sky-500'], 'transfer_sales' => ['Transfer', 'bg-indigo-500'], 'debit_sales' => ['Debit', 'bg-amber-500']] as $key => $config)
                        <div class="bg-black/20 p-3 rounded-xl border border-white/5 flex flex-col">
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-1.5 h-1.5 rounded-full {{ $config[1] }}"></div>
                                <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest">{{ $config[0] }}</span>
                            </div>
                            <span class="text-sm font-black text-white">Rp {{ number_format($settlementData[$key] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Compact Form Opaque Section --}}
                <div class="bg-black/30 p-5 rounded-2xl border border-white/5 space-y-5">
                    <div class="flex items-center gap-2 mb-1">
                        <x-heroicon-o-currency-dollar class="w-4 h-4 text-emerald-500" />
                        <h4 class="text-[10px] font-black text-white uppercase tracking-widest">Penutupan Kas</h4>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="relative">
                            <label class="absolute left-4 top-1.5 text-[8px] font-black text-emerald-500/60 uppercase tracking-widest">Uang Tunai di Laci</label>
                            <span class="absolute left-4 bottom-3 text-gray-400 font-black text-lg">Rp</span>
                            <input type="number" wire:model="actualCashAmount" placeholder="0" class="w-full bg-[#0f172a] border border-white/10 rounded-xl pt-6 pb-2.5 pl-11 pr-4 text-xl font-black text-white focus:border-emerald-500/50 transition-all focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        </div>

                        <div class="relative">
                            <label class="absolute left-4 top-1.5 text-[8px] font-black text-gray-600 uppercase tracking-widest">Catatan (Opsional)</label>
                            <textarea wire:model="settlementNotes" placeholder="Tulis catatan..." class="w-full bg-[#0f172a] border border-white/5 rounded-xl pt-6 pb-3 px-4 text-xs font-bold text-white placeholder-gray-800 focus:border-emerald-500/30 transition-all focus:ring-0 min-h-[80px] outline-none resize-none"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-5 bg-[#0f172a] border-t border-white/5 flex gap-3">
                <button wire:click="closeSettlement" class="flex-1 h-12 bg-rose-600 hover:bg-rose-500 text-white font-black rounded-xl text-[10px] uppercase tracking-[0.2em] transition-all shadow-lg active:scale-95 flex items-center justify-center gap-3">
                    <span wire:loading.remove wire:target="closeSettlement">TUTUP SHIFT & CETAK</span>
                    <div wire:loading wire:target="closeSettlement" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        MEMPROSES...
                    </div>
                </button>
            </div>
        </div>
    </div>

    {{-- HISTORY MODAL (RIWAYAT TRANSAKSI) --}}
    {{-- HISTORY MODAL (RIWAYAT TRANSAKSI) --}}
    <div x-show="historyModalOpen" class="fixed inset-0 z-[125] flex items-center justify-center bg-black/90 p-4" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        <div class="bg-[#1e293b] w-full max-w-6xl h-[90vh] rounded-[2rem] shadow-[0_20px_60px_rgba(0,0,0,0.5)] border border-white/5 overflow-hidden flex flex-col" @click.away="historyModalOpen = false">
            
            <div class="p-5 flex justify-between items-center bg-[#0f172a] border-b border-white/5 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-sky-500/10 rounded-xl flex items-center justify-center border border-sky-500/20">
                        <x-heroicon-o-clock class="w-5 h-5 text-sky-500" />
                    </div>
                    <div>
                        <h3 class="font-black text-lg uppercase tracking-tight text-white leading-tight">Riwayat</h3>
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Transaksi hari ini</p>
                    </div>
                </div>
                <button @click="historyModalOpen = false" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center transition-all group">
                    <x-heroicon-o-x-mark class="w-5 h-5 text-gray-500 group-hover:text-white transition-colors"/>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 custom-scrollbar bg-[#0f172a]">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @forelse($this->recent_orders as $order)
                        <div class="bg-gradient-to-br from-indigo-800 to-indigo-900 p-4 rounded-2xl border border-indigo-700 hover:border-sky-400 transition-all flex flex-col justify-between group shadow-md hover:shadow-lg h-[140px]">
                            
                            <div>
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex flex-col">
                                        <div class="text-[14px] font-black text-white tracking-tight leading-none mb-1.5">#{{ $order->id }}</div>
                                        <span class="text-[9px] font-bold text-indigo-300 uppercase tracking-widest">{{ $order->created_at->format('d M \• H:i') }}</span>
                                    </div>
                                    <div class="text-right flex flex-col items-end">
                                        <div class="text-[13px] font-black text-sky-300 tracking-tight leading-none mb-1.5">Rp{{ number_format($order->grand_total, 0, ',', '.') }}</div>
                                        <div class="px-2 py-0.5 bg-indigo-950 rounded text-[8px] font-black text-sky-300 uppercase tracking-widest border border-indigo-800">{{ strtoupper($order->payment_method) }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-2 w-full mt-auto">
                                <button @click="$dispatch('print-receipt', { orderId: {{ $order->id }} })" class="flex-1 h-8 bg-sky-500 hover:bg-sky-400 text-white font-black rounded-xl text-[9px] uppercase tracking-widest transition-all flex items-center justify-center gap-1.5 shadow-sm active:scale-95 border border-sky-400">
                                    <x-heroicon-o-printer class="w-4 h-4" /> <span class="hidden sm:inline">CETAK</span>
                                </button>
                                <button wire:click="loadOrderDetail({{ $order->id }})" class="flex-1 h-8 bg-indigo-700 hover:bg-indigo-600 text-white font-black rounded-xl text-[9px] uppercase tracking-widest transition-all flex items-center justify-center gap-1 border border-indigo-500 shadow-sm active:scale-95">
                                    <x-heroicon-o-eye class="w-4 h-4" /> <span class="hidden sm:inline">DETAIL</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-24 text-center flex flex-col items-center gap-4 text-gray-700">
                            <x-heroicon-o-inbox class="w-10 h-10 opacity-10" />
                            <p class="font-black uppercase tracking-widest text-[10px]">Belum ada transaksi</p>
                        </div>
                    @endforelse
                </div>

                @if($this->recent_orders->hasMorePages())
                    <div class="mt-8 flex justify-center">
                        <button wire:click="loadMoreRecentOrders" class="px-8 py-4 bg-white/5 hover:bg-white/10 border border-white/5 rounded-xl text-[9px] font-black uppercase tracking-widest text-gray-500 hover:text-sky-500 transition-all shadow-sm">
                            MUAT LEBIH BANYAK
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- NEW ORDERS MODAL (MANAJEMEN ORDER ONLINE) --}}
    {{-- ORDER MANAGEMENT MODAL --}}
    <div x-show="newOrdersModalOpen" class="fixed inset-0 z-[125] flex items-center justify-center bg-black/90 p-4" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        <div class="bg-[#1e293b] w-full max-w-6xl h-[90vh] rounded-[2rem] shadow-[0_20px_60px_rgba(0,0,0,0.5)] border border-white/5 overflow-hidden flex flex-col" @click.away="newOrdersModalOpen = false">
            
            <div class="p-5 flex flex-col lg:flex-row justify-between items-center bg-[#0f172a] border-b border-white/5 shrink-0 gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-rose-500/10 rounded-xl flex items-center justify-center border border-rose-500/20">
                        <x-heroicon-o-bell class="w-5 h-5 text-rose-500" />
                    </div>
                    <div>
                        <h3 class="font-black text-lg uppercase tracking-tight text-white leading-tight">Manajemen Order</h3>
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Pantau & proses pesanan pelanggan</p>
                    </div>
                </div>

                <div class="flex bg-black/30 p-1 rounded-xl border border-white/5">
                    @foreach(['new' => ['Baru', 'bg-rose-600'], 'processing' => ['Proses', 'bg-sky-600'], 'ready' => ['Siap', 'bg-emerald-600']] as $tab => $config)
                        <button wire:click="$set('activeTab', '{{ $tab }}')" class="px-5 py-1.5 rounded-lg font-black text-[9px] uppercase tracking-wider transition-all flex items-center gap-2 {{ $activeTab === $tab ? $config[1] . ' text-white shadow-sm' : 'text-gray-500 hover:text-white hover:bg-white/5' }}">
                            {{ $config[0] }}
                            @php $count = $this->{$tab . 'OrdersCount'} ?? 0; @endphp
                            @if($count > 0) 
                                <span class="bg-white/20 px-1.5 py-0.5 rounded text-[8px] font-black">{{ $count }}</span> 
                            @endif
                        </button>
                    @endforeach
                </div>

                <button @click="newOrdersModalOpen = false" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center transition-all group">
                    <x-heroicon-o-x-mark class="w-5 h-5 text-gray-500 group-hover:text-white transition-colors"/>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6 custom-scrollbar bg-[#0f172a]">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @forelse($this->orders_by_tab as $order)
                        @php
                            $cardBg = $activeTab === 'new' ? 'bg-gradient-to-br from-rose-800 to-rose-900 border-rose-700' : ($activeTab === 'processing' ? 'bg-gradient-to-br from-blue-800 to-blue-900 border-blue-700' : 'bg-gradient-to-br from-emerald-800 to-emerald-900 border-emerald-700');
                            $badgeBg = $activeTab === 'new' ? 'bg-rose-950 border border-rose-800 text-rose-200' : ($activeTab === 'processing' ? 'bg-blue-950 border border-blue-800 text-blue-200' : 'bg-emerald-950 border border-emerald-800 text-emerald-200');
                            $badgeText = $activeTab === 'new' ? 'text-rose-200' : ($activeTab === 'processing' ? 'text-blue-200' : 'text-emerald-200');
                        @endphp
                        <div class="{{ $cardBg }} p-4 rounded-2xl hover:border-white/50 transition-all flex flex-col justify-between group shadow-md hover:shadow-lg h-[150px] relative overflow-hidden">
                            <div class="absolute -right-4 -top-4 w-16 h-16 rounded-full blur-2xl opacity-20 {{ $activeTab === 'new' ? 'bg-rose-500' : ($activeTab === 'processing' ? 'bg-sky-500' : 'bg-emerald-400') }}"></div>
                            
                            <div>
                                <div class="flex justify-between items-start mb-3 relative z-10">
                                    <div class="flex flex-col">
                                        <div class="text-[14px] font-black text-white tracking-tight leading-none mb-1.5">#{{ $order->id }}</div>
                                        <span class="text-[9px] font-bold {{ $badgeText }} uppercase tracking-widest">{{ $order->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-right flex flex-col items-end">
                                        <div class="text-[13px] font-black text-white tracking-tight leading-none mb-1.5">Rp{{ number_format($order->grand_total, 0, ',', '.') }}</div>
                                        <div class="px-2 py-0.5 {{ $badgeBg }} rounded text-[8px] font-black uppercase tracking-widest">{{ strtoupper($order->payment_method) }}</div>
                                    </div>
                                </div>
                                <div class="mt-2 line-clamp-1 relative z-10 w-full overflow-hidden">
                                    <span class="text-[11px] font-bold text-white truncate w-full inline-block pr-6">{{ $order->items->first()->product->name ?? 'Produk' }}</span>
                                    @if($order->items->count() > 1)
                                        <span class="absolute right-0 top-0 {{ $badgeBg }} px-1 py-0.5 rounded text-[8px] font-black">+{{ $order->items->count() - 1 }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex gap-2 w-full mt-auto relative z-10">
                                @php
                                    $action = $activeTab === 'new' ? 'process' : ($activeTab === 'processing' ? 'ready' : 'complete');
                                    $label = $activeTab === 'new' ? 'PROSES' : ($activeTab === 'processing' ? 'SIAP' : 'SELESAIKAN');
                                    $colorBtn = $activeTab === 'new' ? 'bg-rose-500 hover:bg-rose-400 text-white shadow-sm border-rose-400' : ($activeTab === 'processing' ? 'bg-sky-500 hover:bg-sky-400 text-white shadow-sm border-sky-400' : 'bg-emerald-500 hover:bg-emerald-400 text-white shadow-sm border-emerald-400');
                                    $btnDetail = $activeTab === 'new' ? 'bg-rose-700 hover:bg-rose-600 border-rose-500 text-white' : ($activeTab === 'processing' ? 'bg-blue-700 hover:bg-blue-600 border-blue-500 text-white' : 'bg-emerald-700 hover:bg-emerald-600 border-emerald-500 text-white');
                                @endphp
                                <button wire:click="processOrder({{ $order->id }}, '{{ $action }}')" class="flex-1 h-8 {{ $colorBtn }} font-black rounded-xl transition-all text-[9px] uppercase tracking-widest active:scale-95 flex items-center justify-center border">
                                    <span class="truncate px-1">{{ $label }}</span>
                                </button>
                                <button wire:click="loadOrderDetail({{ $order->id }})" class="w-10 h-8 shrink-0 {{ $btnDetail }} rounded-xl flex items-center justify-center transition-all border shadow-sm active:scale-95">
                                    <x-heroicon-o-eye class="w-4 h-4"/>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-24 text-center flex flex-col items-center gap-4 text-gray-700">
                            <x-heroicon-o-inbox class="w-10 h-10 opacity-10" />
                            <p class="font-black uppercase tracking-widest text-[10px]">Tidak ada pesanan {{ $activeTab }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ORDER DETAIL MODAL --}}
    {{-- ORDER DETAIL MODAL --}}
    <div x-show="orderDetailModalOpen" class="fixed inset-0 z-[140] flex items-center justify-center bg-black/90 p-4" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        <div class="bg-[#1e293b] w-full max-w-2xl rounded-[2rem] shadow-[0_20px_60px_rgba(0,0,0,0.5)] border border-white/5 overflow-hidden flex flex-col max-h-[90vh]" @click.away="orderDetailModalOpen = false">
            @if($selectedOrder)
                <div class="p-5 flex justify-between items-center bg-[#0f172a] border-b border-white/5 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center border border-white/5 shadow-inner">
                            <span class="text-gray-400 font-black text-[10px]">#{{ $selectedOrder->id }}</span>
                        </div>
                        <div>
                            <h3 class="font-black text-lg uppercase tracking-tight text-white leading-tight">Detail Transaksi</h3>
                            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">{{ $selectedOrder->created_at->format('d M Y • H:i') }}</p>
                        </div>
                    </div>
                    <button @click="orderDetailModalOpen = false" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center transition-all group">
                        <x-heroicon-o-x-mark class="w-5 h-5 text-gray-500 group-hover:text-white transition-colors"/>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto p-6 custom-scrollbar space-y-6 bg-[#1e293b]">
                    {{-- Summary Bar --}}
                    <div class="grid grid-cols-2 gap-3 bg-black/20 p-4 rounded-xl border border-white/5">
                        <div>
                            <span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Metode Bayar</span>
                            <div class="text-sm font-black text-white flex items-center gap-2 uppercase tracking-tight mt-0.5">
                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                {{ $selectedOrder->payment_method }}
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[8px] font-black text-gray-500 uppercase tracking-widest">Status Pembayaran</span>
                            <div class="text-emerald-400 font-black text-sm uppercase tracking-widest mt-0.5">{{ strtoupper($selectedOrder->payment_status) }}</div>
                        </div>
                    </div>

                    {{-- Items List --}}
                    <div class="space-y-3">
                        <h4 class="text-[9px] font-black text-gray-500 uppercase tracking-widest px-1">Daftar Belanja</h4>
                        <div class="bg-black/20 rounded-2xl border border-white/5 divide-y divide-white/5 overflow-hidden">
                            @foreach($selectedOrder->items as $item)
                                <div class="flex items-center gap-4 p-4 transition-all hover:bg-white/[0.02]">
                                    <div class="w-10 h-10 bg-[#0f172a] rounded-lg flex items-center justify-center font-black text-white shrink-0 border border-white/5 text-[10px]">
                                        {{ $item->quantity }}x
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs font-bold text-white truncate line-clamp-1">{{ $item->product->name }}</div>
                                        <div class="text-[9px] text-gray-500 font-black uppercase tracking-widest mt-0.5">@ {{ number_format($item->unit_amount, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs font-black text-white tabular-nums">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Subtotals --}}
                    <div class="space-y-3 bg-[#0f172a] p-5 rounded-2xl border border-white/5 shadow-sm">
                        <div class="flex justify-between items-center text-gray-400 font-bold text-xs">
                            <span class="uppercase tracking-widest text-[9px]">Subtotal</span>
                            <span class="tabular-nums">Rp {{ number_format($selectedOrder->grand_total + $selectedOrder->discount_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-rose-500 font-bold text-xs">
                            <span class="uppercase tracking-widest text-[9px]">Diskon / Promo @if($selectedOrder->voucher_code) ({{ $selectedOrder->voucher_code }}) @endif</span>
                            <span class="tabular-nums">- Rp {{ number_format($selectedOrder->discount_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-4 border-t border-white/10 flex justify-between items-end">
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-0.5">Total Bayar</span>
                                <span class="text-gray-600 font-black text-[8px] uppercase tracking-[0.2em]">{{ $selectedOrder->payment_method }}</span>
                            </div>
                            <span class="text-2xl font-black text-emerald-500 tabular-nums tracking-tight">Rp {{ number_format($selectedOrder->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-5 bg-[#0f172a] border-t border-white/5 flex gap-3 shrink-0">
                    <button @click="$dispatch('print-receipt', { orderId: {{ $selectedOrder->id }} })" class="flex-1 h-12 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-white font-black text-[9px] uppercase tracking-widest transition-all flex items-center justify-center gap-2 group shadow-sm">
                        <x-heroicon-o-printer class="w-4 h-4 text-gray-400 group-hover:text-white"/> CETAK STRUK
                    </button>
                    @if($selectedOrder->status !== 'delivered')
                        @php
                           $btnLabel = $selectedOrder->status === 'new' ? 'PROSES' : ($selectedOrder->status === 'processing' ? 'SIAP' : 'SELESAIKAN');
                           $nextStatus = $selectedOrder->status === 'new' ? 'process' : ($selectedOrder->status === 'processing' ? 'ready' : 'complete');
                           $btnColor = $selectedOrder->status === 'new' ? 'bg-rose-600 hover:bg-rose-500' : ($selectedOrder->status === 'processing' ? 'bg-sky-600 hover:bg-sky-500' : 'bg-emerald-600 hover:bg-emerald-500');
                        @endphp
                        <button wire:click="processOrder({{ $selectedOrder->id }}, '{{ $nextStatus }}')" class="flex-1 h-12 {{ $btnColor }} rounded-xl text-white font-black text-[9px] uppercase tracking-widest transition-all shadow-lg shadow-black/20 active:scale-95">
                            {{ $btnLabel }}
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- VOUCHER CATALOG MODAL --}}
    <div x-show="showVoucherCatalog" class="fixed inset-0 z-[160] flex items-center justify-center bg-black/90 p-4" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" @close-modals.window="$wire.set('showVoucherCatalog', false)">
        <div class="bg-[#1e293b] w-full max-w-lg rounded-[2rem] shadow-[0_20px_60px_rgba(16,185,129,0.1)] border border-emerald-500/10 overflow-hidden flex flex-col max-h-[70vh]" @click.away="$wire.set('showVoucherCatalog', false)">
            <div class="p-5 flex justify-between items-center bg-[#0f172a] border-b border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center border border-emerald-500/20 shadow-lg shadow-emerald-500/5">
                        <x-heroicon-o-ticket class="w-5 h-5 text-emerald-500" />
                    </div>
                    <div>
                        <h3 class="font-black text-lg uppercase tracking-tight text-white leading-tight">Promo Spesial</h3>
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Pilih voucher belanja</p>
                    </div>
                </div>
                <button @click="$wire.set('showVoucherCatalog', false)" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/10 flex items-center justify-center transition-all group">
                    <x-heroicon-o-x-mark class="w-5 h-5 text-gray-500 group-hover:text-white transition-colors"/>
                </button>
            </div>
            <div class="p-6 space-y-3 overflow-y-auto custom-scrollbar bg-[#1e293b] flex-1">
                @forelse($this->available_vouchers as $v)
                    <div wire:click="selectVoucher('{{ $v->code }}')" class="group relative bg-[#0f172a] p-4 rounded-2xl border border-white/5 hover:border-emerald-500/50 transition-all cursor-pointer active:scale-95 flex items-center justify-between shadow-sm overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="flex items-center gap-4 relative z-10">
                            <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex flex-col items-center justify-center border border-emerald-500/20 group-hover:bg-emerald-500 transition-all shadow-inner">
                                <span class="text-emerald-500 font-black text-sm tracking-tighter group-hover:text-white">{{ $v->type === 'percent' ? $v->amount . '%' : 'Rp' }}</span>
                            </div>
                            <div>
                                <div class="text-base font-black text-white group-hover:text-emerald-400 transition-colors tracking-tight">{{ $v->code }}</div>
                                <div class="text-[8px] font-bold text-gray-500 uppercase tracking-widest mt-0.5">{{ $v->type === 'percent' ? 'Diskon Persentase' : 'Potongan ' . number_format($v->amount, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-emerald-500/20 transition-all relative z-10">
                            <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-500 group-hover:text-emerald-400 transition-colors" />
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center flex flex-col items-center gap-4 text-gray-700">
                        <x-heroicon-o-ticket class="w-8 h-8 opacity-10" />
                        <p class="font-black uppercase tracking-widest text-[9px]">Belum ada voucher aktif</p>
                    </div>
                @endforelse
            </div>
            <div class="p-4 bg-[#0f172a] border-t border-white/5 text-center">
                <p class="text-[8px] font-black text-gray-700 uppercase tracking-widest">Ketuk voucher untuk menggunakan</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('play-sound', (event) => {
                 const status = event.status || (event[0] ? event[0].status : null);
                 if(status === 'success') document.getElementById('beep-sound').play().catch(e=>{});
                 else if (status === 'checkout') document.getElementById('cash-sound').play().catch(e=>{});
            });
            @this.on('print-receipt', (event) => {
                const id = Array.isArray(event) ? event[0].orderId : event.orderId;
                if(id) window.open(`/admin/pos/print/${id}`, '_blank', 'height=600,width=400');
            });
            @this.on('print-settlement', (event) => {
                const id = Array.isArray(event) ? event[0].settlementId : event.settlementId;
                if(id) window.open(`/admin/pos/settlement/print/${id}`, '_blank', 'height=600,width=400');
            });
        });
    </script>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.4s ease-out both; }
        @keyframes scaleIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
        .animate-scaleIn { animation: scaleIn 0.2s ease-out; }
    </style>
</div>
