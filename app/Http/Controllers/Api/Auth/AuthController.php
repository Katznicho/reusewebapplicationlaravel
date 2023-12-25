<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RequestPasswordReset;
use App\Mail\UserVerification;
use App\Mail\WalletActivated;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserDevice;
use App\Models\UserLocation;
use App\Traits\MessageTrait;
use App\Traits\SendPushNotification;
use App\Traits\UserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AuthController extends Controller
{
    use MessageTrait, UserTrait;
    //

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'phone_number' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|string|same:password',
            'role' => 'required',
        ]);

        // Generate a random OTP code
        // $otpCode = 123456;
        $otpCode = random_int(100000, 999999);

        // Create a new user
        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'phone_number' => $this->formatMobileInternational($request->phone_number) ?? $request->phone_number,
            'otp' => Hash::make($otpCode),
            'otp_send_time' => now(),
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Create an auth token for the user
        $authToken = $user->createToken('authToken')->plainTextToken;



        try {
            // Send the OTP code to the user's email
            Mail::to($user->email)->send(new UserVerification($user, $otpCode));
        } catch (\Throwable $th) {
            // throw $th;
        }

        return response()->json([
            'response' => 'success',
            'message' => 'Successfully created user!',
            'user' => $user,
            'authToken' => $authToken,
        ], 201);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
            'otp' => 'required|size:6'
        ]);

        // Find the user
        $user = User::where('email', $request->email)->first();

        // Check if the OTP code is correct
        if (!Hash::check($request->otp, $user->otp)) {
            return response()->json([
                'response' => 'failure',
                'errors' => [
                    'otp' => ['Incorrect OTP. Check your email for OTP sent to you']
                ],
                'message' => 'Incorrect OTP. Check your email for OTP sent to you'
            ], 401);
        }

        // Check if the OTP code is expired
        if (now()->diffInMinutes($user->otp_send_time) > 5) {
            return response()->json([
                'response' => 'failure',
                'errors' => [
                    'otp' => ['OTP code expired']
                ],
                'message' => 'OTP code expired'
            ], 401);
        }

        // Update the user's email verification status
        $user->update([
            'otp' => null,
            'otp_send_time' => null,
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        return response()->json([
            'response' => 'success',
            'message' => 'Successfully verified email!',
            'user' => $user
        ], 200);
    }


    // 
    // Resend OTP in case user didn't receive or it expired
    public function resendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
        ]);

        // Generate a random OTP code
        $otpCode = random_int(100000, 999999);

        // Get the user
        $user = User::where('email', $request->email)->first();

        // Update the user's OTP code and OTP send time
        $user->update([
            'otp' => Hash::make($otpCode),
            'otp_send_time' => now(),
        ]);

        try {
            // Send the OTP code to the user's email
            Mail::to($user->email)->send(new UserVerification($user, $otpCode,));
        } catch (\Throwable $th) {
            throw $th;
        }

        return response()->json([
            'response' => 'success',
            'message' => 'OTP resent successfully',
        ], 201);
    }


    // 
    // Login a user
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        // Find the user
        $user = User::where('email', $request->email)->first();

        // Check if the user exists and the password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'response' => 'failure',
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Create an auth token for the user
        $authToken = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'response' => 'success',
            'message' => 'Successfully logged in!',
            'user' => $user,
            'authToken' => $authToken
        ], 200);
    }

    public function logout(Request $request)
    {
        // Delete the user's current auth token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'response' => 'success',
            'message' => 'Successfully logged out!'
        ], 200);
    }


    // 
    // Change a user's password
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        // Find the user
        $user = User::where('email', $request->user()->email)->first();

        // Check if the user exists and the password is correct
        if (!$user || !Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'response' => 'failure',
                'message' => 'Invalid credentials',
                'errors' => [
                    'old_password' => ['Invalid credentials']
                ]
            ], 401);
        }

        // Check if the new password is the same as the old password
        if ($request->old_password == $request->new_password) {
            return response()->json([
                'response' => 'failure',
                'message' => 'New password cannot be the same as the old password',
                'errors' => [
                    'new_password' => ['New password cannot be the same as the old password']
                ]
            ], 401);
        }

        // Change the user's password
        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json([
            'response' => 'success',
            'message' => 'Successfully changed password!',
            'trainer' => $user->trainer,
        ], 200);
    }



    // Request a password reset
    public function requestPasswordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
        ]);

        // Generate a random OTP code
        $otpCode = random_int(100000, 999999);

        // Get the user
        $user = User::where('email', $request->email)->first();

        // Update the user's OTP code and OTP send time
        $user->update([
            'otp' => Hash::make($otpCode),
            'otp_send_time' => now(),
        ]);

        try {
            // Send the OTP code to the user's email
            Mail::to($user->email)->send(new RequestPasswordReset($user, $otpCode));
        } catch (\Throwable $th) {
            throw $th;
        }

        return response()->json([
            'response' => 'success',
            'message' => 'OTP sent successfully',
        ], 201);
    }


    // 
    // Reset a user's password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
            'otp' => 'required|size:6',
            'new_password' => 'required|string|min:6',
            'confirm_new_password' => 'required|string|same:new_password',
        ]);

        // Find the user
        $user = User::where('email', $request->email)->first();

        // Check if the OTP code is correct
        if (!Hash::check($request->otp, $user->otp)) {
            return response()->json([
                'response' => 'failure',
                'message' => 'Incorrect OTP. Check your email for OTP sent to you',
                'errors' => [
                    'otp' => ['Incorrect OTP. Check your email for OTP sent to you']
                ]
            ], 401);
        }

        // Check if the OTP code is expired
        if (now()->diffInMinutes($user->otp_send_time) > 5) {
            return response()->json([
                'response' => 'failure',
                'message' => 'OTP code expired',
                'errors' => [
                    'otp' => ['OTP code expired']
                ]
            ], 401);
        }

        // Update the user's password
        $user->update([
            'otp' => null,
            'otp_send_time' => null,
            'password' => Hash::make($request->new_password)
        ]);

        // Delete all the user's auth tokens
        $user->tokens()->delete();

        // Create an auth token for the user
        $authToken = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'response' => 'success',
            'message' => 'Successfully reset password!',
            'authToken' => $authToken,
            'user' => $user
        ], 200);
    }


    // 
    // Update user avatar
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required',
        ]);

        // Find the user
        $user = User::where('id', $request->user()->id)->first();

        // Check if the user exists
        if (!$user) {
            return response()->json([
                'response' => 'failure',
                'message' => 'User does not exist'
            ], 401);
        }

        // Update the user's avatar
        $user->avatar = $request->avatar;
        $user->save();

        return response()->json([
            'response' => 'success',
            'message' => 'Successfully updated avatar!',
            'user' => $user
        ], 200);
    }

    public function setUpUserWalletAccount(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|min:4|max:4'
        ]);
        $user = $this->getCurrentLoggedUserBySanctum();
        if ($user) {

            //update or create wallet
            UserAccount::updateOrCreate(

                ['user_id' => $user->id],

                [
                    'user_id' => $user->id,
                    'account_name' => $user->name,
                    'account_currency' => 'UGX',
                    'account_balance' => 0,
                    'show_wallet_balance' => false,
                    'pin' => Hash::make($request->pin),
                    'is_active' => true
                ]
            );

            $message = "Hello " . $user->name . ", your wallet account has been successfully created. Your account balance is 0";



            try {
                Mail::to($user->email)->send(new WalletActivated($user, "Wallet Activated", $message));
            } catch (Throwable $th) {
                // throw $th;
                Log::error($th);
            }

            //send them a push notification
            //$device_token = UserDevice::where('user_id', $user->id)->get();

            // if (strlen($device_token) > 0) {
            //     $this->sendPushNotification($device_token[0]->push_token, "Wallet Activated", $message);
            // }





            return response()->json([
                'response' => 'success',
                'message' => 'Wallet account successfully created!'
            ]);
        } else {
            return response()->json([
                'response' => 'failure',
                'message' => 'User does not exist'
            ], 401);
        }
    }

    public function changeCustomerPin(Request $request)
    {
        try {
            $request->validate([
                'odPin' => 'required|string|min:4|max:4',
                'newPin' => 'required|string|min:4|max:4|confirmed',
            ]);

            $user = $this->getCurrentLoggedUserBySanctum();

            // Find the customer
            $customer = User::find($user->id);


            if (!$customer) {
                return response()->json([
                    'response' => 'failure',
                    'message' => 'Invalid credentials'
                ], 401);
            }
            $hashed_odPin = Hash::make($request->odPin);
            if ($hashed_odPin != $customer->pin) {
                return response()->json([
                    'response' => 'failure',
                    'message' => 'Invalid credentials'
                ], 401);
            }
            $hashed_newPin = Hash::make($request->newPin);
            $customer->pin = $hashed_newPin;
            $customer->save();
            //send message to customer
            $message = "Your new wallet  pin is " . $request->newPin . "If you did not make this request, please contact us.";
            $this->sendMessage($customer->phone_number, $message);
            try {
                Mail::to($user->email)->send(new WalletActivated($customer, "Wallet Activated", $message));
            } catch (Throwable $th) {
                // throw $th;
                Log::error($th);
            }

            //send them a push notification
            //send them a push notification
            $device_token = UserDevice::where('user_id', $user->id)->pluck('device_token')->get();
            if ($device_token) {
                $this->sendPushNotification($device_token, "Wallet Activated", $message);
            }
            return response()->json([
                'response' => 'success',
                'customer' => $customer
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'response' => 'failure',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function saveDeviceInfo(Request $request)
    {
        $request->validate([
            'push_token' => 'required|string|max:255',
            'device_id' => 'required|string|max:255',
            'device_model' => 'required|string|max:255',
            'device_manufacturer' => 'required|string|max:255',
            'app_version' => 'required|string|max:255',
            'device_os' => 'required|string|max:255',
            'device_os_version' => 'required|string|max:255',
            'device_user_agent' => 'required|string|max:255',
            'device_type' => 'required|string|max:255',
        ]);

        $user = $this->getCurrentLoggedUserBySanctum();

        // Check this info is already saved or not
        $userDevice = UserDevice::where('user_id', $user->id)
            ->where('device_id', $request->device_id)
            ->first();

        if ($userDevice) {
            $userDevice->update([
                'push_token' => $request->push_token,
                'device_model' => $request->device_model,
                'device_manufacturer' => $request->device_manufacturer,
                'app_version' => $request->app_version,
                'device_os' => $request->device_os,
                'device_os_version' => $request->device_os_version,
                'device_user_agent' => $request->device_user_agent,
                'device_type' => $request->device_type,
            ]);

            return response()->json(['response' => 'success', 'message' => 'Device token updated successfully.']);
        }

        // Save device token
        UserDevice::create([
            'user_id' => auth()->user()->id,
            'push_token' => $request->push_token,
            'device_id' => $request->device_id,
            'device_model' => $request->device_model,
            'device_manufacturer' => $request->device_manufacturer,
            'app_version' => $request->app_version,
            'device_os' => $request->device_os,
            'device_os_version' => $request->device_os_version,
            'device_user_agent' => $request->device_user_agent,
            'device_type' => $request->device_type,
        ]);

        return response()->json(['response' => 'success', 'message' => 'Device token saved successfully.']);
    }


    public function saveOrUpdateUserLocation(Request $request)
    {
        $request->validate([
            'lat' => 'required|string|max:255',
            'long' => 'required|string|max:255',
        ]);
        $user = $this->getCurrentLoggedUserBySanctum();
        UserLocation::updateOrCreate(
            ['user_id' => $user->id],
            ['lat' => $request->lat, 'long' => $request->long]
        );
        return response()->json(['response' => 'success', 'message' => 'Location updated successfully.']);
    }
}
