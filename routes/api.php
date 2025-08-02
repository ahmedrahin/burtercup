<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\ResetPasswordController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserMessage;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These routes are loaded by the RouteServiceProvider within a group
| which is assigned the "api" middleware group. Build your API here.
*/

// =====================
// Auth Routes
// =====================
Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout');
});


Route::controller(ResetPasswordController::class)->group(function () {
    Route::post('forgot-password', 'forgotPassword');
    Route::post('reset-password', 'resetPassword');
    Route::post('verify-otp-password', 'verifyOtp');
});


Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    $user = User::findOrFail($id);

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Invalid verification link.'], 403);
    }

    if ($user->hasVerifiedEmail()) {
        return redirect()->to('https://bartercup.com/congratulation');
    }

    $user->markEmailAsVerified();
    event(new Verified($user));

    return redirect()->to('https://bartercup.com/congratulation');
})->middleware(['signed'])->name('verification.verify');


Route::post('/email/resend', function (Request $request) {
    ini_set('max_execution_time', 60);
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'User not found.'], 404);
    }

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified.'], 400);
    }

    $user->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification email resent.']);
});


Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::post('user-message', [UserMessage::class, 'sendMessage']);
    Route::post('share', [UserMessage::class, 'share']);
});
