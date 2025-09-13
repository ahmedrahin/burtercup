@extends('backend.app')

@section('title', 'Edit Game')

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
                    <a href="{{ route('game.index') }}">
                        <div class="text-tiny">Game</div>
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
                        <div class="body-title mb-10">Game Category</div>
                        <select name="category" class="select2">
                            <option value="">Select a parent category</option>
                            @foreach ($categories as $key => $category)
                                <option value="{{ $category->id }}"
                                    {{ $category->id == $data->game_category_id ? 'selected' : '' }}>{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="error text-danger"></div>
                    </fieldset>

                    <fieldset class="name mb-14">
                        <div class="body-title mb-10">Name</div>
                        <input class="flex-grow" type="text" placeholder="Name" name="name"
                            value="{{ $data->name }}">
                        <div class="error text-danger"></div>
                    </fieldset>

                    <fieldset class="name mb-14">
                        <div class="body-title mb-10">Short Title</div>
                        <input class="flex-grow" type="text" placeholder="short title" name="short_title"
                            value="{{ $data->short_title }}">
                        <div class="error text-danger"></div>
                    </fieldset>

                    <fieldset class="name mb-14">
                        <div class="col-md-3">
                            <div>
                                <div class="body-title mb-10">Product Image</div>
                                <input type="file" class="form-control dropify" id="image" name="image"
                                    accept="image/*"
                                    data-default-file="{{ isset($data) && $data->image ? asset($data->image) : '' }}">
                                <div class="error text-danger"></div>
                            </div>
                        </div>
                    </fieldset>

                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="wg-box">
                        <div class="right flex-grow">
                            <h3 style="margin-bottom: 10px;">Game Option</h3>

                            <fieldset class="name mb-14" id="options-container">
                                @foreach ($data->options as $index => $option)
                                    <div class="option-item border p-5 mb-20 rounded">
                                        <div class="d-flex justify-content-end mb-2">
                                            @if ($index > 0)
                                                <button type="button"
                                                    class="btn btn-danger btn-sm remove-option">Remove</button>
                                            @endif
                                        </div>

                                        {{-- Hidden ID for existing options --}}
                                        <input type="hidden" name="options[{{ $index }}][id]"
                                            value="{{ $option->id }}">

                                        <div class="row gap-0">
                                            {{-- Option A --}}
                                            <div class="col-md-6 col-6 thumb-file">
                                                <div>
                                                    <div class="body-title mb-10">Image</div>
                                                    <input type="file" class="form-control dropify"
                                                        name="options[{{ $index }}][option_a_image]"
                                                        data-default-file="{{ asset($option->option_a_image) }}"
                                                        accept="image/*">
                                                </div>
                                                <div style="margin-top: 10px;">
                                                    <div class="body-title mb-10">Title</div>
                                                    <input class="flex-grow form-control" type="text" placeholder="Title"
                                                        name="options[{{ $index }}][option_a_name]"
                                                        value="{{ $option->option_a_name }}">
                                                </div>
                                            </div>

                                            {{-- Option B --}}
                                            <div class="col-md-6 col-6 thumb-file">
                                                <div>
                                                    <div class="body-title mb-10">Image</div>
                                                    <input type="file" class="form-control dropify"
                                                        name="options[{{ $index }}][option_b_image]"
                                                        data-default-file="{{ asset($option->option_b_image) }}"
                                                        accept="image/*">
                                                </div>
                                                <div style="margin-top: 10px;">
                                                    <div class="body-title mb-10">Title</div>
                                                    <input class="flex-grow form-control" type="text" placeholder="Title"
                                                        name="options[{{ $index }}][option_b_name]"
                                                        value="{{ $option->option_b_name }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </fieldset>

                            <button type="button" class="btn btn-success" id="add-option-btn"
                                style="font-size: 16px;padding: 10px;width: 150px;">
                                + Add Option
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            <div class="bot " style="justify-content:right;">
                <button class="tf-button w180 btn-add" type="submit" id="submitBtn">
                    <span class="btn-text">Save Changes</span>
                    <span class="loader spinner-border spinner-border-sm hidden" role="status"
                        aria-hidden="true"></span>
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
                $('.error_border').removeClass('error_border');

                let formData = new FormData(this);
                formData.append('_method', 'PUT');

                // Show loader
                $('#submitBtn .btn-text').text('Saving...');
                $('#submitBtn .loader').removeClass('hidden');
                $('#submitBtn').prop('disabled', true);

                $.ajax({
                    url: "{{ route('game.update', $data->id) }}",
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
        let optionIndex = 1;

        $('#add-option-btn').on('click', function() {
            const html = `
                <div class="option-item border p-3 mb-20 rounded">
                    <div class="d-flex justify-content-end mb-2">
                        <button type="button" class="btn btn-sm btn-danger remove-option">Remove</button>
                    </div>
                    <div class="row gap-0">
                        <div class="col-md-6 col-6 thumb-file">
                            <div>
                                <div class="body-title mb-10">Image</div>
                                <input type="file" class="form-control dropify"
                                    name="options[${optionIndex}][option_a_image]" accept="image/*">
                            </div>
                            <div style="margin-top: 10px;">
                                <div class="body-title mb-10">Title</div>
                                <input class="flex-grow form-control" type="text"
                                    placeholder="Title" name="options[${optionIndex}][option_a_name]" >
                            </div>
                        </div>
                        <div class="col-md-6 col-6 thumb-file">
                            <div >
                                <div class="body-title mb-10">Image</div>
                                <input type="file" class="form-control dropify"
                                    name="options[${optionIndex}][option_b_image]" accept="image/*">
                            </div>
                            <div style="margin-top: 10px;">
                                <div class="body-title mb-10">Title</div>
                                <input class="flex-grow form-control" type="text"
                                    placeholder="Title" name="options[${optionIndex}][option_b_name]" >
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('#options-container').append(html);
            $('.dropify').dropify();

            optionIndex++;
        });

        // remove option
        $(document).on('click', '.remove-option', function() {
            $(this).closest('.option-item').remove();
        });
    </script>
@endpush
