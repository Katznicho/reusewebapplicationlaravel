<?php

namespace App\Filament\Resources\DeliveryResource\Pages;

use App\Filament\Resources\DeliveryResource;
use App\Mail\Payment as ProductMail;
use App\Models\Delivery;
use App\Models\User;
use App\Models\UserNotification;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ViewDelivery extends ViewRecord
{
    protected static string $resource = DeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
            Action::make('ConfirmDelivery')
                ->color('success')
                ->visible(fn (Delivery $record) => $record->status === config('status.delivery_status.Pending'))
                //action update status to Confirmed
                ->action(function (Delivery $record) {
                    $record->status = config('status.delivery_status.Confirmed');
                    $record->save();
                    //create a user notification
                    UserNotification::create([
                        'user_id' => $record->user_id,
                        'title' => 'Delivery Confirmed',
                        'message' => 'Delivery has been confirmed',
                        'type' => 'Product Accepted',
                    ]);
                    try {
                        $user = User::find($record->user_id);
                        $message = 'Your product has been accepted';
                        $message .= '<br/>You can check the application for more details';
                        Mail::to($user->email)->send(new ProductMail($user, $message, 'Product Accepted'));
                    } catch (Throwable $th) {
                        // throw $th;
                        Log::error($th);
                    }
                    Notification::make()
                        ->title('Product Accepted')
                        ->success()
                        ->send();
                }),

            Action::make('RejectDelivery')
                ->color('danger')
                ->visible(fn (Delivery $record) => $record->status === config('status.delivery_status.Pending'))
                ->action(function (Delivery $record) {
                    $record->status = config('status.delivery_status.Rejected');
                    $record->save();
                    //create a user notification
                    UserNotification::create([
                        'user_id' => $record->user_id,
                        'title' => 'User Notification Title',
                        'message' => 'User Notification Message',
                        'type' => 'User Notification Type',
                    ]);
                    try {
                        $user = User::find($record->user_id);
                        $message = 'Your product has been rejected';
                        $message .= '<br/>You can check the application for more details';
                        Mail::to($user->email)->send(new ProductMail($user, $message, 'Product Rejected'));
                    } catch (Throwable $th) {
                        // throw $th;
                        Log::error($th);
                    }
                }),

            Action::make('markDeliveryAsDelivered')
                ->visible(fn (Delivery $record) => $record->status === config('status.delivery_status.Confirmed'))
                ->action(function (Delivery $record) {
                    $record->status = config('status.delivery_status.Delivered');
                    $record->save();
                    //create a user notification
                    UserNotification::create([
                        'user_id' => $record->user_id,
                        'title' => 'User Notification Title',
                        'message' => 'User Notification Message',
                        'type' => 'User Notification Type',
                    ]);
                    try {
                        $user = User::find($record->user_id);
                        $message = 'Your product has been rejected';
                        $message .= '<br/>You can check the application for more details';
                        Mail::to($user->email)->send(new ProductMail($user, $message, 'Product Rejected'));
                    } catch (Throwable $th) {
                        // throw $th;
                        Log::error($th);
                    }
                }),

        ];
    }
}
