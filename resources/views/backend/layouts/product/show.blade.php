@extends('backend.app')

@section('title', 'Product Details')

@push('styles')

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .info-box {
            padding: 20px 0 0;
        }
        .info-box h4 {
            font-size: 16px;
            padding-bottom: 15px;
        }
        .info-box {
            font-size: 13px;
        }
        dd{
            color: black;
        }
        .infos {
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }
        .short-description p {
            line-height: 24px !important;
        }
        .variant-picker-item .variant-picker-values label{
            background-color: var(--Main);
            border-color: var(--Main) !important;
        }
        .body-title-2{
            color: white;
        }
    </style>
@endpush

@section('content')

    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Product Details</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="index.html"><div class="text-tiny">Dashboard</div></a>
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
                    <div class="text-tiny">{{ $product->name }}</div>
                </li>
            </ul>
        </div>


        <div class="wg-box">
            <div class="tf-main-product section-image-zoom flex">
                <div class="tf-product-media-wrap">
                    <div class="thumbs-slider">
                        <div class="swiper tf-product-media-thumbs other-image-zoom" data-direction="vertical">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="item">
                                        <img class="tf-image-zoom" data-zoom="" src="{{ asset($product->thumb_image) }}" alt="">
                                    </div>
                                </div>
                                @foreach($product->gellary_images as $image)
                                    <div class="swiper-slide">
                                        <div class="item">
                                            <img src="{{ asset($image->image) }}" alt="">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="swiper tf-product-media-main" id="gallery-swiper-started">
                            <div class="swiper-wrapper" >
                                <div class="swiper-slide">
                                    <div class="item">
                                        <a href="images/products/product-detail-1.png" target="_blank" data-pswp-width="506px" data-pswp-height="810px">
                                            <img class="tf-image-zoom" data-zoom="{{ asset($product->thumb_image) }}" src="{{ asset($product->thumb_image) }}" alt="">
                                        </a>
                                    </div>
                                </div>
                                @foreach($product->gellary_images as $image)
                                    <div class="swiper-slide">
                                        <div class="item">
                                            <a href="{{ asset($image->image) }}" target="_blank" data-pswp-width="500px" data-pswp-height="500px">
                                                <img class="tf-image-zoom" data-zoom="{{ asset($image->image) }}" src="{{ asset($image->image) }}" alt="">
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-button-next button-style-arrow thumbs-next"></div>
                            <div class="swiper-button-prev button-style-arrow thumbs-prev"></div>
                        </div>
                    </div>
                </div>


                <div class="tf-product-info-wrap relative flex-grow">
                    <div class="tf-zoom-main"></div>
                    <div class="tf-product-info-list other-image-zoom">
                        <div class="tf-product-info-title" style="margin-bottom: 25px;">
                            <h3>{{ $product->name }}</h3>

                            @php
                                $averageRating = round($product->reviews->avg('rating'), 1);
                                $fullStars = floor($averageRating);
                                $halfStar = ($averageRating - $fullStars) >= 0.5 ? 1 : 0;
                                $emptyStars = 5 - $fullStars - $halfStar;
                            @endphp

                            <div class="d-flex align-items-center rating my-4">
                                @for ($i = 0; $i < $fullStars; $i++)
                                    <i class="bi bi-star-fill text-warning me-2"></i>
                                @endfor
                                @if ($halfStar)
                                    <i class="bi bi-star-half text-warning me-2"></i>
                                @endif
                                @for ($i = 0; $i < $emptyStars; $i++)
                                    <i class="bi bi-star text-muted me-2"></i>
                                @endfor
                                <span class="ms-2 text-muted">({{ $product->reviews->count() }} reviews)</span>
                            </div>

                            <div class="price body-title">
                                ৳{{$product->offer_price}}
                                @if( $product->discount_option != 1 )
                                    <span class="text-danger" style="font-weight: 600;">
                                        <del>৳{{ number_format($product->base_price, 2) }}</del>
                                        @php
                                            $discountPercentage = round((($product->discount_amount) / $product->base_price) * 100);
                                        @endphp
                                        {{$discountPercentage}}% off
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="short-description" style="margin-bottom: 25px;">
                            @if( !is_null( $product->description))
                                {!! $product->description !!}
                            @endif
                        </div>


                        <div class="row infos">
                            <div class="row info-box">
                                <div class="col-md-6">
                                    <div class="product-details">
                                        <h4>General Information</h4>
                                        <dl class="row mb-0">
                                            <dt class="col-sm-6">Sku code:</dt>
                                            <dd class="col-sm-6">{{ $product->sku_code ?? 'N/A' }}</dd>

                                            <dt class="col-sm-6">Category:</dt>
                                            <dd class="col-sm-6">
                                                @if ($product->category)
                                                    {!! $product->category->name !!}
                                                    @if ($product->subcategory)
                                                        -> {!! $product->subcategory->name !!}
                                                        @if ($product->subsubcategory)
                                                            -> {!! $product->subsubcategory->name !!}
                                                        @endif
                                                    @endif
                                                @else
                                                    <span class="no">Uncategorized</span>
                                                @endif
                                            </dd>

                                            <dt class="col-sm-6">Brand:</dt>
                                            <dd class="col-sm-6">{{ $product->brand->name ?? 'N/A' }}</dd>

                                            <dt class="col-sm-6">Quantity:</dt>
                                            <dd class="col-sm-6">
                                                @if ($product->quantity > 0)
                                                    <span class="text-success">{{ $product->quantity }} pcs</span>
                                                @else
                                                    <span class="text-danger p-0">Out of stock!</span>
                                                @endif
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>

                            <div class="row info-box">
                                <div class="col-md-6">
                                    <div class="product-details">
                                        <h4>Others Information</h4>
                                        <dl class="row">
                                            <dt class="col-sm-6">Created at:</dt>
                                            <dd class="col-sm-6">{{ \Carbon\Carbon::parse($product->created_at)->format('M-d-Y h:i A') }}</dd>
                                            <dt class="col-sm-6">Last Updated:</dt>
                                            <dd class="col-sm-6">
                                                @if( $product->created_at != $product->updated_at )
                                                    {{ \Carbon\Carbon::parse($product->updated_at)->format('M-d-Y h:i A') }}
                                                @else
                                                    No update yet
                                                @endif
                                            </dd>
                                            <dt class="col-sm-6">Created by:</dt>
                                            <dd class="col-sm-6">
                                                @if( isset($product->user) )
                                                    <a href="{{ route('admin.show', optional($product->user)->id) }}" target="_blank">
                                                        {{ optional($product->user)->name ?? 'N/A' }}
                                                    </a>
                                                @endif
                                            </dd>
                                            @if( !is_null($product->expire_date) )
                                                <dt class="col-sm-6">Expire date:</dt>
                                                <dd class="col-sm-6 text-danger">
                                                    @if (\Carbon\Carbon::now()->lt($product->expire_date))
                                                        {{ \Carbon\Carbon::parse($product->expire_date)->format('M-d-Y h:i A') }}
                                                        ({{ \Carbon\Carbon::now()->diffForHumans($product->expire_date, [
                                                            'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
                                                            'parts' => 3, // Limit to days, hours, and minutes
                                                        ]) }})
                                                    @else
                                                        Expired
                                                    @endif
                                                </dd>

                                            @endif
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tf-product-info-variant-picker">
                            <div class="variant-picker-item">
                                <div class="variant-picker-label body-text">
                                    Size:
                                </div>
                                <div class="variant-picker-values">
                                    <label class="style-text" for="values-s" data-value="S">
                                        <div class="body-title-2">S</div>
                                    </label>

                                </div>
                            </div>
                        </div>

                        {{-- <div class="tf-product-info-buy-button">
                            <form class="">
                                <a href="#" class="tf-button flex-grow">Add to cart - $28.00</a>
                                <div class="tf-product-btn-wishlist">
                                    <i class="icon-heart"></i>
                                </div>
                                <div class="tf-product-btn-wishlist">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M20.0312 17.0306L17.0312 20.0306C16.8905 20.1713 16.6996 20.2503 16.5006 20.2503C16.3016 20.2503 16.1107 20.1713 15.97 20.0306C15.8292 19.8898 15.7502 19.699 15.7502 19.4999C15.7502 19.3009 15.8292 19.11 15.97 18.9693L17.6903 17.2499H4.50059C4.30168 17.2499 4.11091 17.1709 3.97026 17.0303C3.82961 16.8896 3.75059 16.6988 3.75059 16.4999C3.75059 16.301 3.82961 16.1103 3.97026 15.9696C4.11091 15.8289 4.30168 15.7499 4.50059 15.7499H17.6903L15.97 14.0306C15.8292 13.8898 15.7502 13.699 15.7502 13.4999C15.7502 13.3009 15.8292 13.11 15.97 12.9693C16.1107 12.8286 16.3016 12.7495 16.5006 12.7495C16.6996 12.7495 16.8905 12.8286 17.0312 12.9693L20.0312 15.9693C20.1009 16.039 20.1563 16.1217 20.194 16.2127C20.2318 16.3038 20.2512 16.4014 20.2512 16.4999C20.2512 16.5985 20.2318 16.6961 20.194 16.7871C20.1563 16.8782 20.1009 16.9609 20.0312 17.0306ZM6.96996 11.0306C7.1107 11.1713 7.30157 11.2503 7.50059 11.2503C7.69961 11.2503 7.89048 11.1713 8.03122 11.0306C8.17195 10.8898 8.25101 10.699 8.25101 10.4999C8.25101 10.3009 8.17195 10.11 8.03122 9.9693L6.3109 8.24993H19.5006C19.6995 8.24993 19.8903 8.17091 20.0309 8.03026C20.1716 7.88961 20.2506 7.69884 20.2506 7.49993C20.2506 7.30102 20.1716 7.11025 20.0309 6.9696C19.8903 6.82895 19.6995 6.74993 19.5006 6.74993H6.3109L8.03122 5.03055C8.17195 4.88982 8.25101 4.69895 8.25101 4.49993C8.25101 4.30091 8.17195 4.11003 8.03121 3.9693C7.89048 3.82857 7.69961 3.74951 7.50059 3.74951C7.30157 3.74951 7.1107 3.82857 6.96996 3.9693L3.96997 6.9693C3.90023 7.03896 3.84491 7.12168 3.80717 7.21272C3.76943 7.30377 3.75 7.40137 3.75 7.49993C3.75 7.59849 3.76943 7.69609 3.80717 7.78713C3.84491 7.87818 3.90023 7.9609 3.96997 8.03055L6.96996 11.0306Z" fill="#111111"/>
                                    </svg>
                                </div>
                                    <a href="#" class="tf-button style-1 w-full">Buy with <img src="images/item-background/paypal.png" alt=""></a>
                                    <a href="#" class="payment-more-option body-text">More payment options</a>
                            </form>
                        </div> --}}

                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection


@push('scripts')

    <script src="{{ asset('assets/js/zoom.js') }}"></script>
    <script src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/carousel.js') }}"></script>

@endpush

