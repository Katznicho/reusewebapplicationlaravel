<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeviceRelationManager extends RelationManager
{
    protected static string $relationship = 'device';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user_id')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->toggleable()
                    ->label("User"),
                Tables\Columns\TextColumn::make('device_id')
                    ->searchable()
                    ->toggleable()
                    ->label("Device Id")
                    ->sortable(),
                Tables\Columns\TextColumn::make('device_model')
                    ->searchable()
                    ->toggleable()
                    ->label("Device Model")
                    ->sortable(),
                Tables\Columns\TextColumn::make('device_manufacturer')
                    ->searchable()
                    ->toggleable()
                    ->label("Device Manufacturer")
                    ->sortable(),
                Tables\Columns\TextColumn::make('app_version')
                    ->searchable()
                    ->toggleable()
                    ->label("App Version")
                    ->sortable(),
                Tables\Columns\TextColumn::make('device_os')
                    ->searchable()
                    ->toggleable()
                    ->label("Device OS")
                    ->sortable(),
                Tables\Columns\TextColumn::make('device_os_version')
                    ->searchable()
                    ->toggleable()
                    ->label("Device OS Version")
                    ->sortable(),
                Tables\Columns\TextColumn::make('device_user_agent')
                    ->searchable()
                    ->toggleable()
                    ->label("User Agent")
                    ->sortable(),
                Tables\Columns\TextColumn::make('device_type')
                    ->searchable()
                    ->toggleable()
                    ->label("Device Type")
                    ->sortable(),
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
