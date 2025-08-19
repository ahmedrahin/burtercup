<div class="wg-box">

    <div>
        <div class="body-title mb-10">Product Image</div>
        <input type="file" class="form-control dropify" id="image" name="image" accept="image/*" data-default-file="{{ isset($data) && $data->thumb_image ? asset($data->thumb_image) : '' }}">
        <div class="error text-danger"></div>
    </div>
</div>


<div class="wg-box">
    <div>
        <div class="body-title mb-10">Tags</div>
        <div class="tag-input-wrapper">
            <ul class="tag-list" id="tagList">
                <!-- Tags will render here -->
                @foreach ($data->tags as $tag)
                    <li class="tag-item">
                        {{ $tag->name }}
                        <span class="remove-tag" onclick="removeTag({{ $loop->index }})">&times;</span>
                    </li>
                @endforeach
                <!-- Add new tag input -->
                <li class="tag-input-wrapper">
                    <input type="text" id="tagInput" placeholder="Type and press Enter..." />
                </li>
            </ul>

            <input type="hidden" name="tags" id="hiddenTags" value="{{ implode(',', $data->tags->pluck('name')->toArray()) }}">
        </div>
    </div>
</div>

<div class="wg-box">
    <div>
        <div class="body-title mb-10">Status</div>
        <div class="select">
            <select class="" name="status">
                <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>Publish</option>
                <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </div>
</div>

<div class="wg-box">
    <div>
        <div class="body-title mb-14">Expire Date <span class="text-muted" style="font-weight: 400;">(if need)</span> </div>
        <div style="position: relative;">
            @if (is_null($data->expire_date) || $data->expire_date > now())
                <input type="date" name="expire_date" id="expire_date"  placeholder="Select Expire Date & Time" autocomplete="off" value="{{ \Carbon\Carbon::parse($data->expire_date)->format('Y-m-d H:i') }}">
            @else
                <input type="date" name="expire_date" id="expire_date"  placeholder="Select Expire Date & Time" autocomplete="off">
            @endif
            <div class="error text-danger"></div>
            <i class="icon-calendar"></i>
        </div>
    </div>
</div>

<div class="mt-2">
    <div class="bot " style="justify-content:right;">
        <button class="tf-button w180 btn-add" type="submit" id="submitBtn" style="width: 100%;">
            <span class="btn-text">Save Changes</span>
            <span class="loader spinner-border spinner-border-sm hidden" role="status" aria-hidden="true"></span>
        </button>
    </div>
</div>
