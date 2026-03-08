<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Order ID')
                    ->searchable(true, null, true)
                    ->sortable()
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->prefix('#'),

                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->customer_email),

                TextColumn::make('grand_total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->alignment('right')
                    ->weight('bold'),

                TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'Cash',
                        'qris' => 'QRIS',
                        'transfer' => 'Transfer',
                        'debit' => 'Debit',
                        'cash_on_delivery' => 'COD',
                        'bank_transfer' => 'BT',
                        'e_wallet' => 'E-Wallet',
                        default => $state,
                    })
                    ->searchable(),

                TextColumn::make('payment_status')
                    ->label('Pay Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'waiting_dp' => 'info',
                        'paid' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'info',
                        'processing' => 'warning',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('settlement_id')
                    ->label('Settle ID')
                    ->sortable()
                    ->toggleable(true, true),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->deferLoading()
            ->paginated([50, 100, 200, 500, 'all'])
            ->defaultPaginationPageOption(50)
            ->extremePaginationLinks()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->persistColumnSearchesInSession()
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'waiting_dp' => 'Menunggu DP',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ]),
                TernaryFilter::make('settled')
                    ->label('Settlement Status')
                    ->placeholder('All Orders')
                    ->trueLabel('Settled Only')
                    ->falseLabel('Unsettled Only')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('settlement_id'),
                        false: fn ($query) => $query->whereNull('settlement_id'),
                    ),
                \Filament\Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('From Date'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('To Date'),
                    ])
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (\Illuminate\Database\Eloquent\Builder $query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('print_receipt')
                    ->label('Struk')
                    ->icon('heroicon-o-printer')
                    ->color('info')
                    ->url(fn ($record) => route('pos.print', $record->id))
                    ->openUrlInNewTab(),
                Action::make('whatsapp')
                    ->label('WA')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn ($record) => 'https://wa.me/'.preg_replace('/[^0-9]/', '', $record->customer_phone).'?text='.urlencode("Halo {$record->customer_name}, kami dari ".config('app.name')." ingin mengonfirmasi pesanan #{$record->id} Anda."))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
