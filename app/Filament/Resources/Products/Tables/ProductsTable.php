<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images')
                    ->label('Image')
                    ->circular()
                    ->stacked()
                    ->limit(1)
                    ->size(40),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->weight('bold'),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('price')
                    ->money('IDR')
                    ->sortable()
                    ->alignment('right'),

                TextColumn::make('stock')
                    ->numeric()
                    ->sortable()
                    ->alignment('center')
                    ->color(fn ($state) => $state <= 5 ? 'danger' : ($state <= 20 ? 'warning' : 'success'))
                    ->weight('bold'),

                ToggleColumn::make('is_active')
                    ->label('Active'),

                ToggleColumn::make('is_featured')
                    ->label('Featured'),

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
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
                \Filament\Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
                \Filament\Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured Status'),
                \Filament\Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock')
                    ->query(fn ($query) => $query->where('stock', '<=', 5)),
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
