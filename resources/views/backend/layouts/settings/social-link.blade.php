@extends('backend.app')

@section('title', 'Social Link')

@push('styles')

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
        <form class="form-add-new-user form-style-2" id="addform">
            <div class="wg-box">

                <div class="right flex-grow">

                    <fieldset class="name mb-14">
                       <div class="row">
                            <div class="col-md-4">
                                <div class="body-title mb-10">Facebook </div>
                                <input class="flex-grow" type="text" placeholder="Facebook link" name="facebook_link" value="{{ $social->facebook_link ?? '' }}">
                                <div class="error text-danger"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="body-title mb-10">Instagram </div>
                                <input class="flex-grow" type="text" placeholder="Instagram link" name="instagram_link" value="{{ $social->instagram_link ?? '' }}">
                                <div class="error text-danger"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="body-title mb-10">Twitter  </div>
                                <input class="flex-grow" type="text" placeholder="Twitter link" name="twitter_link" value="{{ $social->twitter_link ?? '' }}">
                                <div class="error text-danger"></div>
                            </div>
                       </div>
                    </fieldset>

                    <fieldset class="name mb-14">
                        <div class="row">
                             <div class="col-md-4">
                                 <div class="body-title mb-10">Youtube</div>
                                 <input class="flex-grow" type="text" placeholder="Youtube link" name="youtube_link" value="{{ $social->youtube_link ?? '' }}">
                                 <div class="error text-danger"></div>
                             </div>
                             <div class="col-md-4">
                                 <div class="body-title mb-10">Linkedin </div>
                                 <input class="flex-grow" type="text" placeholder="Linkedin link" name="linkedin_link" value="{{ $social->linkedin_link ?? '' }}">
                                 <div class="error text-danger"></div>
                             </div>
                             <div class="col-md-4">
                                <div class="body-title mb-10">Whatsapp</div>
                                <input class="flex-grow" type="text" placeholder="Whatsapp" name="whatsapp" value="{{ $social->whatsapp ?? '' }}">
                                <div class="error text-danger"></div>
                            </div>
                        </div>
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
                    url: "{{ route('sociallink-setting.store') }}",
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
    @endpush

