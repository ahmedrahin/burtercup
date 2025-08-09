<div class="wg-box" style="margin-bottom: 30px;">
    <div class="flex gap10 items-center justify-between" style="padding-bottom: 8px;">
        <div class="body-title head">Frame Measurements</div>
    </div>
    <div class="divider" style="margin-bottom: 10px;"></div>
    <div>
        <div class="item">
            <div class="body-title mb-10" style="margin: 0;">Frame Width:</div>
            <input type="text" name="frame_width" style="flex: 1;" value="{{ $data->measurements->frame_width ?? '' }}">
        </div>
        <div class="item">
            <div class="body-title mb-10" style="margin: 0;">Bridge:</div>
            <input type="text" name="bridge" style="flex: 1;" value="{{ $data->measurements->bridge ?? '' }}">
        </div>
        <div class="item">
            <div class="body-title mb-10" style="margin: 0;">Lens Width:</div>
            <input type="text" name="lens_width" style="flex: 1;" value="{{ $data->measurements->lens_width ?? '' }}">
        </div>
        <div class="item">
            <div class="body-title mb-10" style="margin: 0;">Lens Height:</div>
            <input type="text" name="lens_height" style="flex: 1;" value="{{ $data->measurements->lens_height ?? '' }}">
        </div>
        <div class="item">
            <div class="body-title mb-10" style="margin: 0;">Temple Length:</div>
            <input type="text" name="temple_length" style="flex: 1;" value="{{ $data->measurements->temple_length ?? '' }}">
        </div>
    </div>
</div>

<div class="wg-box">
    <div class="flex gap10 items-center justify-between" style="padding-bottom: 8px;">
        <div class="body-title head">Frame Details</div>
    </div>
    <div class="divider" style="margin-bottom: 10px;"></div>
    <div>
        <div class="item">
            <div class="body-title mb-10" style="margin: 0;">Size:</div>
            <input type="text" name="size" style="flex: 1;" value="{{ $data->frameDetails->size ?? '' }}">
        </div>
        <div class="item">
            <div class="body-title mb-10" style="margin: 0;">Material:</div>
            <input type="text" name="material" style="flex: 1;" value="{{ $data->frameDetails->material ?? '' }}">
        </div>
        <div class="item">
            <div class="body-title mb-10" style="margin: 0;">Shape:</div>
            <input type="text" name="shape" style="flex: 1;" value="{{ $data->frameDetails->shape ?? '' }}">
        </div>
        <div class="item">
            <div class="body-title mb-10" style="margin: 0;">Gender:</div>
            <input type="text" name="gender" style="flex: 1;" value="{{ $data->frameDetails->gender ?? '' }}">
        </div>
        <div class="item">
            <div class="body-title mb-10" style="margin: 0;">Type:</div>
            <input type="text" name="type" style="flex: 1;" value="{{ $data->frameDetails->type ?? '' }}">
        </div>
    </div>
</div>
