<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(true, null, true),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(true, null, true),
                TextColumn::make('pos_pin')
                    ->label('POS PIN')
                    ->state(fn ($record) => $record->pos_pin ? 'Sudah Set' : 'Belum Set')
                    ->color(fn ($record) => $record->pos_pin ? 'success' : 'danger')
                    ->badge(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(true, true),
                TextColumn::make('updated_at')
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
                \Filament\Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable(),
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
