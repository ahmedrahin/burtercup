

<div class="widget-tabs">
    <div class="">
        <ul class="widget-menu-tab">
            <li class="item-title active">
                <span class="inner"><span class="h6">Genarel</span></span>
            </li>
            <li class="item-title">
                <span class="inner"><span class="h6">Product Variant</span></span>
            </li>
            <li class="item-title">
                <span class="inner"><span class="h6">Product Details</span></span>
            </li>
        </ul>
    </div>

    <div class="widget-content-tab">
        <div class="widget-content-inner active">
            <div class="wg-box" style="margin-bottom:30px;">
                <fieldset class="name mb-4">
                    <div class="body-title mb-10">Product Name <span class="tf-color-1">*</span></div>
                    <input class="" type="text" placeholder="Name"  name="name" value="{{ $data->name }}">
                    <div class="text-danger error"></div>
                </fieldset>

                <fieldset class="name mb-4">
                    <div class="body-title mb-10">Product Quantity <span class="tf-color-1">*</span></div>
                    <input class="" type="text" placeholder="Quantity" name="quantity" value="{{ $data->quantity }}">
                    <div class="text-danger error"></div>
                </fieldset>

                <fieldset class="name mb-4">
                    <div class="body-title mb-10">Sku Code</div>
                    <input class="" type="text" placeholder="Sku Code" name="sku" value="{{ $data->sku_code }}">
                    <div class="text-danger error"></div>
                </fieldset>

                <fieldset class="content">
                    <div class="body-title mb-10">Description</div>
                    <textarea class="textarea-tinymce" name="description">{!! $data->description !!}</textarea>
                </fieldset>
            </div>

            <div class="wg-box" style="margin-bottom:30px;">
                <div class="flex gap10 items-center justify-between" style="padding-bottom: 8px;">
                    <div class="body-title head">Product Pricing</div>
                </div>
                <div class="divider" style="margin-bottom: 10px;"></div>
                <div>
                    <fieldset class="name mb-14">
                        <div class="body-title mb-10">Product Price <span class="tf-color-1">*</span></div>
                        <input class="" type="text" placeholder="Base Price" name="base_price" value="{{ $data->base_price ?? '' }}">
                        <div class="text-danger error"></div>
                    </fieldset>

                    <fieldset class="name mb-14">
                        <div class="body-title mb-10">Discount Type</div>
                        <select id="discount_type" name="discount_option">
                            <option value="1" {{ (isset($data) && $data->discount_option == 1) ? 'selected' : '' }}>No Discount</option>
                            <option value="2" {{ (isset($data) && $data->discount_option == 2) ? 'selected' : '' }}>Percentage %</option>
                            <option value="3" {{ (isset($data) && $data->discount_option == 3) ? 'selected' : '' }}>Flat Discount</option>
                        </select>
                    </fieldset>

                    <fieldset class="name" id="product_discount_no">
                        <div class="body-title mb-10">Discount Amount <span class="tf-color-1">*</span></div>
                        <input type="number" name="discount_percentage_or_flat_amount" placeholder="Discount Amount" value="{{ $data->discount_percentage_or_flat_amount ?? '' }}" />
                        <div class="text-danger error"></div>
                    </fieldset>
                </div>
            </div>

            <div class="wg-box">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="body-title head">Product Gallery Image</div>
                </div>
                <div class="divider mb-2"></div>

                <div>
                    <input type="file" id="images" name="images[]" accept="image/*" multiple class="form-control" />
                    <p class="form-text text-muted mt-1">You can select multiple images.</p>
                    <div class="text-danger error mt-1"></div>

                    <input type="hidden" name="removed_image_ids" id="removedImageIds" value="">

                    <div id="existingGalleryImages" class="preview-wrapper mt-3">
                        @foreach($data->gellary_images as $image)
                            <div class="preview-item" data-id="{{ $image->id }}">
                                <img src="{{ asset($image->image) }}" />
                                <button type="button" class="remove-btn existing-remove" data-id="{{ $image->id }}">&times;</button>
                            </div>
                        @endforeach
                    </div>

                    <!-- New uploaded preview -->
                    <div id="imagePreviewContainer" class="preview-wrapper mt-3"></div>
                </div>
            </div>

        </div>

        <div class="widget-content-inner">
            @include('backend.layouts.product.edit-component.product-variant')
        </div>

        <div class="widget-content-inner ">
            <div class="wg-table table-revision-history">
                @include('backend.layouts.product.create-component.product-details')
            </div>
        </div>
    </div>

</div>

