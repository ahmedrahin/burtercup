<div class="row variant-box">
    {{-- Size Section --}}
    <div class="col-md-4">
        <div class="wg-box">
            <div class="d-flex align-items-center justify-between mb-4 top">
                <h3>Select Opacity</h3>
            </div>
            <div class="right flex-grow">
                <div class="row gx-3 gy-1">
                    @foreach($sizes as $size)
                        @php $isChecked = in_array($size->size_value, $selectedSizes); @endphp
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <label class="position-relative bg-light rounded-3 border shadow-sm p-3 text-center size-card hover-shadow d-block selectable-card {{ $isChecked ? 'active' : '' }}">
                                <input type="checkbox"
                                       name="sizes[]"
                                       value="{{ $size->size_value }}"
                                       class="d-none toggle-check"
                                       {{ $isChecked ? 'checked' : '' }}>
                                <h5 class="mb-0 text-primary">{{ $size->size_value }}</h5>
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
                <input type="text" name="length_cm" style="flex: 1;" value="{{ $options->length_cm }}">
            </div>
            <div class="item">
                <div class="body-title mb-10" style="margin: 0;">INC:</div>
                <input type="text" name="length_in" style="flex: 1;" value="{{ $options->length_in }}">
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
                <input type="text" name="width_in" style="flex: 1;" value="{{ $options->width_in }}">
            </div>
            <div class="item">
                <div class="body-title mb-10" style="margin: 0;">INC:</div>
                <input type="text" name="width_cm" style="flex: 1;" value="{{ $options->width_cm }}">
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
                <input type="text" name="height_in" style="flex: 1;" value="{{ $options->height_in }}">
            </div>
            <div class="item">
                <div class="body-title mb-10" style="margin: 0;">INC:</div>
                <input type="text" name="height_cm" style="flex: 1;" value="{{ $options->height_cm }}">
            </div>
        </div>
    </div>


</div>


