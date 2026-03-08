<x-layout>
    <div class="bg-gray-50 min-h-screen">
        <div class="max-w-2xl mx-auto pt-16 pb-24 px-4 sm:px-6 lg:max-w-7xl lg:px-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 mb-8">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600">Checkout</span>
            </h1>

            <form action="{{ route('checkout.store') }}" method="POST" class="lg:grid lg:grid-cols-2 lg:gap-x-12 xl:gap-x-16" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf
                <!-- Honeypot -->
                <input type="text" name="x_honeypot" class="hidden" value="" tabindex="-1" autocomplete="off">
                
                <!-- Contact & Shipping Information -->
                <div class="space-y-8">
                    <!-- Contact Information -->
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <span class="w-8 h-8 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">1</span>
                            Informasi Kontak
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="name" name="name" required maxlength="100" value="{{ old('name') }}"
                                           class="block w-full pl-10 pr-3 py-3 border @error('name') border-red-500 @else border-gray-200 @enderror rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-transparent transition" 
                                           placeholder="Masukkan nama lengkap Anda">
                                </div>
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="email" id="email" name="email" required maxlength="100" value="{{ old('email') }}"
                                           class="block w-full pl-10 pr-3 py-3 border @error('email') border-red-500 @else border-gray-200 @enderror rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-transparent transition" 
                                           placeholder="your@email.com">
                                </div>
                                @error('email')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <input type="tel" id="phone" name="phone" required maxlength="15" value="{{ old('phone') }}"
                                           class="block w-full pl-10 pr-3 py-3 border @error('phone') border-red-500 @else border-gray-200 @enderror rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-transparent transition" 
                                           placeholder="08xxxxxxxxxx">
                                </div>
                                @error('phone')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <span class="w-8 h-8 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">2</span>
                            Alamat Pengiriman
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="address" id="address" required maxlength="500" value="{{ old('address') }}"
                                           class="block w-full pl-10 pr-3 py-3 border @error('address') border-red-500 @else border-gray-200 @enderror rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-transparent transition" 
                                           placeholder="Jl. Contoh No. 123">
                                </div>
                                @error('address')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                                    <input type="text" name="city" id="city" required maxlength="100" value="{{ old('city') }}"
                                           class="block w-full px-3 py-3 border @error('city') border-red-500 @else border-gray-200 @enderror rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-transparent transition" 
                                           placeholder="Jakarta">
                                    @error('city')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                                    <input type="text" name="postal_code" id="postal_code" required maxlength="10" value="{{ old('postal_code') }}"
                                           class="block w-full px-3 py-3 border @error('postal_code') border-red-500 @else border-gray-200 @enderror rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-transparent transition" 
                                           placeholder="12345">
                                    @error('postal_code')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <span class="w-8 h-8 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">3</span>
                            Metode Pembayaran
                        </h2>

                        <div class="space-y-3" x-data="{ selected: '' }">
                            <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all" :class="selected === 'bank_transfer' ? 'border-rose-500 bg-rose-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="payment_method" value="bank_transfer" x-model="selected" class="sr-only">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Bank Transfer</p>
                                    <p class="text-sm text-gray-500">Transfer ke rekening bank kami</p>
                                </div>
                                <div class="w-5 h-5 border-2 rounded-full flex items-center justify-center" :class="selected === 'bank_transfer' ? 'border-rose-500' : 'border-gray-300'">
                                    <div class="w-3 h-3 rounded-full" :class="selected === 'bank_transfer' ? 'bg-rose-500' : ''"></div>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all" :class="selected === 'e_wallet' ? 'border-rose-500 bg-rose-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="payment_method" value="e_wallet" x-model="selected" class="sr-only">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">E-Wallet</p>
                                    <p class="text-sm text-gray-500">GoPay, OVO, DANA, etc.</p>
                                </div>
                                <div class="w-5 h-5 border-2 rounded-full flex items-center justify-center" :class="selected === 'e_wallet' ? 'border-rose-500' : 'border-gray-300'">
                                    <div class="w-3 h-3 rounded-full" :class="selected === 'e_wallet' ? 'bg-rose-500' : ''"></div>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all" :class="selected === 'cash_on_delivery' ? 'border-rose-500 bg-rose-50' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" name="payment_method" value="cash_on_delivery" x-model="selected" class="sr-only">
                                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">Bayar di Tempat (COD)</p>
                                    <p class="text-sm text-gray-500">Bayar saat pesanan sampai</p>
                                </div>
                                <div class="w-5 h-5 border-2 rounded-full flex items-center justify-center" :class="selected === 'cash_on_delivery' ? 'border-rose-500' : 'border-gray-300'">
                                    <div class="w-3 h-3 rounded-full" :class="selected === 'cash_on_delivery' ? 'bg-rose-500' : ''"></div>
                                </div>
                            </label>

                            <div x-show="selected === 'cash_on_delivery'" x-transition 
                                 class="p-4 bg-orange-50 border border-orange-100 rounded-xl text-xs text-orange-800 flex items-start">
                                <svg class="w-5 h-5 mr-2 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span><strong>Penting:</strong> Untuk metode COD, Anda wajib melakukan konfirmasi uang muka (DP) terlebih dahulu agar pesanan dapat diproses.</span>
                            </div>

                            @error('payment_method')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Catatan Pesanan (Opsional)</h2>
                        <textarea name="notes" id="notes" rows="3" 
                                  class="block w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-transparent transition resize-none" 
                                  placeholder="Instruksi khusus untuk pesanan Anda..."></textarea>
                    </div>
                    
                    {{-- <input type="hidden" name="shipping_amount" value="25000"> --}}
                </div>

                <!-- Order Summary -->
                <div class="mt-10 lg:mt-0">
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden sticky top-24">
                        <div class="px-6 py-4 bg-gradient-to-r from-rose-500 to-purple-600">
                            <h2 class="text-lg font-bold text-white">Ringkasan Pesanan</h2>
                        </div>
                        
                        <div class="p-6">
                            <ul role="list" class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
                                @if(session('cart'))
                                    @foreach(session('cart') as $id => $details)
                                    <li class="flex py-4">
                                        <div class="flex-shrink-0">
                                            <img src="{{ $details['image'] ? Storage::url($details['image']) : 'https://via.placeholder.com/100' }}" 
                                                 alt="{{ $details['name'] }}" 
                                                 loading="lazy"
                                                 decoding="async"
                                                 class="w-16 h-16 rounded-xl object-cover shadow-sm">
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <h4 class="text-sm font-medium text-gray-900 line-clamp-2">{{ $details['name'] }}</h4>
                                            <p class="mt-1 text-sm text-gray-500">Qty: {{ $details['quantity'] }}</p>
                                            <p class="mt-1 text-sm font-medium text-gray-900">IDR {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}</p>
                                        </div>
                                    </li>
                                    @endforeach
                                @endif
                            </ul>
                            
                            @php 
                                $subtotal = 0;
                                if(session('cart')) {
                                    foreach(session('cart') as $id => $details) {
                                        $subtotal += $details['price'] * $details['quantity'];
                                    }
                                }
                                $shipping = 0;
                                $total = $subtotal + $shipping;
                            @endphp
                            
                            <dl class="border-t border-gray-200 pt-6 space-y-4">
                                <div class="flex items-center justify-between">
                                    <dt class="text-sm text-gray-600">Subtotal</dt>
                                    <dd class="text-sm font-medium text-gray-900">IDR {{ number_format($subtotal, 0, ',', '.') }}</dd>
                                </div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-sm text-gray-600">Pengiriman</dt>
                                    <dd class="text-sm font-medium text-green-600">Gratis</dd>
                                </div>
                                
                                <!-- Voucher Section -->
                                <div class="pt-4 border-t border-gray-200">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Voucher</label>
                                    <div class="flex space-x-2">
                                        <input type="text" id="voucher_input" class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-rose-500 focus:border-rose-500" placeholder="Masukkan kode">
                                        <button type="button" id="apply_voucher_btn" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-700">Gunakan</button>
                                    </div>
                                    <p id="voucher_message" class="text-xs mt-1 hidden"></p>
                                </div>

                                <div class="flex items-center justify-between">
                                    <dt class="text-sm text-gray-600">Diskon</dt>
                                    <dd class="text-sm font-medium text-green-600" id="discount_display">- IDR 0</dd>
                                </div>

                                <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                                    <dt class="text-base font-bold text-gray-900">Total</dt>
                                    <dd class="text-base font-bold bg-clip-text text-transparent bg-gradient-to-r from-rose-500 to-purple-600 px-0.5" id="total_display">IDR {{ number_format($total, 0, ',', '.') }}</dd>
                                </div>
                            </dl>

                            <input type="hidden" name="voucher_code" id="voucher_code">
                            
                            <script>
                                document.getElementById('apply_voucher_btn').addEventListener('click', function() {
                                    const code = document.getElementById('voucher_input').value;
                                    const subtotal = {{ $subtotal }};
                                    
                                    if(!code) return;

                                    fetch('{{ route("checkout.apply-voucher") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            code: code,
                                            total_amount: subtotal
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        const messageEl = document.getElementById('voucher_message');
                                        messageEl.classList.remove('hidden', 'text-green-600', 'text-red-600');
                                        
                                        if (data.valid) {
                                            messageEl.textContent = data.message;
                                            messageEl.classList.add('text-green-600');
                                            
                                            // Update Discount Display
                                            document.getElementById('discount_display').textContent = '- IDR ' + new Intl.NumberFormat('id-ID').format(data.discount_amount);
                                            
                                            // Update Total Display
                                            const newTotal = (subtotal - data.discount_amount) + {{ $shipping }};
                                            document.getElementById('total_display').textContent = 'IDR ' + new Intl.NumberFormat('id-ID').format(newTotal);
                                            
                                            // Set hidden input
                                            document.getElementById('voucher_code').value = data.code;
                                        } else {
                                            messageEl.textContent = data.message;
                                            messageEl.classList.add('text-red-600');
                                            
                                            // Reset
                                            document.getElementById('discount_display').textContent = '- IDR 0';
                                            document.getElementById('total_display').textContent = 'IDR {{ number_format($total, 0, ',', '.') }}';
                                            document.getElementById('voucher_code').value = '';
                                        }
                                        messageEl.classList.remove('hidden');
                                    });
                                });
                            </script>

                            <div class="mt-6">
                                <button type="submit" 
                                        :disabled="submitting"
                                        :class="{ 'opacity-50 cursor-not-allowed': submitting }"
                                        class="w-full bg-gradient-to-r from-rose-500 to-purple-600 border border-transparent rounded-xl shadow-lg py-4 px-4 text-base font-bold text-white hover:shadow-xl hover:scale-[1.02] transform transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 flex justify-center items-center">
                                    <svg x-show="!submitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <svg x-show="submitting" class="animate-spin h-5 w-5 mr-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span x-text="submitting ? 'Memproses Pesanan...' : 'Buat Pesanan'"></span>
                                </button>
                            </div>
                            
                            <div class="mt-4 flex items-center justify-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Keamanan Terjamin
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layout>
