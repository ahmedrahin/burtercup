<x-guest-layout>
    @section('title', 'Reset Password')

    <style>
        .wrap-login-page .login-box {
            gap: 10px;
        }
    </style>

    <div id="wrapper">
        <!-- #page -->
        <div id="page" class="">
            <div class="wrap-login-page">
                <div class="flex-grow flex flex-column justify-center gap20">

                    <div class="login-box">
                        <div>
                            <h3>Reset your password</h3>
                            <div class="body-text">Enter your new password to login</div>
                        </div>
                        <form id="signInForm" class="form-login flex flex-column gap24" method="POST" action="{{ route('password.store') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <fieldset class="email">
                                <input type="email" placeholder="Enter your email address" name="email" value="{{ old('email', $request->email) }}" class="@error('email') error_border @enderror" hidden>
                            </fieldset>

                            <fieldset class="password">
                                <div class="body-title mb-10">New Password <span class="tf-color-1">*</span></div>
                                <input type="password" id="password" name="password" placeholder="Enter New Password" class="@error('password') error_border @enderror">
                                <span class="show-pass">
                                    <i class="icon-eye view"></i>
                                    <i class="icon-eye-off hide"></i>
                                </span>
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </fieldset>

                            <fieldset class="password">
                                <div class="body-title mb-10">Confirm Password <span class="tf-color-1">*</span></div>
                                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Your Password" class="@error('password_confirmation') error_border @enderror">
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </fieldset>

                            <button type="submit" class="tf-button w-full">Login</button>

                        </form>

                    </div>
                </div>
                <div class="text-tiny">Copyright © 2024 Remos, All rights reserved.</div>
            </div>
        </div>
        <!-- /#page -->
    </div>

</x-guest-layout>
