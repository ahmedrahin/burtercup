<?php

namespace App\Http\Controllers\API\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\AppliedCoupon;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\ProductGlasses;
use App\Models\ColorGlass;
use App\Models\ProductAccessories;
use App\Models\ExtendePrescription;
use App\Models\CartOptions;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use App\Helpers\Helper;


class CartController extends Controller
{
    public $couponCode;
    public $discountAmount = 0;
    public $appliedCoupon;


    public function typeGlass(){
        $data = ProductGlasses::latest()->get();

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'fetch type of glasses',
            'data' => $data
        ]);
    }

    public function colorGlass(){
        $data = ColorGlass::latest()->get();

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'fetch color of glasses',
            'data' => $data
        ]);
    }

    public function accessories(){
        $data = ProductAccessories::latest()->get();

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'fetch accessories',
            'data' => $data
        ]);
    }

    public function extended(){
        $data = ExtendePrescription::latest()->get();

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'fetch ExtendePrescription',
            'data' => $data
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $user = auth('api')->user();
        $appliedCoupon = AppliedCoupon::where('user_id', $user->id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 500);
        }

        if ($appliedCoupon) {
            $appliedCoupon->delete();
        }

        $cartItems = Cart::where('user_id', $user->id)->get();
        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        $rules = [
            'coupon_code' => 'required',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors()
            ], 422);
        }

        $code = $request->coupon_code;
        $coupon = Coupon::whereRaw('BINARY code = ?', [$code])
            ->where('status', 1)
            ->whereDate('start_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('expire_date')
                    ->orWhereDate('expire_date', '>=', now());
            })
            ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired coupon code'
            ], 500);
        }

        if ($coupon->min_purchase_amount && ($coupon->min_purchase_amount > $this->getTotalAmount())) {
            return response()->json([
                'success' => false,
                'message' => 'You need to purchase at least ' . $coupon->min_purchase_amount . '$ to use this coupon'
            ], 500);
        }

        // max used per user
        if (!is_null($coupon->used) && $coupon->used > 0) {
            $userUsageCount = $user->orders()->where('cupon_code', $coupon->code)->count();
            if ($userUsageCount >= $coupon->used) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already used this coupon ' . $coupon->used . ' times.'
                ], 500);
            }
        }

        // total usage limit
        $usage = $coupon->orders()->count();
        if ($coupon->usage_limit && ($usage >= $coupon->usage_limit)) {
            return response()->json([
                'success' => false,
                'message' => 'The coupon is no longer available'
            ], 500);
        }

        // Calculate discount
        $discountAmount = 0;
        if ($coupon->discount_type == 'percentage') {
            $discountAmount = $this->getTotalAmount() * ($coupon->discount_amount / 100);
        } else {
            $discountAmount = $coupon->discount_amount;
        }


        AppliedCoupon::create([
            'user_id'  => $user->id,
            'coupon_code'  => $coupon->code,
            'discount_amount'  => $discountAmount,
        ]);

        // Return coupon and discount in response
        return response()->json([
            'success' => true,
            'message' => 'Coupon Applied Successfully.',
            'discount_amount' => number_format($discountAmount, 2, '.', ''),
            'coupon_code' => $coupon->code,
            'applied_coupon' => [
                'code' => $coupon->code,
                'discount' => number_format($discountAmount, 2, '.', ''),
            ]
        ], 200);
    }

    public function removeCoupon(Request $request){
        $user = auth('api')->user();
        $appliedCoupon = AppliedCoupon::where('user_id', $user->id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 500);
        }

        if ($appliedCoupon) {
            $appliedCoupon->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Coupon has been remove.'
        ], 200);

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

    public function AddCart(Request $request, $id){
        try{
            //  return response()->json([

            //     'message' => $request->all(),
            // ], 200);

            $user = auth('api')->user();
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                ], 500);
            }

            $cartItem = Cart::where('user_id', $user->id)
                        ->where('product_id', $product->id)
                        ->first();

            $newItem = Cart::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'price' => $product->offer_price,
                'quantity' => $request->quantity ?? 1
            ]);

            $this->selectOptions($request, $newItem);

            return response()->json([
                'success' => true,
                'message' => 'Item has been added into cart.',
            ], 200);

        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function selectOptions(Request $request, $cart)
    {
        $data = new CartOptions();
        $data->selected_glasses = $request->selected_glasses;
        $data->prescription_type = $request->prescription_type;

        if( $request->prescription_type == 'custom' ){
            // Right (OD) fields
            $data->right_sphere = $request->right_sphere ?? null;
            $data->right_cylinder = $request->right_cylinder ?? null;
            $data->right_axis = $request->right_axis ?? null;
            $data->right_add = $request->right_add ?? null;

            // Left (OS) fields
            $data->left_sphere = $request->left_sphere ?? null;
            $data->left_cylinder = $request->left_cylinder ?? null;
            $data->left_axis = $request->left_axis ?? null;
            $data->left_add = $request->left_add ?? null;

            // PD (Pupillary Distance)
            $data->right_pd = $request->right_pd ?? null;
            $data->left_pd = $request->left_pd ?? null;
            $data->additional_pd = $request->additional_pd ?? null;
        }

        if ($request->upload_image && $request->prescription_type == 'upload') {
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('upload_image'), 'prescription', $randomString);
            $data->upload_image = $imagePath;
        }

        $data->saved_image = $request->saved_image ?? null;

        $data->type_glass_title = $request->type_glass_title;
        $data->type_glass_price = $request->type_glass_price;
        $data->color_glass_title = $request->color_glass_title;
        $data->color_glass_price = $request->color_glass_price;
        $data->accessorie_title = is_string($request->accessorie_title) ? json_decode($request->accessorie_title, true) : $request->accessorie_title;
        $data->accessorie_price = is_string($request->accessorie_price) ? json_decode($request->accessorie_price, true) : $request->accessorie_price;

        $data->choose_strength = is_string($request->choose_strength) ? json_decode($request->choose_strength, true) : $request->choose_strength;
        $data->extended_title = is_string($request->extended_title) ? json_decode($request->extended_title, true) : $request->extended_title;
        $data->extended_price = is_string($request->extended_price) ? json_decode($request->extended_price, true) : $request->extended_price;

        $data->selected_pack = $request->selected_pack;
        $data->color = $request->color;
        $data->opacity = $request->opacity;

        // Link to the cart item
        $data->cart_id = $cart->id;

        // Save to the database
        $data->save();
    }


    public function CartList(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 500);
            }

            $cartItems = Cart::with(['product:id,name,base_price,offer_price,discount_option,thumb_image,quantity',
                                    'cartOptions:id,cart_id,selected_glasses,type_glass_title,type_glass_price,color_glass_title,color_glass_price,accessorie_title,accessorie_price,selected_pack,choose_strength,extended_title,extended_price,color,opacity'])
                            ->where('user_id', $user->id)
                            ->get();

            $cartItems->transform(function ($item) {
                $productTotal = $item->price * $item->quantity;

                if ($item->cartOptions) {
                    $productTotal += $item->cartOptions->type_glass_price ?? 0;
                    $productTotal += $item->cartOptions->color_glass_price ?? 0;

                    if ($item->cartOptions->accessorie_price) {
                        $accessoriePrice = $item->cartOptions->accessorie_price;

                        if (is_string($accessoriePrice)) {
                            $accessoriePrice = json_decode($accessoriePrice, true);
                        }

                        $productTotal += array_sum($accessoriePrice);
                    }

                    if ($item->cartOptions->extended_price) {
                        $extended_price = $item->cartOptions->extended_price;
                        $productTotal += array_sum($extended_price);
                    }
                }

                $item->subtotal = number_format($productTotal, 2, '.', '');
                return $item;
            });

            return response()->json([
                'success' => true,
                'message' => 'Cart items retrieved successfully.',
                'data' => $cartItems,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function updateQuantity(Request $request, $id, $value){
        try{
            $cart = Cart::where('id', $id)->first();

            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found',
                ], 500);
            }

            $cart->update(['quantity' => $value ?? $cart->quantity]);

            return response()->json([
                'success' => true,
                'message' => 'The Product Quantity has been updated.',
            ], 200);

        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteCart(Request $request)
    {
        try {
            $cartIds = $request->ids;

            if (!is_array($cartIds) || empty($cartIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No cart items selected for deletion.',
                ], 400);
            }

            $deleted = Cart::whereIn('id', $cartIds)->delete();

            return response()->json([
                'success' => true,
                'message' => $deleted > 0 ? 'Selected cart items deleted.' : 'No matching cart items found.',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function orderSummary(Request $request){
        try{
            $user = auth('api')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 500);
            }

            $cartItems = Cart::where('user_id', $user->id)->get();
            $quantity = $cartItems->sum('quantity');
            $appliedCoupon = AppliedCoupon::where('user_id', $user->id)->first();

            $shipping = 0;

            if (!$appliedCoupon) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order summary',
                    'subtotal' => $this->getTotalAmount(),
                    'grand_total' =>  $this->getTotalAmount() +  $shipping,
                    'shipping' => 0,
                    'total_quantity' => $quantity,
                    'applied_coupon' => null,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order summary',
                'subtotal' => $this->getTotalAmount(),
                'grand_total' =>  $this->getTotalAmount() + $shipping - $appliedCoupon->discount_amount,
                'shipping' => 0,
                'total_quantity' => $quantity,
                'applied_coupon' => [
                    'code' => $appliedCoupon->coupon_code,
                    'discount' => number_format($appliedCoupon->discount_amount, 2, '.', ''),
                ]
            ], 200);

        }catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
