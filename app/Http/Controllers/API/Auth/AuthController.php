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

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'phone' => 'nullable|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'status' => false,
                'code' => 400,
                'message' => 'validation error'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone
            ]);

            $token = JWTAuth::fromUser($user);
            $user->sendEmailVerificationNotification();

            DB::commit();

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'User registered successfully.',
                'user_data' => $user,
                'token' => $token,
                'email_verified' => false,
            ], );

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during registration.',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public function onboard_one(Request $request)
    {
        $user = auth('api')->user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
                'code' => 401,
            ], 401);
        }

        if ($user->onboard_first) {
            return response()->json([
                'status' => false,
                'message' => 'User information already updated',
                'code' => 401,
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'date_of_birth' => 'required|date|before_or_equal:' . now()->subYears(15)->format('Y-m-d'),
            'gender' => 'required',
            'categories' => 'required',

        ], [
            'date_of_birth.before_or_equal' => 'You must be at least 15 years old to register.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
                'status' => false,
                'code' => 400,
                'message' => 'validation error'
            ], 400);
        }

        $user->update([
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'categories' => is_string($request->categories) ? json_decode($request->categories, true) : $request->categories,
            'age' => Carbon::parse($request->date_of_birth)->age,
            'onboard_first' => true,
        ]);

        return response()->json([
            'success' => true,
            'code' => 200,
            'data' => $user,
            'message' => 'User information update successfully',
        ], );

    }

    public function onboard_two(Request $request)
    {
        $user = auth('api')->user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
                'code' => 401,
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'city' => 'required',
            'country' => 'required',
            'phone' => 'required',
            'address' => 'required',

        ], [
            'date_of_birth.before_or_equal' => 'You must be at least 15 years old to register.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
                'status' => false,
                'code' => 400,
                'message' => 'validation error'
            ], 400);
        }

        $user->update([
            'country' => $request->country,
            'city' => $request->city,
            'phone' => $request->phone,
            'address' => $request->address,
            'onboard_sec' => true,
        ]);

        return response()->json([
            'success' => true,
            'code' => 200,
            'data' => $user,
            'message' => 'User information update successfully',
        ], );
    }

    public function verifyRegistrationOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email',
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'code' => 400,
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
                'data' => $user
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
            $user->sendEmailVerificationNotification();

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'A new verify link sent',
                'data' => $user
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
            'data' => $user
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
                'message' => 'validate error'
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'error' => 'User not found',
                'code' => 404,
            ], 404);
        }

        // Check if account is inactive
        if ($user->status === 'inactive') {
            return response()->json([
                'status' => false,
                'error' => 'Your account is inactive. Please contact support.',
                'code' => 403,
            ], 403);
        }

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
            'user_data' => $user,
            'email_verified' => $user->hasVerifiedEmail(),
            'onboard_first' => $user->onboard_first ? true : false,
            'onboard_sec' => $user->onboard_sec ? true : false,
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
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to log out, please try again.' . $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }
}
