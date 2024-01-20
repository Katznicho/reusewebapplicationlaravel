<?php

namespace App\Filament\Resources\VerificationResource\Pages;

use App\Filament\Resources\VerificationResource;
use App\Mail\Payment as VerificationMail;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserNotification;
use App\Models\Verification;
use App\Services\FirebaseService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Mail;

class ViewVerification extends ViewRecord
{
    protected static string $resource = VerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
            Action::make('Agreement')
                // ->icon("heroicon-o-document-download")
                ->url(fn (Verification $record) => route('download-document', $record))
                ->openUrlInNewTab()
                ->color('success'),

            Action::make('CommunityDocument')
                ->url(fn (Verification $record) => route('view-document', $record))
                ->visible(function (Verification $record) {
                    $user = User::find($record->user_id);
                    if ($user->role === config('user_roles.user_roles.RECEIVER')) {
                        return true;
                    } else {
                        return false;
                    }
                })
                ->openUrlInNewTab()
                ->color('success'),

            Action::make('DonorImages')
                ->visible(function (Verification $record) {
                    $user = User::find($record->user_id);
                    if ($user->role !== config('user_roles.user_roles.RECEIVER')) {
                        return true;
                    } else {
                        return false;
                    }
                })
                ->action(function (Verification $record) {
                    return redirect()->route('filament.admin.resources.verifications.view-donor-images', $record->id);
                })
                ->color('success'),

            Action::make('verifyUser')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->visible(fn (Verification $record) => $record->status === config('status.verification_status.Pending') || $record->status === config('status.verification_status.Rejected'))
                ->action(function (Verification $record) {
                    $record->update([
                        'status' => config('status.verification_status.Verified'),
                        'reason' => 'Your now a verified user',
                    ]);

                    $user = User::find($record->user_id);
                    $user->update([
                        'is_user_verified' => true,
                    ]);
                    $user = User::find($record->user_id);
                    $device = UserDevice::where('user_id', $record->user_id)->first();
                    $message = 'Your account has been verified';
                    try {
                        //code...
                        Mail::to($user->email)->send(new VerificationMail($user, $message, 'Verified Successfully'));
                        if ($device) {
                            $firebaseService = new FirebaseService();
                            $firebaseService->sendToDevice($device->push_token, 'Verified Successfully', $message);
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                    }

                    UserNotification::create([
                        'user_id' => $record->user_id,
                        'title' => 'Verified Successfully',
                        'message' => 'Your account has been verified',
                        'type' => 'User Verification',
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Verified Successfully')
                        ->body('The user has been verified successfully.')
                        ->send();
                }),

            Action::make('rejectUser')
                ->icon('heroicon-o-x-circle')
                ->visible(fn (Verification $record) => $record->status === config('status.verification_status.Pending') || $record->status === config('status.verification_status.Rejected'))
                ->requiresConfirmation()
                ->color('danger')
                ->form([
                    TextInput::make('reason'),
                ])
                ->action(function (Verification $record, array $data) {
                    $record->update([
                        'status' => config('status.delivery_owner_status.Rejected'),
                        'reason' => $data['reason'],
                    ]);
                    $user = User::find($record->user_id);
                    $user->update([
                        'is_user_verified' => true,
                    ]);
                    $user = User::find($record->user_id);
                    $device = UserDevice::where('user_id', $record->user_id)->first();
                    $message = 'Your account has been verified';
                    try {
                        //code...
                        Mail::to($user->email)->send(new VerificationMail($user, $message, 'Verification Rejected'));
                        if ($device) {
                            $firebaseService = new FirebaseService();
                            $firebaseService->sendToDevice($device->push_token, 'Verification Rejected', $message);
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                    }

                    UserNotification::create([
                        'user_id' => $record->user_id,
                        'title' => 'Verification Rejected',
                        'message' => 'Your account has been rejected',
                        'type' => 'User Verification',
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Verification Rejected')
                        ->body('The user has been rejected successfully.')
                        ->send();
                }),
        ];
    }
}
