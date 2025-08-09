<div class="wg-box">
    <div>
        <div class="body-title mb-10">Product Image</div>
        <input type="file" class="form-control dropify" id="image" name="image" accept="image/*">
        <div class="error text-danger"></div>
    </div>
</div>


<div class="wg-box">
    <div>
        <div class="body-title mb-10">Tags</div>
        <div class="tag-input-wrapper">
            <ul class="tag-list" id="tagList">
                <!-- Tags will render here -->
                <li class="tag-input-wrapper"><input type="text" id="tagInput" placeholder="Type and press Enter..." /></li>
            </ul>

            <input type="hidden" name="tags" id="hiddenTags">
        </div>
    </div>
</div>

<div class="wg-box">
    <div>
        <div class="body-title mb-10">Status</div>
        <div class="select">
            <select class="" name="status">
                <option value="1" selected>Publish</option>
                <option value="0">Inactive</option>
            </select>
        </div>
    </div>
</div>

<div class="wg-box">
    <div>
        <div class="body-title mb-14">Expire Date <span class="text-muted" style="font-weight: 400;">(if need)</span> </div>
        <div style="position: relative;">
            <input type="date" name="expire_date" id="expire_date"  placeholder="Select Expire Date & Time" autocomplete="off">
            <div class="error text-danger"></div>
            <i class="icon-calendar"></i>
        </div>
    </div>
</div>

<div class="mt-2">
    <div class="bot " style="justify-content:right;">
        <button class="tf-button w180 btn-add" type="submit" id="submitBtn" style="width: 100%;">
            <span class="btn-text">Publish</span>
            <span class="loader spinner-border spinner-border-sm hidden" role="status" aria-hidden="true"></span>
        </button>
    </div>
</div>
