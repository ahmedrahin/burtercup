@extends('backend.app')

@section('title', 'Add New FAQ')

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
                    <a href="{{ route('faq.index') }}">
                        <div class="text-tiny">FAQ List</div>
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
        <form class="form-add-new-user form-style-2" id="addform">
            <div class="wg-box">
                <div class="right flex-grow">

                    <fieldset class="email mb-14">
                        <div class="body-title mb-10">Question</div>
                        <input class="flex-grow" type="text" placeholder="Question" name="question">
                        <div class="error text-danger"></div>
                    </fieldset>

                    <fieldset class="email mb-14">
                        <div class="body-title mb-10">Select question type</div>
                        <select name="type" id="">
                            <option value="">select a type</option>
                            <option value="general">General</option>
                            <option value="buyers">For Buyers</option>
                            <option value="sellers">For Sellers</option>
                            <option value="communication">Communication</option>
                        </select>
                        <div class="error text-danger"></div>
                    </fieldset>
                    
                    <fieldset class="email mb-14">
                        <div class="body-title mb-10">Answer</div>
                        <textarea name="answer" id="" placeholder="write here..."></textarea>
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
                    url: "{{ route('faq.store') }}",
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
                            $('#addform')[0].reset();
                            $('.dropify-clear').click();
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
