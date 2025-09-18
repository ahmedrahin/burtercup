@extends('backend.app')

@section('title', 'Add New Programme')

@push('styles')
    <style>
        .dropify-wrapper {
            border: 1px dashed var(--Main);
            border-radius: 10px !important;
            width: 100%;
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

        .dropify-clear {
            display: none !important;
        }
    </style>
@endpush

@section('content')

    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>@yield('title')</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('dashboard') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{ route('programmes.index') }}">
                        <div class="text-tiny">Programme</div>
                    </a>
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
                            <div class="col-md-3">
                                <div>
                                    <div class="body-title mb-10">Image</div>
                                    <input type="file" class="form-control dropify" id="image" name="image"
                                        accept="image/*">
                                    <div class="error text-danger"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div>
                                    <div class="body-title mb-10">Details Thumbnail</div>
                                    <input type="file" class="form-control dropify" id="thumbnail" name="thumbnail"
                                        accept="image/*">
                                    <div class="error text-danger"></div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="name mb-14">
                        <div class="body-title mb-10">Name</div>
                        <input class="flex-grow" type="text" placeholder="Name" name="name">
                        <div class="error text-danger"></div>
                    </fieldset>

                    <fieldset class="name mb-14">
                        <div class="body-title mb-10">Given Coin</div>
                        <input class="flex-grow" type="text" placeholder="Given Coin" name="coin">
                        <div class="error text-danger"></div>
                    </fieldset>

                    <fieldset class="name mb-14">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="body-title mb-10">Country Name</div>
                                <input class="flex-grow" type="text" placeholder="Country Name" name="country">
                                <div class="error text-danger"></div>
                            </div>

                            <div class="col-md-4">
                                <div class="body-title mb-10">Select Programme Type</div>
                                <select name="programme_type" id="">
                                    <option value="">Select Programme Type</option>
                                    <option value="digital">Digital</option>
                                    <option value="physical">Physical</option>
                                </select>
                                <div class="error text-danger"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="body-title mb-10">Select Programme Category</div>
                                <select name="category" id="">
                                    <option value="">Select Programme Category</option>
                                    <option value="digital">Digital</option>
                                    <option value="physical">Physical</option>
                                </select>
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
                    url: "{{ route('game.store') }}",
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

                            // reset form
                            $('#addform')[0].reset();
                            $('.dropify-clear').click();

                            // reset options container
                            $('#options-container').html(`
                                <div class="option-item border p-5 mb-20 rounded">
                                    <div class="row gap-0">
                                        <div class="col-md-6 col-6 thumb-file">
                                            <div>
                                                <div class="body-title mb-10">Image</div>
                                                <input type="file" class="form-control dropify"
                                                    name="options[0][option_a_image]" accept="image/*">
                                            </div>
                                            <div style="margin-top: 10px;">
                                                <div class="body-title mb-10">Title</div>
                                                <input class="flex-grow form-control"
                                                    type="text" placeholder="Title"
                                                    name="options[0][option_a_name]" >
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-6 thumb-file">
                                            <div>
                                                <div class="body-title mb-10">Image</div>
                                                <input type="file" class="form-control dropify"
                                                    name="options[0][option_b_image]" accept="image/*">
                                            </div>
                                            <div style="margin-top: 10px;">
                                                <div class="body-title mb-10">Title</div>
                                                <input class="flex-grow form-control"
                                                    type="text" placeholder="Title"
                                                    name="options[0][option_b_name]" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `);

                            $('.dropify').dropify();
                        }

                    },
                    error: function(xhr) {
                        $('#submitBtn .btn-text').text('Save');
                        $('#submitBtn .loader').addClass('hidden');
                        $('#submitBtn').prop('disabled', false);

                        if (xhr.status === 422) {
                            $('.error-border').removeClass('error-border');
                            let errors = xhr.responseJSON.errors;

                            // Loop through errors and show them
                            $.each(errors, function(key, val) {
                                // Append error message to the error list
                                $('#errors-msgs').append(`
                                    <li>
                                        <div class="block-warning">
                                            <i class="icon-alert-octagon"></i>
                                            <div class="body-title-2">${val[0]}</div>
                                        </div>
                                    </li>
                                `);

                                let inputField = $('[name="' + key + '"]');

                                // Handle Select2 errors
                                if (inputField.hasClass('select2')) {
                                    // Add error class to Select2
                                    inputField.closest('div').find('.error').text(val[
                                        0]);
                                    inputField.next('.select2').addClass(
                                    'error_border');
                                } else {
                                    inputField.addClass('error_border');
                                    let errorDiv = inputField.closest('fieldset').find(
                                        '.error');
                                    errorDiv.text(val[0]);
                                }
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
