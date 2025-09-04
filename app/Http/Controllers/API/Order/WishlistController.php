<?php

namespace App\Http\Controllers\API\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    public function addWishlist(Request $request, $id){
        try {
            $user = auth('api')->user();
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                ]);
            }

            $sessionId = session()->getId();

            $wishlistItem = Wishlist::where('product_id', $id)
                ->where(function ($query) use ($user, $sessionId) {
                    if ($user) {
                        $query->where('user_id', $user->id);
                    }
                })
                ->first();

            if ($wishlistItem) {
                $wishlistItem->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Product removed from your wishlist',
                    'removed' => true
                ], 200);
            } else {
               $data = Wishlist::create([
                    'user_id' => $user ? $user->id : null,
                    'product_id' => $id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Product added to your wishlist',
                    'data' => $data,
                    'code' => 200,
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function wishlistList(Request $request)
    {
        $user = auth('api')->user();
        $sessionId = $request->session_id ?? session()->getId();

        $wishlists = Wishlist::
        when($user, function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->latest()
        ->get();

        $wishlists->transform(function ($item) {
            $product = $item->product;

            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'Wishlist List',
            'data' => $wishlists,
            'code' => 200,
        ], 200);
    }


    public function deleteWishlist(Request $request, $id)
    {
        $user = auth('api')->user();
        $sessionId = $request->session_id ?? session()->getId();

        $wishlist = Wishlist::where('id', $id)->first();

        // if($user){
        //     $wishlist = $wishlist->where('user_id', $user->id)->first();
        // }

        if (!$wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Wishlist not found or access denied',
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist has been removed',
            'code' => 200,
        ], 200);
    }

}
