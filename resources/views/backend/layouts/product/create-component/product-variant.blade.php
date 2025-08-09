<div class="row variant-box">
    <div class="col-md-4">
        <div class="wg-box">
            <div class="d-flex align-items-center justify-between mb-4 top">
                <h3>Select Opacity</h3>
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
</div>
