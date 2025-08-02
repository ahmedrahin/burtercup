<x-guest-layout>

    <style>
        input[type=checkbox], input[type=radio]{
            width: 16px;
            height: 18px;
        }
    </style>

    @section('title', 'Sign In')

    <div id="wrapper">
        <!-- #page -->
        <div id="page" class="">
            <div class="wrap-login-page">
                <div class="flex-grow flex flex-column justify-center gap30">

                    <div class="login-box">
                        <div>
                            <h3>Login to account</h3>
                            <div class="body-text">Enter your email & password to login</div>
                        </div>
                        <form id="signInForm" class="form-login flex flex-column gap24" method="POST" action="{{ route('login') }}">
                            @csrf

                            <fieldset class="email">
                                <div class="body-title mb-10">Email address <span class="tf-color-1">*</span></div>
                                <input type="email" placeholder="Enter your email address" name="email" value="{{ old('email') }}" class="@error('email') error_border @enderror">
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </fieldset>
                            <fieldset class="password">
                                <div class="body-title mb-10">Password <span class="tf-color-1">*</span></div>
                                <input class="password-input @error('password') error_border @enderror" type="password" placeholder="******" name="password" >
                                <span class="show-pass">
                                    <i class="icon-eye view"></i>
                                    <i class="icon-eye-off hide"></i>
                                </span>
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </fieldset>
                            <div class="flex justify-between items-center">
                                <div class="flex gap10">
                                    <input class="" type="checkbox" id="rememberMe" name="remember">
                                    <label class="body-text" for="signed">Remember me</label>
                                </div>
                                <a href="{{ route('password.request') }}" class="body-text tf-color">Forgot password?</a>
                            </div>
                            <button type="submit" class="tf-button w-full">Login</button>

                        </form>

                    </div>
                </div>
                
            </div>
        </div>
        <!-- /#page -->
    </div>

</x-guest-layout>

