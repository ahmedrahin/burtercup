<?php

namespace App\Http\Controllers\API\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OTPRequest;
use App\Http\Requests\Auth\OTPVerificationRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Mail\OTPMail;
use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email|exists:users,email',
            ],
            [
                'email.required' => 'The email field is required.',
                'email.email' => 'The email format is invalid.',
                'email.exists' => 'The provided email is not found.',
            ]
        );

        if ($validator->fails()) {
            //return response()->json($validator->errors(), 422);
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

        $otp = rand(100000, 999999); // Generate 6-digit OTP
        $user = User::where('email', $request->email)->first();
        $user->otp = $otp;
        $user->otp_expiration = now()->addMinutes(30);
        $user->save();

        // Send OTP via email
        Mail::raw("Your OTP code is: $otp", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Reset Password OTP');
        });

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to your email.',
            'otp' => $otp,
            'email' => $user->email,
            'code' => 200,
        ], 200);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->otp !== $request->otp || now()->greaterThan($user->otp_expiration)) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid or expired OTP.',
                'code' => 403,
            ], 403);
        }

        // Generate a temporary reset token
        $resetToken = Hash::make($user->email . now());
        $user->reset_token = $resetToken; // Store the reset token
        $user->otp = null; // Clear OTP after successful verification
        $user->otp_expiration = null;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully.',
            'reset_token' => $resetToken, // Return the reset token to the user
            'code' => 200,
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reset_token' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
            'new_password_confirmation' => 'required|string|min:6|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

       $user = User::where('reset_token', $request->reset_token)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid or expired reset token.',
                'code' => 403,
            ], 403);
        }

        $user->password = Hash::make($request->new_password);
        $user->reset_token = null; // Clear reset token after successful password reset
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully.',
            'code' => 200,
        ], 200);
    }
}
