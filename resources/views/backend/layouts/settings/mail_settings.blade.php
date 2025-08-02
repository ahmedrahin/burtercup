@extends('backend.app')

@section('title', 'Mail Settings')


@section('content')

    <div class="geex-content__section geex-content__form">
        <div class="geex-content__form__wrapper">
            <div class="geex-content__form__wrapper__item geex-content__form__right">
                <form action="{{ route('mail-setting.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="geex-content__form__single pb-0 mb-0">
                        <h4 class="geex-content__form__single__label">@yield('title')</h4>
                        
                        <div class="row geex-content__form__single__box mb-0" style="gap: 0;">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label f-w-500" for="mail_mailer">Mail Mailer :</label>
                                        <input type="text"
                                               class="form-control  @error('mail_mailer') is-invalid @enderror"
                                               placeholder="mail mailer" id="mail_mailer" name="mail_mailer"
                                               value="{{ env('MAIL_MAILER') }}">
                                        @error('mail_mailer')
                                        <div style="color: red">{{$message}}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="mail_host" class="form-label f-w-500">Mail Host :</label>
                                        <input type="text"
                                               class="form-control @error('mail_host') is-invalid @enderror"
                                               placeholder="mail host" name="mail_host" id="mail_host"
                                               value="{{ env('MAIL_HOST') }}">
                                        @error('mail_host')
                                        <div style="color: red">{{$message}}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label f-w-500" for="mail_port">Mail Port :</label>
                                        <input type="text"
                                               class="form-control @error('mail_port') is-invalid @enderror"
                                               placeholder="mail port" id="mail_port" name="mail_port"
                                               value="{{ env('MAIL_PORT') }}">
                                        @error('mail_port')
                                        <div style="color: red">{{$message}}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label f-w-500" for="mail_username">Mail Username :</label>
                                        <input type="text"
                                               class="form-control @error('mail_username') is-invalid @enderror"
                                               placeholder="" name="mail_username" id="mail_username"
                                               value="{{ env('MAIL_USERNAME') }}">
                                        @error('mail_username')
                                        <div style="color: red">{{$message}}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label f-w-500" for="mail_password">Mail Password :</label>
                                        <input type="text"
                                               class="form-control @error('mail_password') is-invalid @enderror"
                                               placeholder="mail password" id="mail_password" name="mail_password" value="{{ env('MAIL_PASSWORD') }}">
                                        @error('mail_password')
                                        <div style="color: red">{{$message}}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label f-w-500" for="mail_encryption">Mail Encryption :</label>
                                        <input type="text"
                                               class="form-control @error('mail_encryption') is-invalid @enderror"
                                               name="mail_encryption" placeholder="mail encryption" id="mail_encryption" value="{{ env('MAIL_ENCRYPTION') }}">
                                        @error('mail_encryption')
                                        <div style="color: red">{{$message}}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label f-w-500" for="mail_from_address">Mail From Address :</label>
                                        <input type="text" placeholder="mail from address" id="mail_from_address" class="form-control @error('mail_from_address') is-invalid @enderror" name="mail_from_address" value="{{ env('MAIL_FROM_ADDRESS') }}">
                                        @error('mail_from_address')
                                        <div style="color: red">{{$message}}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-0 text-end">
                            <button class="geex-btn geex-btn--primary">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


   

@endsection


