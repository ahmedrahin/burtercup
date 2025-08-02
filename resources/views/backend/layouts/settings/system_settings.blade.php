@extends('backend.app')

@section('title', 'System Setting')

@push('styles')
    <style>
        .dropify-wrapper {
            border: 1px dashed var(--Main);
            border-radius: 10px !important;
            width: 86%;
            height: 185px;
        }

        .dropify-wrapper:hover {
            background-color: #F0F5F9 !important;
            background-image: inherit !important;
        }

        .dropify-message span.file-icon {
            font-size: 20px !important;
            color: #2275fc !important;
        }

        .dropify-message p {
            font-weight: bold !important;
            color: #333 !important;
        }

        .dropify-clear {
            background-color: #f44336 !important;
            color: white !important;
            border-radius: 5px !important;
        }

        .dropify-clear:hover {
            background-color: #d32f2f !important;
        }

        .dropify-font-upload:before,
        .dropify-wrapper .dropify-message span.file-icon:before {
            font-size: 50px;
            font-weight: 700;
        }

        .file-icon p {
            font-size: 12px !important;
            color: #45444887 !important;
        }

        .plan-box,
        .plan-box-weekly,
        .plan-box-monthly {
            position: relative;
        }

        .remove-plan,
        .remove-plan-weekly,
        .remove-plan-monthly {
            position: absolute;
            top: 25px;
            right: 0;
            padding: 5px 10px;
            border-radius: 50%;
            border: none;
            background: #ff0000d4;
            color: white;
            width: 27px;
            height: 27px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .custom-select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            padding: .375rem 1.25rem;
            font-size: 1rem;
            width: 100%;
        }

        .custom-select:focus {
            border-color: #80bdff;
            outline: none;
            box-shadow: 0 0 0 .2rem rgba(38, 143, 255, .25);
        }

        .custom-select::-ms-expand {
            display: none;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            font-weight: bold;
            margin-bottom: .5rem;
            display: block;
        }

        .ck-rounded-corners .ck.ck-editor__main>.ck-editor__editable,
        .ck.ck-editor__main>.ck-editor__editable.ck-rounded-corners {
            height: 250px;
            border-bottom-left-radius: 20px !important;
            border-bottom-right-radius: 20px !important;
        }
        .duplicate {
            margin-top: 30px;
            border-top: 1px solid #8080803b;
            padding-top: 30px;
        }
        #add-plan {
            font-size: 13px;
            padding: 8px 15px;
        }
    </style>
@endpush

