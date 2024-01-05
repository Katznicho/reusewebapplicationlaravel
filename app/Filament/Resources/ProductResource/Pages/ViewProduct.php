<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use App\Models\UserNotification;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),

            Action::make('AcceptProduct')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (Product $record) => $record->status === config("status.product_status.Pending"))
                ->form([
                    TextInput::make('reason')
                        ->required()
                        ->label('Reason')
                        ->maxLength(255),
                    //total amount
                    TextInput::make('amount')
                        ->required()
                        ->label('Amount')
                        ->maxLength(255)
                        ->numeric()
                        ->prefix('UGX'),
                ])
                ->action(function (Product $record, array $data) {
                    $record->update([
                        'status' => config("status.product_status.Accepted"),
                        'reason' => $data['reason'],
                        'total_amount' => $data['amount'],
                        'is_product_accepted' => true,
                        'is_product_rejected' => false,
                    ]);
                    //create a user notification
                    UserNotification::create([
                        'user_id' => $record->user_id,
                        'title' => 'Product Accepted',
                        'message' => $data['reason'],
                        'type' => "Product Accepted",
                    ]);
                    Notification::make()
                        ->title('Product Accepted')
                        ->success()
                        ->send();
                }),

            Action::make('RejectProduct')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (Product $record) => $record->status === config("status.product_status.Pending"))
                ->form([
                    TextInput::make('reason')
                        ->required()
                        ->label('Reason')
                        ->maxLength(255),
                ])
                ->action(function (Product $record, array $data) {
                    $record->update([
                        'status' => config("status.product_status.Rejected"),
                        'reason' => $data['reason'],
                        'is_product_accepted' => false,
                        'is_product_rejected' => true,
                    ]);
                    //create a user notification
                    UserNotification::create([
                        'user_id' => $record->user_id,
                        'title' => 'Product Rejected',
                        'message' => $data['reason'],
                        'type' => "Product Rejected",
                    ]);
                    Notification::make()
                        ->title('Product Rejected')
                        ->success()
                        ->send();
                })



        ];
    }
}
