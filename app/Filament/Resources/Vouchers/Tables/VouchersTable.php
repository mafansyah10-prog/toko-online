<?php

namespace App\Filament\Resources\Vouchers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VouchersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'fixed' => 'info',
                        'percent' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'fixed' => 'Nominal',
                        'percent' => 'Percent',
                        default => $state,
                    }),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state, $record) => $record->type === 'percent' ? $state.'%' : 'Rp '.number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->alignment('right'),

                TextColumn::make('usage_limit')
                    ->label('Limit')
                    ->formatStateUsing(fn ($state) => $state ?? '∞')
                    ->sortable()
                    ->alignment('center'),

                TextColumn::make('used_count')
                    ->label('Used')
                    ->numeric()
                    ->sortable()
                    ->alignment('center'),

                TextColumn::make('expired_at')
                    ->label('Expiry')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color(fn ($state) => $state && \Illuminate\Support\Carbon::parse($state)->isPast() ? 'danger' : null),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(true, true),
            ])
            ->deferLoading()
            ->paginated([50, 100, 200, 500, 'all'])
            ->defaultPaginationPageOption(50)
            ->extremePaginationLinks()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->persistColumnSearchesInSession()
            ->filters([
                \Filament\Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
                \Filament\Tables\Filters\Filter::make('expired')
                    ->label('Expired')
                    ->query(fn ($query) => $query->whereNotNull('expired_at')->where('expired_at', '<', now())),
                \Filament\Tables\Filters\Filter::make('percent_only')
                    ->label('Percent Only')
                    ->query(fn ($query) => $query->where('type', 'percent')),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
