<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VerificationResource\Pages;
use App\Models\Verification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VerificationResource extends Resource
{
    protected static ?string $model = Verification::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('front_national_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('back_national_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('passport')
                    ->label('Community Document')
                    ->maxLength(255),
                Forms\Components\TextInput::make('document_url')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->toggleable()
                    ->label('User'),
                // Tables\Columns\TextColumn::make('front_national_id')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('back_national_id')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('passport')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('document_url')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('status')
                //     ->searchable(),
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
                //Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListVerifications::route('/'),
            'create' => Pages\CreateVerification::route('/create'),
            'view' => Pages\ViewVerification::route('/{record}'),
            'view-donor-images' => Pages\ViewDonorImages::route('/{record}/view-donor-images'),

            // 'edit' => Pages\EditVerification::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
