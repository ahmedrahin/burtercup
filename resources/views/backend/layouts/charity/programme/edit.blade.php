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

         <form action="{{ route('programmes.update', $data->id) }}" class="form-add-new-user form-style-2" id="addform" enctype="multipart/form-data" method="POST">
            @csrf
            @method('PUT')
            <div class="wg-box">
                <div class="right flex-grow">

                    <div class="row">
                        <div class="col-md-3">
                            <fieldset class="name mb-14">
                                <div class="body-title mb-10">Image</div>
                                <input type="file" class="form-control dropify" id="image" name="image"
                                    accept="image/*" data-default-file="{{ isset($data) && $data->image ? asset($data->image) : '' }}">
                                @error('image')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </fieldset>
                        </div>
                        <div class="col-md-3">
                            <fieldset class="name mb-14">
                                <div class="body-title mb-10">Details Thumbnail</div>
                                <input type="file" class="form-control dropify" id="thumbnail" name="thumbnail"
                                    accept="image/*" data-default-file="{{ isset($data) && $data->thumbnail ? asset($data->thumbnail) : '' }}">
                                @error('thumbnail')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </fieldset>
                        </div>
                    </div>

                    <fieldset class="name mb-14">
                        <div class="body-title mb-10">Title</div>
                        <input class="flex-grow" type="text" placeholder="Title" name="title" value="{{ isset($data) ? $data->title : '' }}">
                        @error('title')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </fieldset>

                    <div class="row">
                        <div class="col-md-6">
                            <fieldset class="name mb-14">
                                <div class="body-title mb-10">Country Name</div>
                                <input class="flex-grow" type="text" placeholder="Country Name" name="country" value="{{ isset($data) ? $data->country : '' }}">
                                @error('country')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="name mb-14">
                                <div class="body-title mb-10">Select Programme Type</div>
                                <select name="type" id="">
                                    <option value="">Select Programme Type</option>
                                    <option value="digital" {{ isset($data) && $data->type == 'digital' ? 'selected' : '' }}>Digital</option>
                                    <option value="physical" {{ isset($data) && $data->type == 'physical' ? 'selected' : '' }}>Physical</option>
                                </select>
                                @error('type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </fieldset>
                        </div>
                    </div>

                    <fieldset class="content">
                        <div class="body-title mb-10">Description</div>
                        <textarea class="textarea-tinymce" name="description">{{ isset($data) ? $data->description : '' }}</textarea>
                        @error('description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
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
    <script src="{{ asset('assets/js/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('assets/js/tinymce/tinymce-custom.js') }}"></script>
     <script>
         $(document).ready(function() {
            $('.dropify').dropify();
        });
    </script>

@endpush
