<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserDeviceResource\Pages;
use App\Models\UserDevice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserDeviceResource extends Resource
{
    protected static ?string $model = UserDevice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('device_id')
                    ->maxLength(255),
                Forms\Components\TextInput::make('device_model')
                    ->maxLength(255),
                Forms\Components\TextInput::make('device_manufacturer')
                    ->maxLength(255),
                Forms\Components\TextInput::make('app_version')
                    ->maxLength(255),
                Forms\Components\TextInput::make('device_os')
                    ->maxLength(255),
                Forms\Components\TextInput::make('device_os_version')
                    ->maxLength(255),
                Forms\Components\TextInput::make('device_user_agent')
                    ->maxLength(255),
                Forms\Components\TextInput::make('device_type')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('device_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('device_model')
                    ->searchable(),
                Tables\Columns\TextColumn::make('device_manufacturer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('app_version')
                    ->searchable(),
                Tables\Columns\TextColumn::make('device_os')
                    ->searchable(),
                Tables\Columns\TextColumn::make('device_os_version')
                    ->searchable(),
                Tables\Columns\TextColumn::make('device_user_agent')
                    ->searchable(),
                Tables\Columns\TextColumn::make('device_type')
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
            'index' => Pages\ListUserDevices::route('/'),
            'create' => Pages\CreateUserDevice::route('/create'),
            'view' => Pages\ViewUserDevice::route('/{record}'),
            'edit' => Pages\EditUserDevice::route('/{record}/edit'),
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
