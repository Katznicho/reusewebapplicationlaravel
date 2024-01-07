<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Mail\Payment as ProductMail;
use App\Models\Product;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserNotification;
use App\Services\FirebaseService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),

            Action::make('View Images')
                ->action(function (Product $record) {
                    return redirect()->route('filament.admin.resources.products.view-images', $record->id);
                }),

            Action::make('Add Delivery')
                ->visible(fn (Product $record) => $record->payment_id === null | $record->payment_id === null)
                ->form([
                    TextInput::make('delivery_address')
                        ->required()
                        ->label('Delivery Address')
                        ->maxLength(255),
                    TextInput::make('delivery_date')
                        ->required()
                        ->label('Delivery Date')
                        ->maxLength(255),

                ])
                ->action(function (Product $record, array $data) {
                    //update product
                    // $record->update([
                    //     'delivery_address' => $data['delivery_address'],
                    //     'delivery_date' => $data['delivery_date'],
                    // ]);
                    try {
                        $user = User::find($record->user_id);
                        $device = UserDevice::where('user_id', $record->user_id)->first();
                        $message = 'Your product has been accepted successfully.<br/>Total Amount: '.$data['amount'];
                        $message .= '<br/>Reason: '.$data['reason'];
                        $message .= '<br/>Product Name: '.$record->name;
                        $message .= '<br/>You can check the application for more details';
                        Mail::to($user->email)->send(new ProductMail($user, $message, 'Product Accepted'));
                        if ($device) {
                            $firebaseService = new FirebaseService();
                            $firebaseService->sendToDevice($device->push_token, 'Product Accepted', $message);
                        }
                    } catch (Throwable $th) {
                        //throw $th;
                        Log::error($th);
                    }
                }),

            Action::make('AcceptProduct')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (Product $record) => $record->status === config('status.product_status.Pending'))
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
                    //update product
                    $record->update([
                        'status' => config('status.product_status.Accepted'),
                        'reason' => $data['reason'],
                        'total_amount' => $data['amount'],
                        'is_product_accepted' => true,
                        'is_product_rejected' => false,
                        'available' => $record->is_product_available_for_all ? true : false,
                    ]);
                    //create a user notification
                    UserNotification::create([
                        'user_id' => $record->user_id,
                        'title' => "Product $record->name Accepted",
                        'message' => $data['reason'],
                        'type' => 'Product Accepted',
                    ]);
                    try {
                        $user = User::find($record->user_id);
                        $device = UserDevice::where('user_id', $record->user_id)->first();
                        $message = 'Your product has been accepted successfully.Total Amount: '.$data['amount'];
                        $message .= 'Reason: '.$data['reason'];
                        $message .= 'Product Name: '.$record->name;
                        $message .= 'You can check the application for more details';
                        Mail::to($user->email)->send(new ProductMail($user, $message, 'Product Accepted'));
                        if ($device) {
                            $firebaseService = new FirebaseService();
                            $firebaseService->sendToDevice($device->push_token, "Product $record->name Accepted", $message);
                        }
                    } catch (Throwable $th) {
                        //throw $th;
                        Log::error($th);
                    }
                    Notification::make()
                        ->title('Product Accepted')
                        ->success()
                        ->send();
                }),

            Action::make('RejectProduct')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (Product $record) => $record->status === config('status.product_status.Pending'))
                ->form([
                    TextInput::make('reason')
                        ->required()
                        ->label('Reason')
                        ->maxLength(255),
                ])
                ->action(function (Product $record, array $data) {
                    $record->update([
                        'status' => config('status.product_status.Rejected'),
                        'reason' => $data['reason'],
                        'is_product_accepted' => false,
                        'is_product_rejected' => true,
                    ]);
                    //create a user notification
                    UserNotification::create([
                        'user_id' => $record->user_id,
                        'title' => 'Product Rejected',
                        'message' => $data['reason'],
                        'type' => 'Product Rejected',
                    ]);
                    try {
                        $user = User::find($record->user_id);
                        $message = 'Your product has been rejected.Total Amount: '.$data['amount'];
                        $message .= 'Reason: '.$data['reason'];
                        $message .= 'Product Name: '.$record->name;
                        $message .= 'You can check the application for more details';
                        Mail::to($user->email)->send(new ProductMail($user, $message, "Product $record->name Rejected"));
                        $device = UserDevice::where('user_id', $record->user_id)->first();
                        if ($device) {
                            $firebaseService = new FirebaseService();
                            $firebaseService->sendToDevice($device->push_token, 'Product Accepted', $message);
                        }
                    } catch (Throwable $th) {
                        // throw $th;
                        Log::error($th);
                    }
                    Notification::make()
                        ->title('Product Rejected')
                        ->success()
                        ->send();
                }),

        ];
    }
}
