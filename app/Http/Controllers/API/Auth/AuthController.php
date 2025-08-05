<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;
use App\Notifications\PasswordResetNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{
    /**
     * Register a new user and send OTP for verification.
     *
     * @param Request $request
     * @return JsonResponse
     */

     public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name'          => 'required|string|max:255',
            'last_name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|confirmed|min:6',
            'phone'         => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'status' => false,
            ], 400);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'      => $request->first_name,
                'last_name' => $request->last_name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'phone'     => $request->phone
            ]);

            $token = JWTAuth::fromUser($user);
            $user->sendEmailVerificationNotification();

            DB::commit();

            return response()->json([
                'success' => true,
                'code' => 200,
                'message'   => 'User registered successfully.',
                'token'     => $token,
                'verified'  => false,
            ], );

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success'   => false,
                'message'   => 'An error occurred during registration.',
                'error'     => $e->getMessage(),
            ], 500);
        }

     }

     public function verifyRegistrationOtp(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'email'    => 'required|string|email|exists:users,email',
             'otp'      => 'required|numeric',
         ]);

         if ($validator->fails()) {
             return response()->json([
                 'success' => false,
                 'message' => $validator->errors()->first(),
             ], 400);
         }

         try {
             $user = User::where('email', $request->email)->first();

             if ($user->otp != $request->otp || Carbon::now()->greaterThan($user->otp_expiration)) {
                 return response()->json([
                     'success' => false,
                     'message' => 'Invalid or expired OTP.',
                 ], 400);
             }

             $user->email_verified_at = Carbon::now();
             $user->otp = null;
             $user->otp_expiration = null;
             $user->save();

             return response()->json([
                 'success' => true,
                 'code' => 200,
                 'message' => 'Email verified successfully.',
             ], 200);
         } catch (Exception $e) {
             return response()->json([
                 'success' => false,
                 'message' => 'An error occurred while verifying OTP.',
                 'error' => $e->getMessage(),
             ], 500);
         }
     }

     public function resendRegistrationOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 400);
        }

        try {
            $user = User::where('email', $request->email)->first();

            // Generate new OTP
            //$otp = rand(100000, 999999);
            $otp = rand(1000, 9999);
            $user->otp = $otp;
            $user->otp_expiration = Carbon::now()->addMinutes(15);
            $user->save();

            // Send OTP via email
            Mail::send('api.emails.otp-reg', ['otp' => $otp], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Your Registration OTP');
            });

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'A new OTP has been sent to your email.',
                'otp'     => $otp,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while resending OTP.',
                'error' => $e->getMessage(),
            ], 500);
        }
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
        $user->otp = null; // Clear OTP after successful verification
        $user->otp_expiration = null;
        $user->email_verified_at = Carbon::now();
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully.',
            'code' => 200,
        ], 200);
    }

     public function login(Request $request)
     {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

         $credentials = $request->only('email', 'password');

         if (!$token = JWTAuth::attempt($credentials)) {
             return response()->json(['error' => 'Invalid credentials'], 401);
         }

         // Get the authenticated user
         $user = auth()->user();

         $user->update([
            'last_login_at' => now()->toDateTimeString(),
        ]);

         return response()->json([
             'success' => true,
             'code' => 200,
             'message' => 'User login successfully',
             'token' => $token,
            //  'user_data' => $user,
             'email_verified' => $user->hasVerifiedEmail(),
         ]);
     }


     public function logout(): JsonResponse
    {
        try {
            // Invalidate token
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'message' => 'Successfully logged out',
                'success' => true,
                'code' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to log out, please try again.',
                'code'    => 500,
            ], 500);
        }
    }
}
