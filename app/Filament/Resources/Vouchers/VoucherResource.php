<?php

namespace App\Filament\Resources\Vouchers;

use App\Filament\Resources\Vouchers\Pages\CreateVoucher;
use App\Filament\Resources\Vouchers\Pages\EditVoucher;
use App\Filament\Resources\Vouchers\Pages\ListVouchers;
use App\Filament\Resources\Vouchers\Schemas\VoucherForm;
use App\Filament\Resources\Vouchers\Tables\VouchersTable;
use App\Models\Voucher;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'code';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('Kode Voucher'),
                \Filament\Forms\Components\Select::make('type')
                    ->options([
                        'fixed' => 'Nominal Tetap (Rp)',
                        'percent' => 'Persentase (%)',
                    ])
                    ->required()
                    ->default('fixed')
                    ->label('Tipe Potongan'),
                \Filament\Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->label('Jumlah Potongan'),
                \Filament\Forms\Components\TextInput::make('usage_limit')
                    ->numeric()
                    ->label('Batas Penggunaan (Kosongkan jika tidak terbatas)'),
                \Filament\Forms\Components\DateTimePicker::make('expired_at')
                    ->label('Tanggal Kadaluarsa'),
                \Filament\Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->required()
                    ->label('Aktif'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->label('Kode'),
                \Filament\Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'fixed' => 'Nominal Tetap',
                        'percent' => 'Persentase',
                    }),
                \Filament\Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->formatStateUsing(fn ($state, $record) => $record->type === 'percent' ? $state . '%' : 'Rp ' . number_format($state, 0, ',', '.')),
                \Filament\Tables\Columns\TextColumn::make('usage_limit')
                    ->label('Batas')
                    ->formatStateUsing(fn ($state) => $state ?? 'Unlimited'),
                \Filament\Tables\Columns\TextColumn::make('used_count')
                    ->label('Terpakai'),
                \Filament\Tables\Columns\TextColumn::make('expired_at')
                    ->dateTime()
                    ->label('Kadaluarsa'),
                \Filament\Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Aktif'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVouchers::route('/'),
            'create' => CreateVoucher::route('/create'),
            'edit' => EditVoucher::route('/{record}/edit'),
        ];
    }
}
