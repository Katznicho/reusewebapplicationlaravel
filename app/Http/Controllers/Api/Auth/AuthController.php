<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
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
            'role' => 'required'
        ]);

        // Generate a random OTP code
        // $otpCode = 123456;
        $otpCode = random_int(100000, 999999);

        // Create a new user
        $user = User::create([
            'name' => $request->first_name, '',
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'otp' => Hash::make($otpCode),
            'otp_send_time' => now(),
            'password' => Hash::make($request->password),
            'role_id' => $request->role
        ]);

        // Create an auth token for the user
        $authToken = $user->createToken('authToken')->plainTextToken;

        // Create Web Notification
        $action = env('APP_URL') . "/admin/clients";
        $this->createNotification($action, "A new user, <strong>{$user->first_name}</strong> has registered.", $user->id);

        try {
            // Send the OTP code to the user's email
            Mail::to($user->email)->send(new EmailUserVerification($otpCode, $user, 'user'));
        } catch (\Throwable $th) {
            // throw $th;
        }

        return response()->json([
            'response' => 'success',
            'message' => 'Successfully created user!',
            'user' => $user,
            'authToken' => $authToken
        ], 201);
    }
}
