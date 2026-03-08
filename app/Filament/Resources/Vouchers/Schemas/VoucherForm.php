<?php

namespace App\Filament\Resources\Vouchers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Voucher Information')
                    ->description('General details about the voucher')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('code')
                                ->label('Voucher Code')
                                ->required()
                                ->unique(null, null, null, true)
                                ->maxLength(255)
                                ->placeholder('e.g. DISKON50'),

                            Toggle::make('is_active')
                                ->label('Active Status')
                                ->default(true)
                                ->inline(false),
                        ]),
                    ]),

                Section::make('Discount Settings')
                    ->description('Set the discount type and amount')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('type')
                                ->label('Type')
                                ->options([
                                    'fixed' => 'Fixed Amount (Rp)',
                                    'percent' => 'Percentage (%)',
                                ])
                                ->required()
                                ->default('fixed')
                                ->live(),

                            TextInput::make('amount')
                                ->label(fn (callable $get) => $get('type') === 'percent' ? 'Discount Percentage' : 'Discount Amount')
                                ->prefix(fn (callable $get) => $get('type') === 'percent' ? null : 'Rp')
                                ->suffix(fn (callable $get) => $get('type') === 'percent' ? '%' : null)
                                ->required()
                                ->numeric(),

                            TextInput::make('usage_limit')
                                ->label('Max Usage')
                                ->helperText('Leave empty for unlimited')
                                ->numeric(),
                        ]),
                    ]),

                Section::make('Expiration')
                    ->description('Set when the voucher expires')
                    ->schema([
                        DateTimePicker::make('expired_at')
                            ->label('Expiry Date')
                            ->helperText('Leave empty for no expiration'),
                    ]),
            ]);
    }
}
