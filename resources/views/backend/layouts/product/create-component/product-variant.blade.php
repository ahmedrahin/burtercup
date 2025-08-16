<div class="row variant-box">
    <div class="col-md-4">
        <div class="wg-box">
            <div class="d-flex align-items-center justify-between mb-4 top">
                <h3>Select Size</h3>
            </div>

            <div class="right flex-grow">
                <div class="row gx-3 gy-1">
                    @foreach($sizes as $size)
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <label class="position-relative bg-light rounded-3 border shadow-sm p-3 text-center size-card hover-shadow d-block selectable-card">
                                <input type="checkbox" name="sizes[]" value="{{ $size->size_value }}" class="d-none toggle-check">
                                <h5 class="mb-0 text-primary">{{ $size->size_value }}</h5>
                            </label>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="wg-box">
            <div class="d-flex align-items-center justify-between mb-4 top">
                <h3>Select Color</h3>
            </div>

            <div class="right flex-grow">
                <div class="row gx-3 gy-1">
                    @foreach($colors as $color)
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <label class="position-relative bg-light rounded-3 border shadow-sm p-3 text-center size-card hover-shadow d-block selectable-card">
                                <input type="checkbox" name="colors[]" value="{{ $color->color_value }}" class="d-none toggle-check">
                                <h5 class="mb-0 text-primary">{{ $color->color_value }}</h5>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>


    </div>

    <div class="wg-box" style="margin: 30px 0 0;">
        <div class="flex gap10 items-center justify-between" style="padding-bottom: 8px;">
            <div class="body-title head">Length</div>
        </div>
        <div class="divider" style="margin-bottom: 10px;"></div>
        <div>
            <div class="item">
                <div class="body-title mb-10" style="margin: 0;">CM:</div>
                <input type="text" name="length_cm" style="flex: 1;">
            </div>
            <div class="item">
                <div class="body-title mb-10" style="margin: 0;">INC:</div>
                <input type="text" name="length_in" style="flex: 1;">
            </div>
        </div>
    </div>

    <div class="wg-box" style="margin: 30px 0 0;">
        <div class="flex gap10 items-center justify-between" style="padding-bottom: 8px;">
            <div class="body-title head">Width</div>
        </div>
        <div class="divider" style="margin-bottom: 10px;"></div>
        <div>
            <div class="item">
                <div class="body-title mb-10" style="margin: 0;">CM:</div>
                <input type="text" name="width_in" style="flex: 1;">
            </div>
            <div class="item">
                <div class="body-title mb-10" style="margin: 0;">INC:</div>
                <input type="text" name="width_cm" style="flex: 1;">
            </div>
        </div>
    </div>

    <div class="wg-box" style="margin: 30px 0 0;">
        <div class="flex gap10 items-center justify-between" style="padding-bottom: 8px;">
            <div class="body-title head">Height</div>
        </div>
        <div class="divider" style="margin-bottom: 10px;"></div>
        <div>
            <div class="item">
                <div class="body-title mb-10" style="margin: 0;">CM:</div>
                <input type="text" name="height_in" style="flex: 1;">
            </div>
            <div class="item">
                <div class="body-title mb-10" style="margin: 0;">INC:</div>
                <input type="text" name="height_cm" style="flex: 1;">
            </div>
        </div>
    </div>

</div>