@section('content')

    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>@yield('title')</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('dashboard') }}"><div class="text-tiny">Dashboard</div></a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">@yield('title')</div>
                </li>
            </ul>
        </div>

        <!-- add-new-user -->
        <form class="form-add-new-user form-style-2" id="addform" enctype="multipart/form-data">
            <div class="wg-box">

                <div class="right flex-grow">
                    <fieldset class="name mb-14">
                        <div class="row">
                            <div class="col-md-2 col-6">
                                <div class="body-title mb-10">Logo</div>
                                <input type="file" class="form-control dropify" id="image" name="logo" accept="image/*" data-default-file="{{ isset($setting) && $setting->logo ? asset($setting->logo) : '' }}">
                                <div class="form-check">
                                    <input type="checkbox" class="remove-checkbox" name="remove_logo" value="1" hidden>
                                </div>
                                <div class="error text-danger"></div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="body-title mb-10">Dark Logo</div>
                                <input type="file" class="form-control dropify" id="dark_image" name="dark_image" accept="image/*" data-default-file="{{ isset($setting) && $setting->dark_logo ? asset($setting->dark_logo) : '' }}">
                                <div class="form-check">
                                    <input type="checkbox" class="remove-checkbox" name="remove_dark" value="1" hidden>
                                </div>
                                <div class="error text-danger"></div>
                            </div>
                            <div class="col-md-2 col-6">
                                <div class="body-title mb-10">Fav Icon</div>
                                <input type="file" class="form-control dropify" id="fav" name="favicon" accept="image/*" data-default-file="{{ isset($setting) && $setting->favicon ? asset($setting->favicon) : '' }}">
                                <div class="form-check">
                                    <input type="checkbox" class="remove-checkbox" name="remove_fav" value="1" hidden>
                                </div>
                                <div class="error text-danger"></div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="name mb-14">
                        <div class="body-title mb-10">Site Title</div>
                        <input class="flex-grow" type="text" placeholder="Site Title" name="title" value="{{ $setting->title ?? '' }}">
                        <div class="error text-danger"></div>
                    </fieldset>

                    <fieldset class="name mb-14">
                        <div class="body-title mb-10">E-mail Address</div>
                        <input class="flex-grow" type="text" placeholder="E-mail Address" name="email" value="{{ $setting->email ?? '' }}">
                        <div class="error text-danger"></div>
                    </fieldset>

                    <fieldset class="name mb-14">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="body-title mb-10">Phone no.</div>
                                <input class="flex-grow" type="text" placeholder="Phone no." name="phone1" value="{{ $setting->phone1 ?? '' }}">
                                <div class="error text-danger"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="body-title mb-10">Phone no. 2</div>
                                <input class="flex-grow" type="text" placeholder="Phone no." name="phone2" value="{{ $setting->phone2 ?? '' }}">
                                <div class="error text-danger"></div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="name mb-14">
                        <div class="body-title mb-10">Address</div>
                        <input class="flex-grow" type="text" placeholder="Address" name="address" value="{{ $setting->address ?? '' }}">
                        <div class="error text-danger"></div>
                    </fieldset>

                    <fieldset class="name mb-14">
                        <div class="body-title mb-10">Description</div>
                        <textarea name="description" placeholder="Write here...">{{ $setting->description ?? '' }}</textarea>
                        <div class="error text-danger"></div>
                    </fieldset>

                </div>
            </div>

            <div class="bot " style="justify-content:right;">
                <button class="tf-button w180 btn-add" type="submit" id="submitBtn">
                    <span class="btn-text">Save</span>
                    <span class="loader spinner-border spinner-border-sm hidden" role="status" aria-hidden="true"></span>
                </button>
            </div>

        </form>

        <!-- /add-new-user -->
    </div>

@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            $('.dropify').dropify();

            $('#addform').on('submit', function(e) {
                e.preventDefault();
                $('.error').html('');

                var formData = new FormData(this);

                // Show loader
                $('#submitBtn .btn-text').text('Saving...');
                $('#submitBtn .loader').removeClass('hidden');
                $('#submitBtn').prop('disabled', true);

                $.ajax({
                    url: "{{ route('system-setting.store') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Reset button
                        $('#submitBtn .btn-text').text('Save');
                        $('#submitBtn .loader').addClass('hidden');
                        $('#submitBtn').prop('disabled', false);

                        if (response.success) {
                            $('.error_border').removeClass('error_border');
                            $('.error').text('');
                            ajaxMessage(response.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        $('#submitBtn .btn-text').text('Save');
                        $('#submitBtn .loader').addClass('hidden');
                        $('#submitBtn').prop('disabled', false);

                        if (xhr.status === 422) {
                            $('.error-border').removeClass('error-border');
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, val) {
                                let inputField = $('[name="' + key + '"]');
                                inputField.siblings('.error').text(val[0]);
                                inputField.addClass('error_border');
                            });
                        } else {
                            alert('Something went wrong!');
                        }
                    }

                });
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            var drEvent = $('.dropify').dropify();
            drEvent.on('dropify.beforeClear', function (event, element) {
                $(event.target).closest('.col-md-2').find('.remove-checkbox').prop('checked', true);
            });

            $('.dropify').on('change', function () {
                $(this).closest('.col-md-2').find('.remove-checkbox').prop('checked', false);
            });
        });
    </script>
@endpush

