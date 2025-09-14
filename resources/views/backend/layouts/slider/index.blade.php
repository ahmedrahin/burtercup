@extends('backend.app')

@section('title', 'Add Banner Image')

@push('styles')
    <style>
        .dropify-wrapper {
            border: 2px dashed var(--Main);
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
            border: 2px solid #ced4da;
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
            font-size: 14px;
            margin-bottom: 13px;
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

        .body-title.head {
            color: var(--Main) !important;
            font-size: 16px;
        }

        .icon-calendar {
            font-size: 20px;
            color: #000000d6;
            position: absolute;
            right: 13px;
            top: 22%;
        }

        #errors-msgs li {
            margin-block: 15px;
        }

        #errors-msgs .block-warning {
            width: 50%;
        }

        .item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }

        .item .body-title {
            margin-bottom: 0 !important;
            width: 150px;
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
                    <div class="text-tiny">@yield('title')</div>
                </li>
            </ul>
        </div>

        <form id="imageUploadForm" class="mt-6" enctype="multipart/form-data">
            @csrf
            <div class="row">
                @if ($images->count() == 0)
                    <div class="col-md-3">
                        <div class="image-box">
                            <label for="image">Image</label>
                            <input type="file" class="form-control dropify" id="image" name="image"
                                accept="image/*">
                            @error('image')
                                <div style="color: red" class="mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @else
                    @foreach ($images as $index => $image)
                        <div class="col-md-3">
                            <div class="image-box mb-3">
                                <label for="image">Image - {{ $index + 1 }}</label>
                                <input type="file" class="form-control dropify" accept="image/*"
                                    data-default-file="{{ isset($image) && $image->image ? asset($image->image) : '' }}"
                                    data-image-id="{{ $image->id }}">
                                <div id="uploadStatus" style="margin-top: 10px;"></div>
                            </div>
                        </div>
                    @endforeach

                    <div class="col-md-3">
                        <div class="image-box">
                            <label for="new-image">Add New Image</label>
                            <input type="file" class="form-control dropify" id="image" name="image"
                                accept="image/*">
                            @error('image')
                                <div style="color: red" class="mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                @endif
            </div>
        </form>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var drEvent = $('.dropify').dropify({

            });


            $('#image').on('change', function() {
                var file = this.files[0];
                if (file) {
                    uploadImage(file);
                }
            });

            function uploadImage(file) {
                var formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('image', file);

                $.ajax({
                    url: "{{ route('slider-banner.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {

                        location.reload();
                    },
                    error: function() {

                    }
                });
            }
        });
    </script>

    <script>
        $(document).ready(function() {

            $('.dropify').on('dropify.afterClear', function(event, element) {
                var imageId = $(this).attr('data-image-id');
                if (imageId) {
                    removeImage(imageId, $(this));
                } else {
                    console.error("Image ID not found");
                }
            });

            function removeImage(imageId, inputElement) {
                $.ajax({
                    url: '{{ route('slider-banner.destroy', ':id') }}'.replace(':id', imageId),
                    type: "DELETE",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            inputElement.closest('.col-md-3').fadeOut(300, function() {
                                $(this).remove();
                            });
                        }
                    },
                    error: function() {
                        alert("Error deleting image.");
                    }
                });
            }
        });
    </script>
@endpush
