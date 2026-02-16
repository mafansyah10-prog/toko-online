<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pelanggan')
                    ->schema([
                        TextInput::make('user_id')
                            ->label('User ID')
                            ->numeric()
                            ->disabled(),
                        Textarea::make('notes')
                            ->label('Catatan / Info Pelanggan')
                            ->rows(3)
                            ->columnSpanFull(),
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
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                            ])
                            ->default('pending')
                            ->native(false),
                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'cod' => 'Cash on Delivery (COD)',
                                'bank_transfer' => 'Bank Transfer',
                                'e_wallet' => 'E-Wallet',
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
