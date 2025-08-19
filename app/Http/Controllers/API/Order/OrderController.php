<?php

namespace App\Http\Controllers\API\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\Cart;
use App\Models\OrderItem;
use App\Models\AppliedCoupon;
use App\Helpers\Helper;
use App\Models\SavedPrescribtion;
use App\Models\DeliveryOption;

class OrderController extends Controller
{
    public function DeliveryOption()
    {
        $data = DeliveryOption::latest()->get();

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'fetch delivery options',
            'data' => $data
        ]);
    }

    public function getTotalAmount()
    {
        $user = auth('api')->user();

        $cartItems = Cart::with('cartOptions:id,cart_id,type_glass_price,color_glass_price,accessorie_price,extended_price')
            ->where('user_id', $user->id)
            ->get();

        $cartTotal = $cartItems->sum(function ($item) {
            $productTotal = $item->price * $item->quantity;

            if ($item->cartOptions) {
                // Add the price for type of glass and color of glass
                $productTotal += $item->cartOptions->type_glass_price ?? 0;
                $productTotal += $item->cartOptions->color_glass_price ?? 0;

                if ($item->cartOptions->accessorie_price) {
                    $accessoriePrice = $item->cartOptions->accessorie_price;
                    $productTotal += array_sum($accessoriePrice);
                }

                if ($item->cartOptions->extended_price) {
                    $extended_price = $item->cartOptions->extended_price;
                    $productTotal += array_sum($extended_price);
                }

            }

            return $productTotal;
        });

        // Subtract the discount amount
        $finalTotal = $cartTotal - ($this->discountAmount ?? 0);

        return number_format($finalTotal, 2, '.', '');
    }

    public function placeOrder(Request $request)
    {
        $user = auth('api')->user();
        $appliedCoupon = AppliedCoupon::where('user_id', $user->id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 500);
        }

        $cartItems = Cart::where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        $rules = [
            'payment_method' => 'required',
            'phone' => 'required|numeric',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ];

        $messages = [
            'payment_method.required' => 'Please select a payment method.',
        ];

        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors()
            ], 422);
        }

        $letters = Str::upper(Str::random(4));
        $numbers = (string) rand(1000, 9999);
        $orderId = str_shuffle($letters . $numbers);

        $subtotal = $this->getTotalAmount();
        $discount = $appliedCoupon ? $appliedCoupon->discount_amount : 0;
        $grandTotal = $subtotal - $discount + $request->delivery_option_price ?? 0;

        // Create the order
        $order = Order::create([
            'order_id' => $orderId,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $request->phone,
            'order_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'note' => $request->note ?? null,
            'address' => $request->address ?? null,
            'city' => $request->city ?? null,
            'state' => $request->state ?? null,
            'country' => $request->country ?? null,
            'zip_code' => $request->zip_code ?? null,
            'shipping_address' => trim(($request->address ?? '') . ', ' . ($request->city ?? '') . ', ' . ($request->state ?? '') . ', ' . ($request->country ?? '')),
            'payment_method' => $request->payment_method,
            'order_source' => 'website',
            'subtotal' => $subtotal,
            'grand_total' => $grandTotal,
            'user_type' => 'customer',
            'transaction_id' => null,
            'cupon_code' => $appliedCoupon->coupon_code ?? '',
            'coupon_discount' => $discount,
            'delivery_option_id' => $request->delivery_option_id ?? null,
            'delivery_option_price' => $request->delivery_option_price ?? null,
        ]);

        foreach ($cartItems as $item) {
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ]);

            if ($item->cartOptions) {
                $item->cartOptions->update([
                    'order_item_id' => $orderItem->id
                ]);
            }

            if ($item->cartOptions->upload_image) {
                $imagePath = $item->cartOptions->upload_image;
                $this->savedPrescriptionStore($imagePath);
            }

        }

        // Clear the cart after order placed
        Cart::where('user_id', $user->id)->delete();

        if ($appliedCoupon) {
            $appliedCoupon->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully',
            'order' => $order
        ], 200);
    }

    public function savedPrescriptionStore($imagePath)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
                'code' => 401,
            ], 401);
        }

        $data = new SavedPrescribtion();
        if ($imagePath) {
            $data->image = $imagePath;
        }

        $data->user_id = $user->id;
        $data->name = 'prescription_' . now()->format('m/d/Y');

        $data->save();

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Successfully added',
        ], 200);
    }

    public function orderHistroy(Request $request)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
                'code' => 401,
            ], 401);
        }

        $orders = Order::withCount('orderItems')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'order history',
            'data' => $orders,
        ], 200);
    }

    public function orderTrack(Request $request, $id)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 401);
        }

        $order = Order::where('order_id', $id)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $deliveryStatuses = [
            'pending',
            'confirmed',
            'processing',
            'ready to ship',
            'delivered',
            'cancel'
        ];

        $tracking = [];
        foreach ($deliveryStatuses as $status) {
            $tracking[$status] = $order->delivery_status === $status;
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status fetched',
            'order_date' => $order->order_date,
            'status_date' => $order->order_status_date,
            'current_status' => $order->delivery_status,
            'tracking' => $tracking
        ]);

    }

    public function orderInvoice($id)
    {
        $order = Order::where('order_id', $id)->first();
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $orderData = $order->toArray();
        $orderData['orderItems'] = $order->orderItems->map(function ($item) {
            $product = $item->product;
            return [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Order invoice retrieved successfully',
            'order' => $orderData,
        ]);

    }

}
