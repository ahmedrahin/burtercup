@extends('backend.app')

@section('title', 'My Profile')

@push('styles')
    <style>
        .dropify-wrapper {
            border: 2px dashed #ab54db !important; /* Green dashed border */
            background-color: #f9f9f9 !important; /* Light gray background */
            border-radius: 10px !important;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
        }

        .dropify-wrapper:hover {
            border-color: #ab54db !important;
            background-color: #ab54db1c !important; /* Lighter green background on hover */
            background-image: inherit !important;
        }

        .dropify-message span.file-icon {
            font-size: 40px !important;
            color: #ab54db !important; /* Green upload icon */
        }

        .dropify-message p {
            font-weight: bold !important;
            color: #333 !important;
        }

        .dropify-clear {
            background-color: #f44336 !important; /* Red clear button */
            color: white !important;
            border-radius: 5px !important;
        }

        .dropify-clear:hover {
            background-color: #d32f2f !important;
        }

        .dropify-font-upload:before, .dropify-wrapper .dropify-message span.file-icon:before{
            font-size: 50px;
            font-weight: 700;
        }
        .file-icon p {
            font-size: 12px !important;
            color: #45444887 !important;
        }
    </style>
@endpush

@section('content')

    <div class="geex-content__section geex-content__form">
        <div class="geex-content__form__wrapper">
            <div class="geex-content__form__wrapper__item geex-content__form__right">
                <form action="{{ route('profile.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="geex-content__form__single pb-0 mb-0">
                        <h4 class="geex-content__form__single__label">@yield('title')</h4>
                        <div class="row">
                            <div class="col-md-2">
                                <label for="avatar">Profile Image</label>
                                <input type="file" class="form-control dropify" id="avatar" name="avatar"
                                data-default-file="{{ isset($user) && $user->avatar ? asset($user->avatar) : '' }}" accept="image/*">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input remove-checkbox" id="remove_avatar" name="remove_avatar" value="1" hidden>
                                </div>
                                @error('avatar')
                                    <div style="color: red" class="mt-2">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row geex-content__form__single__box mb-0" style="gap: 0;">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="name">Name</label>
                                    <input type="text" placeholder="enter your name" class="form-control @error('name') error_border @enderror" name="name" id="name" value="{{ $user->name }}" />
                                    @error('name')
                                        <div style="color: red" class="mt-2">{{$message}}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="email">E-mail Address</label>
                                    <input type="text" placeholder="enter emali address" class="form-control @error('email') error_border @enderror" name="email" id="email" value="{{ $user->email }}" />
                                    @error('email')
                                        <div style="color: red" class="mt-2">{{$message}}</div>
                                    @enderror
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


    <div class="geex-content__section geex-content__form mt-40">
        <div class="geex-content__form__wrapper">
            <div class="geex-content__form__wrapper__item geex-content__form__right">
                <form action="{{ route('update.password') }}" method="POST">
                    @csrf
                    <div class="geex-content__form__single pb-0 mb-0">
                        <h4 class="geex-content__form__single__label">Update Your Password</h4>
                        <div class="row geex-content__form__single__box mb-0" style="gap: 0;">
                            <div class="row">
                                <!-- Old Password -->
                                <div class="col-md-4 mb-4">
                                    <label for="old_password">Current Password</label>
                                    <input type="password" placeholder="******" class="form-control @error('old_password') error_border @enderror" name="old_password" id="old_password" />
                                    @error('old_password')
                                        <div style="color: red" class="mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
    
                                <!-- New Password -->
                                <div class="col-md-4 mb-4">
                                    <label for="password">New Password</label>
                                    <input type="password" placeholder="******" class="form-control @error('password') error_border @enderror" name="password" id="password" />
                                    @error('password')
                                        <div style="color: red" class="mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
    
                                <!-- Confirm Password -->
                                <div class="col-md-4 mb-4">
                                    <label for="password_confirmation">Confirm New Password</label>
                                    <input type="password" placeholder="******" class="form-control @error('password_confirmation') error_border @enderror" name="password_confirmation" id="password_confirmation" />
                                    @error('password_confirmation')
                                        <div style="color: red" class="mt-2">{{ $message }}</div>
                                    @enderror
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

@push('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Dropify
            var drEvent = $('.dropify').dropify({
                messages: {
                    default: '<i class="bi bi-cloud-upload" style="font-size: 40px; color: #4CAF50;"></i><br> Drag & Drop or Click to Upload',
                    replace: 'Drag and drop or click to replace',
                    remove: 'Remove',
                    error: 'Oops, something went wrong!'
                }
            });

            // Handle Dropify's remove button click
            drEvent.on('dropify.beforeClear', function (event, element) {
                // Find the checkbox next to the input field and check it
                $(event.target).closest('.col-md-2').find('.remove-checkbox').prop('checked', true);
            });

            // Handle Dropify file selection (uncheck the checkbox)
            $('.dropify').on('change', function () {
                $(this).closest('.col-md-2').find('.remove-checkbox').prop('checked', false);
            });
        });

    </script>
@endpush
