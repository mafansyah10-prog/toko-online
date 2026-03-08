<?php

namespace App\Filament\Resources\Settlements;

use App\Filament\Resources\Settlements\Pages\ListSettlements;
use App\Filament\Resources\Settlements\Pages\ViewSettlement;
use App\Models\Settlement;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SettlementResource extends Resource
{
    protected static ?string $model = Settlement::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Setelmen';

    protected static string|\UnitEnum|null $navigationGroup = 'Shop Management';

    protected static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereNull('closed_at')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->columns([
                TextColumn::make('user.name')
                    ->label('Kasir')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->state(fn ($record) => $record->closed_at ? 'Closed' : 'Open')
                    ->color(fn ($state) => $state === 'Open' ? 'success' : 'gray'),
                TextColumn::make('opened_at')
                    ->label('Dibuka')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('closed_at')
                    ->label('Ditutup')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Masih Terbuka'),
                TextColumn::make('total_sales')
                    ->label('Total Penjualan')
                    ->money('IDR')
                    ->sortable()
                    ->alignment('right'),
                TextColumn::make('actual_cash_amount')
                    ->label('Kas Aktual')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('diff')
                    ->label('Selisih')
                    ->money('IDR')
                    ->state(fn ($record) => $record->actual_cash_amount - $record->cash_sales)
                    ->color(fn ($state) => $state < 0 ? 'danger' : ($state > 0 ? 'warning' : 'success')),
            ])
            ->defaultSort('opened_at', 'desc')
            ->paginated([50, 100, 200, 500, 'all'])
            ->defaultPaginationPageOption(50)
            ->extremePaginationLinks()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->persistColumnSearchesInSession()
            ->filters([
                SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Kasir'),
                \Filament\Tables\Filters\Filter::make('status')
                    ->form([
                        \Filament\Forms\Components\Select::make('status')
                            ->label('Status Shift')
                            ->options([
                                'open' => 'Masih Terbuka',
                                'closed' => 'Sudah Ditutup',
                            ]),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['status'] === 'open', fn ($q) => $q->whereNull('closed_at'))
                            ->when($data['status'] === 'closed', fn ($q) => $q->whereNotNull('closed_at'));
                    }),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('print')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('pos.settlement.print', $record))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->closed_at !== null),
            ])
            ->bulkActions([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rincian Sesi')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('user.name')->label('Kasir'),
                        TextEntry::make('opened_at')->label('Waktu Buka')->dateTime(),
                        TextEntry::make('closed_at')->label('Waktu Tutup')->dateTime()->placeholder('Masih Terbuka'),

                        TextEntry::make('notes')
                            ->label('Catatan Shift')
                            ->columnSpanFull()
                            ->placeholder('Tidak ada catatan.'),
                    ]),

                Section::make('Ringkasan Penjualan')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('cash_sales')->label('Penjualan Tunai')->money('IDR'),
                        TextEntry::make('qris_sales')->label('Penjualan QRIS')->money('IDR'),
                        TextEntry::make('transfer_sales')->label('Penjualan Transfer')->money('IDR'),
                        TextEntry::make('debit_sales')->label('Penjualan Debit')->money('IDR'),
                        TextEntry::make('total_sales')
                            ->label('Total Omzet Sesi')
                            ->money('IDR')
                            ->color('primary')
                            ->columnSpanFull()
                            ->size('lg')
                            ->weight('bold'),
                    ]),

                Section::make('Audit Kas (Settlement)')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('cash_sales')->label('Ekspektasi Kas')->money('IDR'),
                        TextEntry::make('actual_cash_amount')->label('Kas Aktual (Diterima)')->money('IDR'),
                        TextEntry::make('difference')
                            ->label('Selisih Kas')
                            ->money('IDR')
                            ->state(fn ($record) => $record->actual_cash_amount - $record->cash_sales)
                            ->color(fn ($state) => $state < 0 ? 'danger' : ($state > 0 ? 'warning' : 'success'))
                            ->weight('bold'),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrdersRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\SettlementOverview::class,
            Widgets\SettlementCalendarWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSettlements::route('/'),
            'view' => ViewSettlement::route('/{record}'),
        ];
    }
}
