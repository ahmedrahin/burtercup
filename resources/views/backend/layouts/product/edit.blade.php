@extends('backend.app')

@section('title', 'Edit Product')

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
        display: flex; align-items: center; gap: 10px;
        margin-bottom: 15px;
       }
       .item .body-title {
        margin-bottom: 0 !important;
        width: 150px;
       }
    </style>

    {{-- tags --}}
    <style>
        .tag-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            padding: 8px;
            border-radius: 12px;
            min-height: 48px;
            align-items: flex-start;
            position: relative;
            border: 1px solid var(--Input);
            align-items: center;
        }

        .tag-item {
            background-color: #e0f2fe;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .remove-tag {
            cursor: pointer;
            font-weight: bold;
            color: #0c4a6e;
            margin-left: 4px;
        }

        .tag-input-wrapper {
            flex-grow: 1;
            min-width: 150px;
            /* margin-top: 6px; */
        }

        #tagInput {
            width: 100%;
            border: none;
            outline: none;
            font-size: 14px;
            padding: 6px 4px !important;
            background-color: transparent;
        }

        #tagInput::placeholder {
            color: #aaa;
        }

        @media screen and (max-width: 576px) {
            .tag-item {
                font-size: 13px;
            }
        }
    </style>

    <style>
        .preview-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 10px;
        }

        .preview-wrapper .preview-item {
            position: relative;
            width: 110px;
            height: 110px;
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .preview-wrapper .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-wrapper .remove-btn {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 18px;
            height: 18px;
            background: #dc3545;
            color: white;
            font-size: 14px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            line-height: 12px;
            text-align: center;
            padding: 0;
        }

    </style>

     {{-- variant --}}
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
            padding-bottom: 5px;
            margin-bottom: 8px !important;
        }
        .modal-footer button {
            font-size: 12px;
        }
        .selectable-card {
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .selectable-card.active {
            background-color: #198754 !important;
        }
        .selectable-card.active h5 {
            color: #fff !important;
            opacity: 1;
        }
        .variant-box h3 {
            font-size: 18px;

        }
        .variant-box h5 {
            opacity: .7;
            font-weight: 500;
            font-size: 14px;
            color: black !important;
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
                    <a href="{{ route('dashboard') }}"><div class="text-tiny">Dashboard</div></a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <a href="{{ route('product.index') }}"><div class="text-tiny">Product</div></a>
                </li>
                <li>
                    <i class="icon-chevron-right"></i>
                </li>
                <li>
                    <div class="text-tiny">@yield('title')</div>
                </li>
            </ul>
        </div>

        <div id="errors">
            <ul id="errors-msgs">

            </ul>
        </div>

        <!-- new-page-wrap -->
        <form class="form-new-page"  action="{{ route('product.update', $data->id) }}" method="POST" enctype="multipart/form-data"  >
            @csrf
            @method('PUT')
            <div class="new-page-wrap">
                <div class="left">
                    @include('backend.layouts.product.edit-component.left-side')
                </div>

                <div class="right">
                    @include('backend.layouts.product.edit-component.right-side')
                </div>

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

            $('#addform').on('submit', function(e) {
                e.preventDefault();
                $('.error').html('');

                var formData = new FormData(this);

                // Show loader
                $('#submitBtn .btn-text').text('Saving...');
                $('#submitBtn .loader').removeClass('hidden');
                $('#submitBtn').prop('disabled', true);

                $.ajax({
                    url: "{{ route('product.update', $data->id) }}",
                    method: "PUT",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
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

                        $('#errors-msgs').empty();
                        $('#errors-msgs').css('display', 'block');
                        $('#errors-msgs').css('margin-bottom', '30px');

                        let scrolled = false;
                        if (!scrolled) {
                            $('html, body').animate({
                                scrollTop: $('#errors-msgs').offset().top - 70
                            }, 500);
                            scrolled = true;
                        }

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

                                if (inputField.hasClass('select2')) {
                                    inputField.closest('div').find('.error').text(val[0]);
                                    inputField.next('.select2').addClass('error_border');
                                } else {
                                    inputField.addClass('error_border');
                                    let errorDiv = inputField.closest('fieldset').find('.error');
                                    errorDiv.text(val[0]);
                                }
                            });
                        } else {
                            $('#errors-msgs').append(`
                                <li>
                                    <div class="block-warning">
                                        <i class="icon-alert-octagon"></i>
                                        <div class="body-title-2">Something went wrong! Please try again.</div>
                                    </div>
                                </li>
                            `);
                        }
                    }

                });
            });
        });
    </script>

    {{-- product price --}}
    <script>
       $(document).ready(function() {
        const initialDiscountOption = @json($data->discount_option);
        const initialDiscountValue = @json($data->discount_percentage_or_flat_amount);

        const updateDiscountInput = () => {
            const discountValue = $('#discount_type').val();
            const discountInput = $('input[name="discount_percentage_or_flat_amount"]');

            // Show or hide the discount input based on selected option
            if (discountValue == "1") {
                $('#product_discount_no').hide();
                discountInput.val('');
            } else {
                $('#product_discount_no').show();
                if (discountValue == "2") {
                    discountInput.attr('placeholder', 'Percentage Amount');
                    if (initialDiscountOption == 2) {
                        discountInput.val(initialDiscountValue);
                    }
                } else if (discountValue == "3") {
                    discountInput.attr('placeholder', 'Flat Amount');
                    if (initialDiscountOption == 3) {
                        discountInput.val(initialDiscountValue);
                    }
                }
            }
        };

        // Update the discount input when the discount type changes
        $('#discount_type').change(function() {
            const discountValue = $(this).val();
            const discountInput = $('input[name="discount_percentage_or_flat_amount"]');

            if (discountValue != initialDiscountOption) {
                discountInput.val('');
            }

            updateDiscountInput();
        });

        updateDiscountInput();
    });
    </script>

    {{-- category or sub-category --}}
    <script>
        let baseSubcategoryUrl = "{{ url('/admin/get-subcategories') }}";
        $('#categorySelect').on('change', function () {
            var categoryId = $(this).val();
            $('#subcategorySelect').empty().append('<option value="">Select a sub category</option>');

            if (categoryId) {
                var url = baseSubcategoryUrl + '/' + categoryId;

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        if (data.length > 0) {
                            $.each(data, function (key, subcategory) {
                                $('#subcategorySelect').append(
                                    '<option value="' + subcategory.id + '">' + subcategory.name + '</option>'
                                );
                            });
                        } else {
                            $('#subcategorySelect').append('<option value="">No subcategories available</option>');
                        }
                    },
                    error: function (xhr) {
                        console.error("Error fetching subcategories", xhr.responseText);
                    }
                });
            }
        });

        $(document).ready(function() {
            var categoryId = $('#categorySelect').val();
            if (categoryId) {
                var url = baseSubcategoryUrl + '/' + categoryId;

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        $.each(data, function (key, subcategory) {
                            $('#subcategorySelect').append(
                                '<option value="' + subcategory.id + '" ' + (subcategory.id == "{{ old('subcategory', $data->subcategory_id) }}" ? 'selected' : '') + '>' + subcategory.name + '</option>'
                            );
                        });
                    }
                });
            }
        });
    </script>

    {{-- tags --}}
    <script>
        let maxTags = 10;
        let tags = @json($data->tags->pluck('name')->toArray());

        const tagInput = document.getElementById("tagInput");
        const tagList = document.getElementById("tagList");
        const hiddenTagsInput = document.getElementById("hiddenTags");

        function renderTags() {
            tagList.innerHTML = "";

            tags.forEach((tag, index) => {
                let li = document.createElement("li");
                li.classList.add("tag-item");
                li.innerHTML = `
                    ${tag}
                    <span class="remove-tag" onclick="removeTag(${index})">&times;</span>
                `;
                tagList.appendChild(li);
            });

            const inputLi = document.createElement("li");
            inputLi.classList.add("input-tag");
            inputLi.appendChild(tagInput);
            tagList.appendChild(inputLi);
            tagInput.focus();
            updateHiddenTags();
        }

        function removeTag(index) {
            tags.splice(index, 1);
            renderTags();
        }

        tagInput.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                let tag = tagInput.value.trim();

                if (tag === "") return;

                if (tags.includes(tag)) {
                    ajaxMessage('The tag is already included.', 'info');
                    tagInput.value = "";
                    return;
                }

                if (tags.length >= maxTags) {
                    ajaxMessage('You can only add up to 10 tags.', 'info');
                    tagInput.value = "";
                    return;
                }

                tags.push(tag);
                tagInput.value = "";

                renderTags();
            }
        });

        function updateHiddenTags() {
            hiddenTagsInput.value = tags.join(',');
        }

        renderTags();
    </script>

    {{-- gellary images --}}
    <script>
        let removedImageIds = [];

        document.querySelectorAll('.existing-remove').forEach(button => {
            button.addEventListener('click', function () {
                const imageId = this.getAttribute('data-id');
                removedImageIds.push(imageId);
                document.getElementById('removedImageIds').value = removedImageIds.join(',');
                this.parentElement.remove();
            });
        });

        // Preview new uploads
        document.getElementById('images').addEventListener('change', function (event) {
            const files = event.target.files;
            const previewContainer = document.getElementById('imagePreviewContainer');
            previewContainer.innerHTML = '';

            Array.from(files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'preview-item';

                    const img = document.createElement('img');
                    img.src = e.target.result;

                    const removeBtn = document.createElement('button');
                    removeBtn.innerHTML = '&times;';
                    removeBtn.className = 'remove-btn';

                    removeBtn.addEventListener('click', function () {
                        const dt = new DataTransfer();
                        Array.from(files).forEach((f, i) => {
                            if (i !== index) dt.items.add(f);
                        });
                        event.target.files = dt.files;
                        wrapper.remove();
                        document.getElementById('images').dispatchEvent(new Event('change'));
                    });

                    wrapper.appendChild(img);
                    wrapper.appendChild(removeBtn);
                    previewContainer.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.toggle-check').forEach(input => {
                const label = input.closest('.selectable-card');

                // Add active class initially if checked
                if (input.checked) {
                    label.classList.add('active');
                }

                // Toggle active class on change
                input.addEventListener('change', function () {
                    label.classList.toggle('active', this.checked);
                });
            });
        });
    </script>


@endpush

