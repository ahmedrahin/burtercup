<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['errors' => ['email' => 'User not found.']], 404);
        }

        $status = Password::broker()->sendResetLink($request->only('email'), function ($user, $token) {
            Mail::to($user->email)->send(new ResetPasswordMail($token, $user->email, $user->name));
        });

        if ($status == Password::RESET_LINK_SENT) {
            return redirect()->route('login')->with('success', 'Reset link sent! Check your email.');
        }

        return response()->json(['errors' => ['email' => 'Failed to send reset link.']], 422);
    }
}
