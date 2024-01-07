<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentRelationManager extends RelationManager
{
    protected static string $relationship = 'payment';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('payment_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('payment_id')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->label('User Name'),
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->sortable()
                    ->label('Product Name'),
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->label('Type'),
                Tables\Columns\TextColumn::make('amount')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->money('UGX', true)
                    ->label('Amount'),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->sortable()
                    ->label('Phone Number'),
                Tables\Columns\TextColumn::make('payment_mode')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->sortable()
                    ->label('Payment Mode'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->sortable()
                    ->label('Payment Method'),
                Tables\Columns\IconColumn::make('is_annyomous')
                    ->boolean(),
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->sortable()
                    ->label('Reference'),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->sortable()
                    ->label('Status'),
                Tables\Columns\TextColumn::make('order_tracking_id')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->sortable()
                    ->label('Tracking Id'),
                Tables\Columns\TextColumn::make('OrderNotificationType')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
