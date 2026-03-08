<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::updated(function ($order) {
            if ($order->wasChanged('status') && $order->customer_email) {
                try {
                    \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderUpdatedMail($order));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send order status update email', ['order_id' => $order->id, 'error' => $e->getMessage()]);
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function settlement()
    {
        return $this->belongsTo(Settlement::class);
    }

    public function getPaymentMethodLabelAttribute()
    {
        return match ($this->payment_method) {
            'cash' => 'Tunai',
            'qris' => 'QRIS',
            'transfer' => 'Transfer Bank',
            'debit' => 'Kartu Debit',
            'e_wallet' => 'E-Wallet',
            'cash_on_delivery' => 'COD (Bayar di Tempat)',
            default => ucfirst($this->payment_method),
        };
    }
}
