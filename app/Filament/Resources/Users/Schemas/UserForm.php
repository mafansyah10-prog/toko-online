<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                TextInput::make('pos_pin')
                    ->label('POS PIN')
                    ->password()
                    ->revealable()
                    ->maxLength(6)
                    ->minLength(6)
                    ->numeric()
                    ->helperText('PIN tepat 6 digit untuk login ke sistem POS.'),
                \Filament\Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Job Desc (Role)')
                    ->preload()
                    ->searchable(),
            ]);
    }
}
