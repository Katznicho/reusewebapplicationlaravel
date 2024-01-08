<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Products';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('cover_image')
                    ->image()
                    ->required(),
                Forms\Components\TextInput::make('images')
                    ->required(),
                Forms\Components\TextInput::make('pick_up_location')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('weight')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_delivery_available')
                    ->required(),
                Forms\Components\Toggle::make('is_delivery_fee_covered')
                    ->required(),
                Forms\Components\Toggle::make('is_delivery_set')
                    ->required(),
                Forms\Components\Toggle::make('is_donation')
                    ->required(),
                Forms\Components\Toggle::make('is_product_new')
                    ->required(),
                Forms\Components\Toggle::make('is_product_available_for_all')
                    ->required(),
                Forms\Components\Toggle::make('is_product_damaged')
                    ->required(),
                Forms\Components\Toggle::make('is_product_rejected')
                    ->required(),
                Forms\Components\Toggle::make('is_product_accepted')
                    ->required(),
                Forms\Components\TextInput::make('reason')
                    ->maxLength(255),
                Forms\Components\TextInput::make('damage_description')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_amount')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Cover Image')
                    ->circular(),
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable()
                    ->label('User')
                    ->color('success')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable()
                    ->searchable()
                    ->label('Category')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Product')
                    ->toggleable()
                    ->limit(50)
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50)
                    ->sortable()
                    ->label('Description')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('price')
                    ->searchable()
                    ->sortable()
                    ->label('Price')
                    ->toggleable()
                    ->money('UGX', true),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable()
                    ->color('danger')
                    ->label('Status')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->searchable()
                    ->sortable()
                    ->label('Total Amount')
                    ->toggleable()
                    ->money('UGX', true),
                Tables\Columns\TextColumn::make('pick_up_location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('weight')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_delivery_available')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_delivery_fee_covered')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_delivery_set')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_donation')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_product_new')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_product_available_for_all')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_product_damaged')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_product_rejected')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_product_accepted')
                    ->boolean(),
                Tables\Columns\TextColumn::make('reason')
                    ->searchable(),
                Tables\Columns\TextColumn::make('damage_description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total_amount')
                    ->searchable(),
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
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',

                    ])
                    ->label('Status'),
                    
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators[] = Indicator::make('Created from ' . Carbon::parse($data['from'])->toFormattedDateString())
                                ->removeField('from');
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = Indicator::make('Created until ' . Carbon::parse($data['until'])->toFormattedDateString())
                                ->removeField('until');
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
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
            RelationManagers\PaymentRelationManager::class,
            RelationManagers\DeliveryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            // 'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            // 'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view-images' => Pages\ViewImages::route('/{record}/view-images'),
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
