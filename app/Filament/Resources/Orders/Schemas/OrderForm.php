<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pelanggan')
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('Nama Pelanggan')
                            ->disabled(),
                        TextInput::make('customer_email')
                            ->label('Email Pelanggan')
                            ->disabled(),
                        TextInput::make('customer_phone')
                            ->label('Telepon Pelanggan')
                            ->disabled(),
                        TextInput::make('user_id')
                            ->label('User ID')
                            ->numeric()
                            ->disabled(),
                        Textarea::make('customer_address')
                            ->label('Alamat Lengkap')
                            ->rows(2)
                            ->columnSpanFull(),
                        TextInput::make('customer_city')
                            ->label('Kota'),
                        TextInput::make('customer_postal_code')
                            ->label('Kode Pos'),
                        Textarea::make('notes')
                            ->label('Catatan (Log History)')
                            ->rows(2)
                            ->columnSpanFull()
                            ->disabled(),
                    ])
                    ->columns(2),

                Section::make('Status Pesanan')
                    ->schema([
                        Select::make('status')
                            ->label('Status Pesanan')
                            ->options([
                                'new' => 'Pesanan Baru',
                                'processing' => 'Diproses',
                                'shipped' => 'Dikirim',
                                'delivered' => 'Terkirim',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->required()
                            ->default('new')
                            ->native(false),
                        Select::make('payment_status')
                            ->label('Status Pembayaran')
                            ->options([
                                'pending' => 'Pending',
                                'waiting_dp' => 'Menunggu DP',
                                'paid' => 'Lunas',
                                'failed' => 'Gagal',
                            ])
                            ->default('pending')
                            ->native(false),
                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'cash' => 'Tunai',
                                'qris' => 'QRIS',
                                'transfer' => 'Transfer Bank',
                                'debit' => 'Kartu Debit',
                                'bank_transfer' => 'Bank Transfer (Web)',
                                'e_wallet' => 'E-Wallet',
                                'cash_on_delivery' => 'COD (Bayar di Tempat)',
                            ])
                            ->native(false),
                    ])
                    ->columns(3),

                Section::make('Informasi Pengiriman & Pembayaran')
                    ->schema([
                        TextInput::make('grand_total')
                            ->label('Total Pembayaran')
                            ->numeric()
                            ->prefix('IDR')
                            ->disabled(),
                        TextInput::make('shipping_amount')
                            ->label('Ongkos Kirim')
                            ->numeric()
                            ->prefix('IDR'),
                        TextInput::make('shipping_method')
                            ->label('Metode Pengiriman'),
                        TextInput::make('currency')
                            ->label('Mata Uang')
                            ->required()
                            ->default('IDR')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }
}
