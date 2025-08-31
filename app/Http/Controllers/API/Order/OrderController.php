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
use App\Helpers\Helper;
use App\Models\SavedPrescribtion;
use App\Models\DeliveryOption;
use Illuminate\Support\Facades\DB;
use App\Models\OrderHistory;

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

        $cartItems = Cart::where('user_id', $user->id)->get();

        // Sum up price * quantity for each item
        $finalTotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        return $finalTotal;
    }

    public function placeOrder(Request $request)
    {
        $user = auth('api')->user();
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

        if($user->coins < $this->getTotalAmount()){
            return response()->json([
                'success' => false,
                'message' => 'You do not have enough coins to place this order.'
            ], 400);
        }


        $rules = [
            // 'phone' => 'required|numeric',
            'address' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors()
            ], 422);
        }

        $letters = Str::upper(Str::random(4));
        $numbers = (string) rand(1000, 9999);
        $orderId = str_shuffle($letters . $numbers);

        $subtotal = $this->getTotalAmount() + $request->delivery_option_price ?? 0;

        // Create the order
        $order = Order::create([
            'order_id' => $orderId,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $request->phone ?? $user->phone,
            'order_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'note' => $request->note ?? null,
            'address' => $request->address ?? null,
            'city' => $request->city ?? null,
            'state' => $request->state ?? null,
            'country' => $request->country ?? null,
            'zip_code' => $request->zip_code ?? null,
            'shipping_address' => trim(($request->address ?? '') . ', ' . ($request->city ?? '') . ', ' . ($request->state ?? '') . ', ' . ($request->zip_code ?? '')),
            'order_source' => 'app',
            'subtotal' => $subtotal,
            'grand_total' => $subtotal,
            'user_type' => 'customer',
            'transaction_id' => null,
            'payment_method' => 'Cash on Delivery',
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
        }

        // Clear the cart after order placed
        Cart::where('user_id', $user->id)->delete();

        // user coin exist
        $user->update([
            'coins' => $user->coins - $subtotal
        ]);

         // add order history
        OrderHistory::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'note' => 'Order placed, waiting for processing.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully',
            'order' => $order
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


        $orders = Order::withCount(['orderItems as orderItemsCount'])
                ->with(['delivery:id,name'])
                ->where('user_id', $user->id)
                ->select(['id', 'order_id', 'delivery_status', 'delivery_option_id', DB::raw('(select count(*) from order_items where order_items.order_id = orders.id) as orderItemsCount')])
                ->latest()
                ->get();

        // group by delivery_status
        $groupedOrders = $orders->groupBy('delivery_status');

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'order history',
            'data'    => $groupedOrders
        ]);

    }

   public function orderDetails(Request $request, $id)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 401);
        }

        $order = Order::with([
                'orderItems.product' => function ($query) {
                            $query->select('id', 'name', 'thumb_image');
                        },
                        'delivery:id,name'
                    ])
                    ->where('order_id', $id)
                    ->where('user_id', $user->id)
                    ->first();


        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'code'    => 200,
            'message' => 'order details',
            'data'    => $order
        ]);
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

        $order = Order::where('order_id', $id)->where('user_id', $user->id)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        $history = OrderHistory::where('order_id', $order->id)->get()->select(['order_id', 'status', 'note', 'changed_at']);

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Order tracking details',
            'order' => $order->order_id,
            'data' => $history
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
