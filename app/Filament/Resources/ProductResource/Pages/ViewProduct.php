<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Mail\Payment as ProductMail;
use App\Models\Delivery;
use App\Models\Product;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserNotification;
use App\Services\FirebaseService;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Filament\Forms\Components\FileUpload;

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

            Action::make("markDeliveryAsCompleted")
                ->visible(fn (Product $record) => $record->delivery_id !== null)
                ->color('success')
                ->requiresConfirmation()
                ->form([
                    FileUpload::make('proof')
                        ->label('Proof')
                        //accept only images
                        ->image()
                        ->multiple()
                        ->directory('delivery')
                        ->required(),
                ])
                ->action(function (Product $record, array $data) {
                    //update delivery status to completed
                    $delivery = Delivery::where('product_id', $record->id)->first();
                    $delivery->update([
                        'status' => config('status.delivery_owner_status.Completed'), //update delivery status
                        'proof' => $data['proof']
                    ]);
                    try {
                        $user = User::find($record->user_id);
                        $device = UserDevice::where('user_id', $record->user_id)->first();
                        $message = 'Your product  delivery has been completed';
                        $message .= ' Product Name:' . $record->name;
                        $message .= ' You can check the application for more details';
                        Mail::to($user->email)->send(new ProductMail($user, $message, 'Product Delivery Completed'));
                        if ($device) {
                            $firebaseService = new FirebaseService();
                            $firebaseService->sendToDevice($device->push_token, 'Product Delivery  Completed', $message);
                        }
                        UserNotification::create([
                            'user_id' => $record->user_id,
                            'title' => "Product $record->name Delivery Completed",
                            'message' => 'Your product  delivery has been completed',
                            'type' => 'Product Delivery Completed',
                        ]);

                        //if the product is for the community update the community
                        if ($record->community_id) {

                            $community = User::find($record->community_id);
                            $device = UserDevice::where('user_id', $community->id)->first();
                            $message = 'Your product  delivERY has been completed';
                            $message .= 'Product Name:' . $record->name;
                            $message .= 'You can check the application for more details';
                            Mail::to($community->email)->send(new ProductMail($community, $message, 'Product Delivery Completed'));
                            if ($device) {
                                $firebaseService = new FirebaseService();
                                $firebaseService->sendToDevice($device->push_token, 'Product Delivery  Completed', $message);
                            }
                            UserNotification::create([
                                'user_id' => $community->id,
                                'title' => "Product $record->name Delivery Completed",
                                'message' => 'Your product  delivery has been completed',
                                'type' => 'Product Delivery Completed',
                            ]);
                        }
                    } catch (Throwable $th) {
                        //throw $th;
                        Log::error($th);
                    }
                    Notification::make()
                        ->success()
                        ->title('Delivery Completed')
                        ->body('The product delivery has been completed')
                        ->send();
                }),

            Action::make('Add Delivery Details')
                ->visible(fn (Product $record) => $record->status === config('status.product_status.Accepted'))
                ->requiresConfirmation()
                ->color('success')
                ->form([
                    DateTimePicker::make('pickup_date')
                        ->required()
                        ->label('Pickup Date'),
                    DateTimePicker::make('delivery_date')
                        ->required()
                        ->label('Delivery Date'),
                    TextInput::make('description')
                        ->label('Description')
                        ->required()
                ])
                ->action(function (Product $record, array $data) {

                    //create or update delivery details
                    $res = Delivery::updateOrCreate(
                        [
                            'product_id' => $record->id,
                        ],
                        [
                            'name' => "$record->name Delivery",
                            'pickup_date' => $data['pickup_date'],
                            'delivery_date' => $data['delivery_date'],
                            'owner_status' => config('status.delivery_owner_status.Pending'),
                            'user_id' => $record->user_id,
                            'category_id' => $record->category_id,
                            'product_id' => $record->id,
                            'status' => config('status.delivery_status.Pending'),
                            'description' => $data['description'],
                            'slug' => "#delivery"
                        ]
                    );
                    //update the product delivery id
                    $record->update([
                        'delivery_id' => $res->id,
                    ]);
                    try {
                        $user = User::find($record->user_id);
                        $device = UserDevice::where('user_id', $record->user_id)->first();
                        $message = 'Your product  delivery details have been updated';
                        $message .= ' Product Name:' . $record->name;
                        $message .= ' You can check the application for more details';
                        Mail::to($user->email)->send(new ProductMail($user, $message, 'Product Delivery Details Updated'));
                        if ($device) {
                            $firebaseService = new FirebaseService();
                            $firebaseService->sendToDevice($device->push_token, 'Product Delivery Updated', $message);
                        }
                        UserNotification::create([
                            'user_id' => $record->user_id,
                            'title' => "Product $record->name Delivery Details Updated",
                            'message' => 'Your product  delivery details have been updated',
                            'type' => 'Product Delivery Details Updated',
                        ]);

                        //if the product is for the community update the community
                        if ($record->community_id) {

                            $community = User::find($record->community_id);
                            $device = UserDevice::where('user_id', $community->id)->first();
                            $message = 'Your product  delivery details have been updated';
                            $message .= 'Product Name:' . $record->name;
                            $message .= 'You can check the application for more details';
                            Mail::to($community->email)->send(new ProductMail($community, $message, 'Product Delivery Details Updated'));
                            if ($device) {
                                $firebaseService = new FirebaseService();
                                $firebaseService->sendToDevice($device->push_token, 'Product Delivery Details Updated', $message);
                            }
                            UserNotification::create([
                                'user_id' => $community->id,
                                'title' => "Product $record->name Delivery Details Updated",
                                'message' => 'Your product  delivery details have been updated',
                                'type' => 'Product Delivery Details Updated',
                            ]);
                        }
                    } catch (Throwable $th) {
                        //throw $th;
                        Log::error($th);
                    }

                    Notification::make()
                        ->title('Delivery Details Updated')
                        ->success()
                        ->send();
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
                        $message = 'Your product has been accepted successfully.Total Amount: ' . $data['amount'];
                        $message .= 'Reason: ' . $data['reason'];
                        $message .= 'Product Name: ' . $record->name;
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
                        $message = 'Your product has been rejected.Total Amount: ' . $data['amount'];
                        $message .= 'Reason: ' . $data['reason'];
                        $message .= 'Product Name: ' . $record->name;
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
