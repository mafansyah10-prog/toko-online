<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use BackedEnum;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Details')
                    ->schema([
                        TextEntry::make('id')
                            ->label('Order ID'),
                        TextEntry::make('customer_name')
                            ->label('Customer Name'),
                        TextEntry::make('customer_email')
                            ->label('Customer Email'),
                        TextEntry::make('customer_phone')
                            ->label('Customer Phone'),
                        TextEntry::make('grand_total')
                            ->money('IDR'),
                        TextEntry::make('payment_method'),
                        TextEntry::make('payment_status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'waiting_dp' => 'info',
                                'paid' => 'success',
                                'failed' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'new' => 'info',
                                'processing' => 'warning',
                                'shipped' => 'primary',
                                'delivered' => 'success',
                                'cancelled' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('customer_address')
                            ->label('Shipping Address')
                            ->columnSpanFull(),
                        TextEntry::make('customer_city')
                            ->label('City'),
                        TextEntry::make('customer_postal_code')
                            ->label('Postal Code'),
                        TextEntry::make('shipping_amount')
                            ->money('IDR'),
                        TextEntry::make('shipping_method'),
                        TextEntry::make('notes')
                            ->label('Log/Notes')
                            ->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(2),
                Section::make('Order Items')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label('Product'),
                                TextEntry::make('quantity'),
                                TextEntry::make('unit_amount')
                                    ->money('IDR'),
                                TextEntry::make('total_amount')
                                    ->money('IDR'),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'view' => ViewOrder::route('/{record}'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}

