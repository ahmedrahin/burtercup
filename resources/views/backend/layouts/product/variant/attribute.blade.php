@extends('backend.app')

@section('title', 'Product Attribute')

@push('styles')
    <style>
        h5{
            font-size: 16px;
        }
        .form-control:focus, button:focus {
            box-shadow: 0 !important;
            box-shadow: none !important;
        }
        label{
            color: #525151;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px !important;
            display: block;
        }
        input {
            padding: 11px 22px !important;
        }
        .top {
            border-bottom: 1px solid #ebebebe6;
            padding-bottom: 8px;
            margin-bottom: 8px !important;
        }
        .modal-footer button {
            font-size: 12px;
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

        <div class="row">
            <div class="col-md-4">
                <div class="wg-box">
                    <div class="d-flex align-items-center justify-between mb-4 top">
                        <h3>Opacity</h3>
                        <div>
                            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addSizeModal" style="font-size: 13px;">
                                <i class="icon-plus"></i> Add new
                            </button>
                        </div>
                    </div>

                    <div class="right flex-grow">
                        <div class="row g-3">
                            @foreach($sizes as $size)
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="position-relative bg-light rounded-3 border shadow-sm p-3 text-center size-card hover-shadow">
                                        <h5 class="mb-0 text-primary">{{ $size->size_value }}</h5>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('attribute.destroy', $size->id) }}" method="POST" class="position-absolute top-0 end-0 m-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm text-danger p-0" onclick="return confirm('Are you sure you want to delete this size?')">
                                                <i class="icon-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>


            </div>

            <div class="col-md-4">
                <div class="wg-box">
                    <div class="d-flex align-items-center justify-between mb-4 top">
                        <h3>Color</h3>
                        <div>
                            <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addColorModal" style="font-size: 13px;">
                                <i class="icon-plus"></i> Add new
                            </button>
                        </div>
                    </div>

                    <div class="right flex-grow">
                        <div class="row g-3">
                            @foreach($colors as $color)
                                <div class="col-sm-6 col-md-4 col-lg-3">
                                    <div class="position-relative bg-light rounded-3 border shadow-sm p-3 text-center size-card hover-shadow">
                                        <h5 class="mb-0 text-primary">{{ $color->color_value }}</h5>

                                        {{-- Delete Button --}}
                                        <form action="{{ route('attribute.destroy', $color->id) }}" method="POST" class="position-absolute top-0 end-0 m-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm text-danger p-0" onclick="return confirm('Are you sure you want to delete this color?')">
                                                <i class="icon-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>


            </div>
        </div>

    </div>

    <!-- Add New Size Modal -->
    <div class="modal fade" id="addSizeModal" tabindex="-1" aria-labelledby="addSizeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="addColorForm" method="POST" style="width: 100%;">
                @csrf
                <div class="modal-content rounded-4" style="background-color: #ffffff; border-radius: 1rem;">
                    <!-- Modal Header -->
                    <div class="modal-header" style="background-color: #f9f9f9; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                        <h5 class="modal-title text-dark fw-semibold" id="addSizeModalLabel">
                            <i class="fas fa-ruler-combined me-2" style="color: #00796b;"></i> Add Opacity
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="color: #00796b;"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body" style="background-color: #f9f9f9;">
                        <div class="mb-3">
                            <label for="sizeInput" class="form-label fw-medium" style="color: #555;">Opacity</label>
                            <input type="text" class="form-control form-control-lg rounded-3" id="sizeInput" name="value" placeholder="e.g. 10, 20"  style="border: 1px solid #ced4da; background-color: #fafafa; color: #495057;">
                            <div class="text-danger error-value mt-1"></div>
                            <input type="hidden" name="size" value="1">
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer" style="background-color: #f9f9f9; border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" >Cancel</button>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fas fa-check-circle me-1"></i> Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Add New Color Modal -->
    <div class="modal fade" id="addColorModal" tabindex="-1" aria-labelledby="addSizeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('attribute.store') }}" method="POST" style="width: 100%;">
                @csrf
                <div class="modal-content rounded-4" style="background-color: #ffffff; border-radius: 1rem;">
                    <!-- Modal Header -->
                    <div class="modal-header" style="background-color: #f9f9f9; border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                        <h5 class="modal-title text-dark fw-semibold" id="addSizeModalLabel">
                            <i class="fas fa-ruler-combined me-2" style="color: #00796b;"></i> Add New Color
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="color: #00796b;"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body" style="background-color: #f9f9f9;">
                        <div class="mb-3">
                            <label for="color" class="form-label fw-medium" style="color: #555;">Color Name</label>
                            <input type="text" class="form-control form-control-lg rounded-3" id="color" name="value" placeholder="e.g. red, blue, white" style="border: 1px solid #ced4da; background-color: #fafafa; color: #495057;">
                            <div class="text-danger error-value mt-1"></div>

                            <label for="sizeInput" class="form-label fw-medium" style="color: #555;padding-top:15px;">Color Code</label>
                            <input type="color" class="form-control form-control-lg rounded-3" id="sizeInput" name="color_code"  style="border: 1px solid #ced4da; background-color: #fafafa; color: #495057;">
                            <input type="hidden" name="color" value="1">
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer" style="background-color: #f9f9f9; border-bottom-left-radius: 1rem; border-bottom-right-radius: 1rem;">
                        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" >Cancel</button>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="fas fa-check-circle me-1"></i> Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection


@push('scripts')
<script>
    $(document).ready(function () {
        // Size form submission
        $('#addSizeForm').on('submit', function (e) {
            e.preventDefault();
            $('.error-value').text(''); // clear previous error
            let form = $(this);
            let submitBtn = form.find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: "{{ route('attribute.store') }}",
                type: "POST",
                data: form.serialize(),
                success: function (response) {
                    $('#addSizeModal').modal('hide');
                    form[0].reset();
                    location.reload(); // Or dynamically update list
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.value) {
                            form.find('.error-value').text(errors.value[0]);
                        }
                    }
                },
                complete: function () {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Save');
                }
            });
        });

        // Color form submission
        $('#addColorForm').on('submit', function (e) {
            e.preventDefault();
            $('.error-value').text('');
            let form = $(this);
            let submitBtn = form.find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: "{{ route('attribute.store') }}",
                type: "POST",
                data: form.serialize(),
                success: function (response) {
                    $('#addColorModal').modal('hide');
                    form[0].reset();
                    location.reload(); // Or dynamically update list
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.value) {
                            form.find('.error-value').text(errors.value[0]);
                        }
                    }
                },
                complete: function () {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-check-circle me-1"></i> Save');
                }
            });
        });
    });
</script>
@endpush


