<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class PosPage extends Page
{
    use WithPagination;

    public static function canAccess(): bool
    {
        return true;
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-cart';

    protected string $view = 'filament.pages.pos-page';
    protected static string $layout = 'components.layouts.pos';

    protected static ?string $title = 'Kasir (POS)';
    
    protected static ?string $navigationLabel = 'Kasir (POS)';

    public $search = '';
    public $cart = [];
    public $total = 0;
    public $change = 0;
    public $receivedAmount = 0;
    public $selectedCategory = 'all';
    public $paymentMethod = 'cash'; // Default payment method
    
    // Voucher Properties
    public $voucherCode = '';
    public $discount = 0;

    public $newOrdersCount = 0;
    public $processingOrdersCount = 0;
    public $readyOrdersCount = 0;

    public function mount()
    {
        $this->cart = [];
        $this->checkNewOrders();
    }

    // ... (existing update methods) ...
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (!$product) return;

        if ($product->stock <= 0) {
            Notification::make()->title('Stok Habis')->danger()->send();
            return;
        }

        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['qty'] + 1 > $product->stock) {
                Notification::make()->title('Stok Tidak Cukup')->warning()->send();
                return;
            }
            $this->cart[$productId]['qty']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'qty' => 1,
                'image' => $product->images ? $product->images[0] : null,
            ];
        }
        
        $this->calculateTotal();
        $this->dispatch('play-sound', status: 'success');
    }

    public function removeFromCart($productId)
    {
        if (isset($this->cart[$productId])) {
            unset($this->cart[$productId]);
            $this->calculateTotal();
        }
    }

    public function updateQty($productId, $qty)
    {
        if (!isset($this->cart[$productId])) return;

        if ($qty <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        $product = Product::find($productId);
        if ($qty > $product->stock) {
            Notification::make()->title('Stok Tidak Cukup')->warning()->send();
            return;
        }

        $this->cart[$productId]['qty'] = $qty;
        $this->calculateTotal();
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->calculateTotal();
    }

    public function resetCart()
    {
        $this->clearCart();
        $this->receivedAmount = 0;
        $this->change = 0;
        $this->paymentMethod = 'cash';
        $this->voucherCode = '';
        $this->discount = 0;
    }

    // Voucher Logic
    public function applyVoucher()
    {
        if (empty($this->voucherCode)) {
            Notification::make()->title('Kode Voucher Kosong')->warning()->send();
            return;
        }

        $voucher = \App\Models\Voucher::where('code', $this->voucherCode)
            ->where('is_active', true)
            ->first();

        if (!$voucher) {
            Notification::make()->title('Kode Voucher Tidak Valid')->danger()->send();
            $this->discount = 0;
            $this->calculateTotal();
            return;
        }

        if ($voucher->expired_at && $voucher->expired_at->isPast()) {
            Notification::make()->title('Voucher Kadaluarsa')->danger()->send();
            $this->discount = 0;
            $this->calculateTotal();
            return;
        }

        if ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit) {
            Notification::make()->title('Kuota Voucher Habis')->danger()->send();
            $this->discount = 0;
            $this->calculateTotal();
            return;
        }

        // Calculate initial total without discount
        $subtotal = array_reduce($this->cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['qty']);
        }, 0);

        if ($voucher->type === 'fixed') {
            $this->discount = $voucher->amount;
        } else {
            $this->discount = ($subtotal * $voucher->amount) / 100;
        }

        // Cap discount at subtotal
        $this->discount = min($this->discount, $subtotal);
        
        $this->calculateTotal();
        Notification::make()->title('Voucher Berhasil Digunakan')->success()->send();
    }
    
    public function removeVoucher()
    {
        $this->voucherCode = '';
        $this->discount = 0;
        $this->calculateTotal();
        Notification::make()->title('Voucher Dihapus')->info()->send();
    }

    public function calculateTotal()
    {
        $subtotal = array_reduce($this->cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['qty']);
        }, 0);
        
        // Recalculate discount if percentage based on new subtotal, or just ensure it doesn't exceed
        if ($this->voucherCode) {
             $voucher = \App\Models\Voucher::where('code', $this->voucherCode)->first();
             if ($voucher) {
                 if ($voucher->type === 'percent') {
                     $this->discount = ($subtotal * $voucher->amount) / 100;
                 }
                 $this->discount = min($this->discount, $subtotal);
             } else {
                 $this->discount = 0; 
             }
        } else {
            $this->discount = 0;
        }
        
        $this->total = $subtotal - $this->discount;
        $this->calculateChange();
    }

    // Notification Polling (Updated to cover all NEW orders)
    public function checkNewOrders()
    {
        // 1. Update the count badges for all tabs
        $this->newOrdersCount = Order::where('status', 'new')->count();
        $this->processingOrdersCount = Order::where('status', 'processing')->count();
        $this->readyOrdersCount = Order::where('status', 'ready')->count();

        // 2. Check for recent orders to trigger toast/sound
        $latestOrder = Order::where('status', 'new') 
            ->where('created_at', '>=', now()->subMinutes(2)) // Look back 2 mins
            // ->where('user_id', '!=', auth()->id()) // Exclude own orders - REMOVED for testing/single-user shops
            ->latest()
            ->first();

        if ($latestOrder) {
             $lastNotifiedId = session('last_notified_order_id', 0);
             
             if ($latestOrder->id > $lastNotifiedId) {
                Notification::make()
                   ->title('Pesanan Baru Masuk!')
                   ->body("Pesanan #{$latestOrder->id} - Rp " . number_format($latestOrder->grand_total, 0, ',', '.'))
                   ->actions([
                       \Filament\Actions\Action::make('view')
                           ->label('Lihat Detail')
                           ->button()
                           ->dispatch('trigger-order-detail', ['orderId' => $latestOrder->id]) // Dispatch event to load specific order
                           ->close(),
                   ])
                   ->success()
                   ->persistent() 
                   ->send();
                 
                 $this->dispatch('play-sound', status: 'success');
                 
                 session(['last_notified_order_id' => $latestOrder->id]);
             }
        }
    }

    public $activeTab = 'new'; // 'new', 'processing', 'ready'

    public function processOrder($orderId, $action = 'process')
    {
        $order = Order::find($orderId);
        if (!$order) return;

        if ($action === 'print') {
            $this->dispatch('print-receipt', ['orderId' => $order->id]);
            return;
        }

        if ($action === 'mark_paid') {
            $order->update(['payment_status' => 'paid']);
            Notification::make()->title('Pembayaran Dikonfirmasi')->success()->send();
            return; // Stay on same tab
        }

        if ($action === 'process') {
            $order->update(['status' => 'processing']);
            Notification::make()->title('Pesanan Diproses')->success()->send();
        } 
        elseif ($action === 'ready') {
            $order->update(['status' => 'ready']); // Ensure 'ready' status exists in enum/db or handle as needed
             Notification::make()->title('Pesanan Siap Diambil')->success()->send();
        }
        elseif ($action === 'complete') {
            $order->update(['status' => 'delivered']);
            Notification::make()->title('Pesanan Selesai')->success()->send();
        }

        // Re-check to update count
        $this->checkNewOrders();
    }

    public function getOrdersByTabProperty()
    {
        return Order::with(['items.product'])
            ->where('status', $this->activeTab)
            // ->where('user_id', '!=', auth()->id()) // Exclude own orders - REMOVED
            ->latest()
            ->get();
    }

    public $selectedOrder = null;

    public function loadOrderDetail($orderId)
    {
        $this->selectedOrder = Order::with('items.product')->find($orderId);
        if ($this->selectedOrder) {
            $this->dispatch('open-order-detail-modal');
        }
    }



    public function create()
    {
        // ...
    }

    public function calculateChange()
    {
        if ($this->receivedAmount >= $this->total) {
            $this->change = $this->receivedAmount - $this->total;
        } else {
            $this->change = 0;
        }
    }

    public function updatedReceivedAmount()
    {
        $this->calculateChange();
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            Notification::make()
                ->title('Keranjang Kosong')
                ->warning()
                ->send();
            return;
        }

        if ($this->receivedAmount < $this->total) {
            Notification::make()
                ->title('Pembayaran Kurang')
                ->body('Jumlah uang yang diterima kurang dari total belanja.')
                ->danger()
                ->send();
            return;
        }

        DB::beginTransaction();

        try {
            $voucherCodeToSave = null;
            
            // Re-validate voucher before saving
            if ($this->voucherCode) {
                 $voucher = \App\Models\Voucher::where('code', $this->voucherCode)->where('is_active', true)->first();
                 if ($voucher) {
                     $voucher->increment('used_count');
                     $voucherCodeToSave = $voucher->code;
                 }
            }

            $order = Order::create([
                'user_id' => auth()->id(), // Assuming logged in admin
                'grand_total' => $this->total,
                'discount_amount' => $this->discount,
                'voucher_code' => $voucherCodeToSave,
                'payment_method' => $this->paymentMethod,
                'payment_status' => 'paid',
                'status' => 'delivered', // Changed from 'completed' to 'delivered'
                'currency' => 'IDR',
                'shipping_amount' => 0,
                'shipping_method' => 'pickup',
            ]);

            foreach ($this->cart as $item) {
                $product = Product::lockForUpdate()->find($item['id']);
                
                if ($product->stock < $item['qty']) {
                    throw new \Exception("Stok untuk {$product->name} tidak mencukupi.");
                }

                $product->decrement('stock', $item['qty']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'unit_amount' => $item['price'],
                    'total_amount' => $item['price'] * $item['qty'],
                ]);
            }

            DB::commit();

            Notification::make()
                ->title('Transaksi Berhasil')
                ->success()
                ->send();

            // Prepare for printing and show success modal
             $this->dispatch('print-receipt', ['orderId' => $order->id]);
             $this->dispatch('play-sound', status: 'checkout');
             $this->dispatch('order-completed', orderId: $order->id, changeAmount: $this->change); // Trigger success modal with data

             // DO NOT reset cart here anymore, let the user do it explicitly via "New Order" button

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('POS Transaction Failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            Notification::make()
                ->title('Gagal Melakukan Transaksi')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    public $recentOrdersPerPage = 10;

    public function loadMoreRecentOrders()
    {
        $this->recentOrdersPerPage += 10;
    }

    public function getRecentOrdersProperty()
    {
        return Order::where('user_id', auth()->id())
            ->where('status', 'delivered') // Only show completed POS orders
            ->latest()
            ->paginate($this->recentOrdersPerPage);
    }

    public $productsPerPage = 12;

    public function loadMoreProducts()
    {
        $this->productsPerPage += 12;
    }

    public function getProductsProperty()
    {
        return Product::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->selectedCategory !== 'all', fn($q) => $q->whereHas('category', fn($c) => $c->where('slug', $this->selectedCategory)))
            ->where('is_active', true)
            ->latest()
            ->paginate($this->productsPerPage);
    }

    public function getViewData(): array
    {
        return [
            'products' => $this->products,
            'categories' => \App\Models\Category::where('is_active', true)->get(),
        ];
    }
}
