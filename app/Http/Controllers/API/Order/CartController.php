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
use App\Models\ExtendePrescription;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use App\Helpers\Helper;


class CartController extends Controller
{

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

    public function AddCart(Request $request, $id)
    {
        try {
            $user = auth('api')->user();
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            if ($product->quantity <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This product is out of stock and cannot be added to the cart.',
                ], 400);
            }

            // Check if already in cart
            $cartItem = Cart::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->first();

            $qtyToAdd = $request->quantity ?? 1;

            // Prevent adding more than available stock
            if ($cartItem) {
                if ($cartItem->quantity + $qtyToAdd > $product->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You cannot add more than available stock.',
                    ], 400);
                }

                // Update quantity
                $cartItem->quantity += $qtyToAdd;
                $cartItem->save();
            } else {
                if ($qtyToAdd > $product->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You cannot add more than available stock.',
                    ], 400);
                }

                // Create new cart item
                Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'price' => $product->coin,
                    'quantity' => $qtyToAdd
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item has been added into cart.',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function CartList(Request $request)
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 401);
            }

            // Load cart items with valid products only
            $cartItems = Cart::with(['product:id,name,coin,thumb_image,quantity,status,expire_date'])
                ->where('user_id', $user->id)
                ->get();

            // Filter and remove invalid cart items
            $validCartItems = $cartItems->filter(function ($item) {
                $product = $item->product;
                if (!$product) {
                    $item->delete();
                    return false;
                }

                if (
                    $product->status != 1 ||
                    ($product->expire_date && $product->expire_date <= now())
                ) {
                    $item->delete();
                    return false;
                }

                return true;
            });

            // Add subtotal for each valid item
            $validCartItems->map(function ($item) {
                $item->subtotal = number_format($item->product->coin * $item->quantity, 0, '.', '');
                return $item;
            });

            return response()->json([
                'success' => true,
                'message' => 'Cart items retrieved successfully.',
                'data'    => $validCartItems->values(),
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // public function updateQuantity(Request $request, $id, $value)
    // {
    //     try {
    //         $cart = Cart::where('id', $id)->first();

    //         if (!$cart) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Cart not found',
    //             ], 500);
    //         }

    //         $cart->update(['quantity' => $value ?? $cart->quantity]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'The Product Quantity has been updated.',
    //         ], 200);

    //     } catch (Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Something went wrong.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

     public function deleteCart(Request $request, $id)
    {
        try {
            $cartId = Cart::find($id);

            if (!$cartId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No cart items found.',
                ], 400);
            }

            $deleted = Cart::where('id', $cartId->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'cart item deleted.',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function removeAll(Request $request)
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

    public function orderSummary(Request $request)
    {
        try {
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
                    'grand_total' => $this->getTotalAmount() + $shipping,
                    'shipping' => 0,
                    'total_quantity' => $quantity,
                    'applied_coupon' => null,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order summary',
                'subtotal' => $this->getTotalAmount(),
                'grand_total' => $this->getTotalAmount() + $shipping - $appliedCoupon->discount_amount,
                'shipping' => 0,
                'total_quantity' => $quantity,
                'applied_coupon' => [
                    'code' => $appliedCoupon->coupon_code,
                    'discount' => number_format($appliedCoupon->discount_amount, 2, '.', ''),
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
