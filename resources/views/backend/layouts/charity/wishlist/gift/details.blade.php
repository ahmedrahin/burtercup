@extends('backend.app')

@section('title', $data->name . ' Gifts Information')

@push('styles')
    <style>
        :root {
            --primary: #7c3aed;
            --primary-light: #a78bfa;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-500: #6b7280;
            --gray-700: #374151;
            --gray-900: #111827;
        }
        h1{
            line-height: inherit;
        }
        .main-content-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--gray-200);
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 24px;
            font-weight: 700;
            color: var(--gray-900);
        }

        .page-title svg {
            width: 28px;
            height: 28px;
            color: var(--primary);
        }

        .card-box {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--gray-200);
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .card-box:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--gray-100);
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-title svg {
            width: 20px;
            height: 20px;
            color: var(--primary);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }

        .info-item {
            background: var(--gray-50);
            border-radius: 12px;
            padding: 16px;
            transition: all 0.2s ease;
        }

        .info-item:hover {
            background: var(--gray-100);
            transform: translateY(-2px);
        }

        .info-title {
            font-size: 13px;
            font-weight: 500;
            color: var(--gray-500);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: var(--gray-900);
        }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }

        .image-item {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 1;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .image-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .image-item:hover img {
            transform: scale(1.05);
        }

        .no-images {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px 20px;
            color: var(--gray-500);
            background: var(--gray-50);
            border-radius: 12px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .status-approved {
            background: rgba(16, 185, 129, 0.1);
            color: var(--secondary);
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }

        .description-box {
            background: var(--gray-50);
            border-radius: 12px;
            padding: 20px;
            margin-top: 8px;
            line-height: 1.6;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            font-size: 14px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #6d28d9;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .btn-secondary:hover {
            background: var(--gray-300);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            .image-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="main-content-wrap">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                </svg>
                {{ $data->name }} - Gift Details
            </h1>
        </div>

        <!-- IMAGE GALLERY CARD -->
        <div class="card-box">
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Gift Images
                </h3>
            </div>

            <div id="giftGallery" class="image-grid">
                @php
                    $images = [];
                    foreach (['image', 'image2', 'image3'] as $img) {
                        if ($data->$img) {
                            $images[] = $data->$img;
                        }
                    }
                @endphp

                @if (count($images) > 0)
                    @foreach ($images as $image)
                        <a href="{{ asset($image) }}" class="image-item">
                            <img src="{{ asset($image) }}" alt="Gift image" />
                        </a>
                    @endforeach
                @else
                    <div class="no-images">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p>No images uploaded for this gift.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- INFORMATION SECTION -->
        <div class="card-box">
            <div class="card-header">
                <h3 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Gift Information
                </h3>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <p class="info-title">Gift Name</p>
                    <p class="info-value">{{ $data->name }}</p>
                </div>

                <div class="info-item">
                    <p class="info-title">Condition</p>
                    <p class="info-value">{{ ucfirst($data->condition) ?? 'N/A' }}</p>
                </div>

                <div class="info-item">
                    <p class="info-title">User</p>
                    <p class="info-value">{{ $data->user->name ?? 'N/A' }}</p>
                </div>

                <div class="info-item">
                    <p class="info-title">Wishlist Path</p>
                    <p class="info-value">
                        @if ($data->wishlistList)
                            {{ $data->wishlistList->wishlist->title }} →
                            {{ $data->wishlistList->title }}
                        @else
                            N/A
                        @endif
                    </p>
                </div>

                <div class="info-item">
                    <p class="info-title">Shipping Option</p>
                    <p class="info-value">
                        @if ($data->shipping_option == 1)
                            Self Delivery
                        @elseif($data->shipping_option == 2)
                            Courier
                        @elseif($data->shipping_option == 3)
                            Pickup Point
                        @else
                            N/A
                        @endif
                    </p>
                </div>

                <div class="info-item">
                    <p class="info-title">Delivery Address</p>
                    <p class="info-value">{{ $data->delivery_address ?? 'N/A' }}</p>
                </div>

                <div class="info-item">
                    <p class="info-title">Approved</p>
                    <input type="checkbox" class="is-featured-checkbox" {{ $data->is_approved ? 'checked' : '' }} data-id="{{ $data->id }}">
                </div>
            </div>

            <div style="margin-top:20px;">
                <p class="info-title">Description</p>
                <div class="description-box" style="font-size: 15px;">
                    {{ $data->description ?? 'No description provided.' }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/css/lightgallery-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/lightgallery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/thumbnail/lg-thumbnail.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/zoom/lg-zoom.min.js"></script>
    <script>
        lightGallery(document.getElementById('giftGallery'), {
            speed: 300,
            plugins: [lgZoom, lgThumbnail],
            thumbnail: true,
            zoom: true
        });
    </script>

    <script>
        $(document).on('change', '.is-featured-checkbox', function () {
            let id = $(this).data('id');
            let approve = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '{{ route("gift.approve", "") }}/' + id,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    approve: approve
                },
                success: function (response) {
                    ajaxMessage(response.message, response.type);
                },
                error: function () {
                    alert('Something went wrong');
                }
            });
        });
    </script>
@endpush
