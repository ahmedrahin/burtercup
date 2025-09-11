@extends('backend.app')

@section('title', 'Edit Category')

@push('styles')

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
                    <a href="{{ route('category.index') }}">
                        <div class="text-tiny">Category</div>
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

       <form class="form-add-new-user form-style-2" id="addform" enctype="multipart/form-data">
            <div class="wg-box">
                <div class="right flex-grow">

                    <fieldset class="name mb-14">
                        <div class="body-title mb-10">Category Name</div>
                        <input class="flex-grow" type="text" placeholder="Name" name="name" value="{{ $data->name }}">
                        <div class="error text-danger"></div>
                    </fieldset>

                    <div class="row">
                        <div class="col-md-4">
                            <fieldset class="name mb-14">
                                <div class="body-title mb-10">Free</div>
                                <select name="free">
                                    <option value="0" {{ $data->free == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ $data->free == 1 ? 'selected' : '' }}>Yes</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-md-4">
                            <fieldset class="name mb-14">
                                <div class="body-title mb-10">Premium</div>
                                <select name="premium">
                                    <option value="0" {{ $data->premium == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ $data->premium == 0 ? 'selected' : '' }}>Yes</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-md-4">
                            <fieldset class="name mb-14">
                                <div class="body-title mb-10">Platinum</div>
                                <select name="platinum">
                                    <option value="0" {{ $data->platinum == 0 ? 'selected' : '' }}>No</option>
                                    <option value="1" {{ $data->platinum == 0 ? 'selected' : '' }}>Yes</option>
                                </select>
                            </fieldset>
                        </div>

                    </div>

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
            $('#addform').on('submit', function(e) {
                e.preventDefault();
                $('.error').html('');
                $('.error_border').removeClass('error_border');

                let formData = new FormData(this);
                formData.append('_method', 'PUT');

                // Show loader
                $('#submitBtn .btn-text').text('Saving...');
                $('#submitBtn .loader').removeClass('hidden');
                $('#submitBtn').prop('disabled', true);

                $.ajax({
                    url: "{{ route('game-category.update', $data->id) }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Reset button
                        $('#submitBtn .btn-text').text('Save Changes');
                        $('#submitBtn .loader').addClass('hidden');
                        $('#submitBtn').prop('disabled', false);

                        if (response.success) {
                            $('.error_border').removeClass('error_border');
                            $('.error').text('');
                            ajaxMessage(response.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        $('#submitBtn .btn-text').text('Save Changes');
                        $('#submitBtn .loader').addClass('hidden');
                        $('#submitBtn').prop('disabled', false);

                        if (xhr.status === 422) {
                            // Validation errors
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, val) {
                                let inputField = $('[name="' + key + '"]');
                                inputField.addClass('error_border');
                                inputField.siblings('.error').text(val[0]);
                            });
                        } else {
                            ajaxMessage('Something went wrong', 'error');
                        }
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            var drEvent = $('.dropify').dropify();
            drEvent.on('dropify.beforeClear', function(event, element) {
                $(event.target).closest('.col-md-2').find('.remove-checkbox').prop('checked', true);
            });

            $('.dropify').on('change', function() {
                $(this).closest('.col-md-2').find('.remove-checkbox').prop('checked', false);
            });
        });
    </script>
@endpush
