<x-guest-layout>

    @section('title', 'Forgot Password')

    <style>
        .wrap-login-page .login-box {
            gap: 25px;
        }
    </style>

    <div id="wrapper">
        <!-- #page -->
        <div id="page" class="">
            <div class="wrap-login-page">
                <div class="flex-grow flex flex-column justify-center gap30">
                    
                    <div class="login-box">
                        <div>
                            <h3>Forgot your password?</h3>
                            <div class="body-text">Please enter the email address associated with your account and We will email you a link to reset your password.</div>
                        </div>
                        <form id="signInForm" class="form-login flex flex-column gap24"  method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <fieldset class="email">
                                <div class="body-title mb-10">Email address <span class="tf-color-1">*</span></div>
                                <input type="email" placeholder="Enter your email address" name="email" value="{{ old('email') }}" class="@error('email') error_border @enderror">
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </fieldset>

                            <button type="submit" class="tf-button w-full">Send</button>

                            <div style="text-align: center;">
                                <a href="{{ route('login') }}" class="body-text tf-color">Back to login?</a>
                            </div>

                        </form>

                    </div>
                </div>
                <div class="text-tiny">Copyright © 2024 Remos, All rights reserved.</div>
            </div>
        </div>
        <!-- /#page -->
    </div>


</x-guest-layout>
