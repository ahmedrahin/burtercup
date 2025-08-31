@extends('backend.app')

@section('title', 'Order Details ' . $data->order_id)

@push('styles')
    <style>
        .table-order-detail .name {
            text-align: center;
        }

        .table-order-detail .product-name {
            text-align: left !important;
        }

        .total {
            text-align: right !important;
        }

        .wg-order-detail .right {
            width: 380px !important;
        }
        #order-status-select {
            font-size: 15px;
            background: white;
            padding: 9px 15px;
            border-radius: 6px;
        }
        #order-status-select:focus {
            box-shadow: none;
        }
    </style>
@endpush

@section('content')
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>@yield('title')</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('dashboard') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <a href="{{ route('order.index') }}">
                        <div class="text-tiny">Order Details</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">@yield('title')</div>
                </li>
            </ul>
        </div>
    </div>

    <div class="wg-order-detail">
        <div class="left flex-grow">
            <div class="wg-box mb-20">
                <div class="wg-table table-order-detail">
                    <ul class="table-title flex items-center justify-between gap20 mb-24">
                        <li>
                            <div class="body-title">All item</div>
                        </li>
                        <li>
                            <div class="dropdown default">
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:void(0);">Name</a></li>
                                    <li><a href="javascript:void(0);">Quantity</a></li>
                                    <li><a href="javascript:void(0);">Price</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>

                    <ul class="flex flex-column">
                        <!-- Header Row -->
                        <li class="product-item gap14">
                            <div class="flex items-center justify-between gap40 flex-grow">
                                <div class="name product-name">
                                    <div class="body-title-2 font-bold">Product</div>
                                </div>
                                <div class="name">
                                    <div class="body-title-2 font-bold">Quantity</div>
                                </div>
                                <div class="name">
                                    <div class="body-title-2 font-bold">Price</div>
                                </div>
                                <div class="name total">
                                    <div class="body-title-2 font-bold">Total</div>
                                </div>
                                <div class="name total">
                                    <div class="body-title-2 font-bold">Actions</div>
                                </div>
                            </div>
                        </li>

                        <!-- Order Items -->
                        @foreach ($data->orderItems as $item)
                            <li class="product-item gap14">
                                <div class="image no-bg">
                                    <img src="{{ asset($item->product->thumb_image) }}" alt=""
                                        style="max-width: 80px;">
                                </div>
                                <div class="flex items-center justify-between gap40 flex-grow">
                                    <div class="name product-name">
                                        <a href="{{ route('product.show', $item->product->id) }}"
                                            class="body-title-2">{{ $item->product->name }}</a>
                                    </div>
                                    <div class="name">
                                        <div class="body-title-2">{{ $item->quantity }}</div>
                                    </div>
                                    <div class="name">
                                        <div class="body-title-2">${{ $item->price }}</div>
                                    </div>
                                    <div class="name total">
                                        <div class="body-title-2">${{ number_format($item->price * $item->quantity, 2) }}
                                        </div>
                                    </div>
                                    <div class="name total">

                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

             <div class="wg-box">
                <div class="wg-table table-cart-totals">
                    <ul class="table-title flex mb-24">
                        <li>
                            <div class="body-title">Cart Totals</div>
                        </li>
                        <li>
                            <div class="body-title text-end">Price</div>
                        </li>
                    </ul>
                    <ul class="flex flex-column gap14">
                        <li class="cart-totals-item">
                            <span class="body-text">Subtotal:</span>
                            <span class="body-title-2 text-end">${{ number_format($data->subtotal, 2) }}</span>
                        </li>

                        <li class="divider"></li>
                        @if ($data->coupon_discount)
                            <li class="cart-totals-item">
                                <span class="body-text">Coupon Discount:</span>
                                <span class="body-title-2 text-end text-danger">(- ${{ $data->coupon_discount }})</span>
                            </li>
                            <li class="divider"></li>
                        @endif

                        @if ($data->delivery_option_price)
                            <li class="cart-totals-item">
                                <span class="body-text">Delivery Price <span class="text-muted">{{ isset($data->delivery) ? '('. $data->delivery->name .')' : '' }}</span> :</span>
                                <span class="body-title-2 text-end">(+ ${{ $data->delivery_option_price }})</span>
                            </li>
                        @endif

                        <li class="cart-totals-item">
                            <span class="body-title">Grand Total:</span>
                            <span class="body-title tf-color-1 text-end">${{ number_format($data->grand_total, 2) }}</span>
                        </li>
                    </ul>
                </div>


            </div>

            <!-- Bootstrap Modal -->
            <div id="detailsModal" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Item Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="itemDetails"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="right">
            <div class="wg-box mb-20 gap10">
                <div class="body-title">Summary</div>
                <div class="summary-item">
                    <div class="body-text">Order ID:</div>
                    <div class="body-title-2">#{{ $data->order_id }}</div>
                </div>
                <div class="summary-item">
                    <div class="body-text">Order Date:</div>
                    <div class="body-title-2">{{ Carbon\Carbon::parse($data->order_date)->format('d M, Y - (h:i A)') }}
                    </div>
                </div>
                <div class="summary-item">
                    <div class="body-text">Order Time:</div>
                    <div class="body-title-2">{{ \Carbon\Carbon::parse($data->order_date)->diffForHumans() }}</div>
                </div>
                @if ($data->coupon_discount)
                    <div class="summary-item">
                        <div class="body-text">Coupon Code:</div>
                        <div class="body-title-2">{{ $data->cupon_code }}</div>
                    </div>
                @endif
                <div class="summary-item">
                    <div class="body-text">Transaction_id:</div>
                    <div class="body-title-2">{{ $data->transaction_id }}</div>
                </div>
                <div class="summary-item">
                    <div class="body-text">Total:</div>
                    <div class="body-title-2 tf-color-1">${{ number_format($data->grand_total, 2) }}</div>
                </div>
            </div>

            <div class="wg-box mb-20 gap10">
                <div class="body-title">Customer Information</div>
                <div class="summary-item">
                    <div class="body-text">Name:</div>
                    <div class="body-title-2 ">
                        <a href="{{ route('user.show', $data->user->id) }}" class="text-primary">
                            {{ $data->user->name }}
                        </a>
                    </div>
                </div>
                <div class="summary-item">
                    <div class="body-text">Email:</div>
                    <div class="body-title-2">{{ $data->user->email }}</div>
                </div>
                <div class="summary-item">
                    <div class="body-text">Phone:</div>
                    <div class="body-title-2">{{ $data->phone }}</div>
                </div>
            </div>

            <div class="wg-box mb-20 gap10">
                <div class="body-title">Shipping Address</div>
                <div class="body-text">
                    {{ $data->shipping_address }}
                </div>
            </div>


            <div class="wg-box gap10">
                <div class="body-title">Order Status</div>
                <select id="order-status-select" class="form-select">
                    <option value="" disabled selected>Select Status</option>
                    <option value="pending" {{ $data->delivery_status == 'pending' ? 'selected' : '' }} >Pending</option>
                    <option value="confirmed" {{ $data->delivery_status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="processing" {{ $data->delivery_status == 'processing' ? 'selected' : '' }} >Processing</option>
                    <option value="ready to ship" {{ $data->delivery_status == 'ready to ship' ? 'selected' : '' }} >Ready to ship</option>
                    <option value="delivered" {{ $data->delivery_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="canceled" {{ $data->delivery_status == 'canceled' ? 'selected' : '' }}>Canceled</option>
                </select>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    {{-- view --}}
    <script>
        $(document).ready(function() {
            var orderId = "{{ $data->id }}";

            $.ajax({
                url: "{{ route('view.order') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    order_id: orderId
                },
                success: function(response) {
                    console.log(response.message);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });
    </script>

    {{-- order status --}}
    <script>
        $('#order-status-select').on('change', function () {
            let status = $(this).val();
            var orderId = "{{ $data->id }}";
            $.ajax({
                url: "{{ route('order.status') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status,
                    order_id: orderId
                },
                success: function (response) {
                    ajaxMessage(response.message, 'success');
                },
                error: function (xhr) {
                    ajaxMessage('something wrong', 'error');
                }
            });
        });
    </script>

    <script>
        // Event listener for button click
        document.querySelectorAll('.view-details').forEach(button => {
            button.addEventListener('click', function() {
                const data = JSON.parse(this.getAttribute('data-item'));

                let htmlContent = `
                    <h5>Prescription Details</h5>
                    <ul>
                        <li><strong>Prescription Type:</strong> ${data.prescription_type ?? 'N/A'}</li>
                        <li><strong>Selected Glasses:</strong> ${data.selected_glasses ?? 'N/A'}</li>
                        <li><strong>Type Glass:</strong> ${data.type_glass_title ?? 'N/A'} - $${data.type_glass_price ?? '0.00'}</li>
                        <li><strong>Color Glass:</strong> ${data.color_glass_title ?? 'N/A'} - $${data.color_glass_price ?? '0.00'}</li>
                        <li><strong>Color:</strong> ${data.color ?? 'N/A'}</li>
                        <li><strong>Opacity:</strong> ${data.opacity ?? 'N/A'}</li>
                        <li><strong>Selected Pack:</strong> ${data.selected_pack ?? 'N/A'}</li>
                    </ul>
                `;

                // If custom prescription, show details
                if (data.prescription_type === 'custom') {
                    htmlContent += `
                        <h5>Right Eye (OD)</h5>
                        <ul>
                            <li><strong>Sphere:</strong> ${data.right_sphere ?? 'N/A'}</li>
                            <li><strong>Cylinder:</strong> ${data.right_cylinder ?? 'N/A'}</li>
                            <li><strong>Axis:</strong> ${data.right_axis ?? 'N/A'}</li>
                            <li><strong>Add:</strong> ${data.right_add ?? 'N/A'}</li>
                        </ul>
                        <h5>Left Eye (OS)</h5>
                        <ul>
                            <li><strong>Sphere:</strong> ${data.left_sphere ?? 'N/A'}</li>
                            <li><strong>Cylinder:</strong> ${data.left_cylinder ?? 'N/A'}</li>
                            <li><strong>Axis:</strong> ${data.left_axis ?? 'N/A'}</li>
                            <li><strong>Add:</strong> ${data.left_add ?? 'N/A'}</li>
                        </ul>
                        <h5>Pupillary Distance (PD)</h5>
                        <ul>
                            <li><strong>Right PD:</strong> ${data.right_pd ?? 'N/A'}</li>
                            <li><strong>Left PD:</strong> ${data.left_pd ?? 'N/A'}</li>
                            <li><strong>Additional PD:</strong> ${data.additional_pd ?? 'N/A'}</li>
                        </ul>
                    `;
                }

                // Show image if available
                if (data.upload_image) {
                    htmlContent += `
                        <h5>Uploaded Prescription Image:</h5>
                        <a href="/${data.upload_image}" target="_blank">
                            View Uploaded Image
                        </a>
                    `;
                }

                document.getElementById('itemDetails').innerHTML = htmlContent;

                // Show modal
                new bootstrap.Modal(document.getElementById('detailsModal')).show();
            });
        });
    </script>

@endpush
