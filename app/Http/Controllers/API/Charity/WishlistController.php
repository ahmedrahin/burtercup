<?php

namespace App\Http\Controllers\API\Charity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gift;
use App\Models\WishlistList;
use App\Models\WishlistCategory;
use App\Models\CharityWishlist;
use App\Models\CharityChecklist;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WishlistController extends Controller
{
    public function createGift(Request $request, $id){
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to access your profile.',
                'code' => 401,
            ], 401);
        }

        $list = WishlistList::find($id);

         if (!$list) {
            return response()->json([
                'status' => false,
                'message' => 'The wishlist list not found',
                'code' => 400,
            ], 400);
        }

         $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:products,name',
            'image' => 'required|image',
            'condition' => 'required|string',
            'shipping_option' => 'required',
        ], messages: [
            'image.required' => 'Please select at least one product image'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
                'status' => false,
            ], 400);
        }

        if ($request->hasFile('image')) {
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'gift', $randomString);
        }
        if ($request->hasFile('image2')) {
            $randomString = (string) Str::uuid();
            $imagePath2 = Helper::fileUpload($request->file('image2'), 'gift', $randomString);
        }
        if ($request->hasFile('image3')) {
            $randomString = (string) Str::uuid();
            $imagePath3 = Helper::fileUpload($request->file('image3'), 'gift', $randomString);
        }

         $data = [
            'wishlist_list_id' => $list->id,
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => auth()->id(),
            'condition' => $request->condition,
            'delivery_address' => $request->delivery_address,
            'shipping_option' => $request->shipping_option
        ];

        if (isset($imagePath)) {
            $data['image'] = $imagePath;
        }
        if (isset($imagePath2)) {
            $data['image2'] = $imagePath2;
        }
        if (isset($imagePath3)) {
            $data['image3'] = $imagePath3;
        }

        $product = Gift::create($data);

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Thanks for your contribute',
            'data' => $product,
        ], 200);

    }
    public function WishlistCategory(){
        $categories = WishlistCategory::where('status', 'active')->get();

        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => $categories,
        ], 200);
    }

    public function CharityWishlist(Request $request)
    {
        $category = $request->input('category');

        $query = CharityWishlist::with('category:id,name')->where('status', 'active');

        if ($category) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', $category);
            });
        }

        $lists = $query->get();

        return response()->json([
            'status' => true,
            'code'   => 200,
            'data'   => $lists,
        ], 200);
    }

    public function wishlistItems($id)
    {
        $items = WishlistList::where('charity_wishlist_id', $id)->where('status', 'active')->get();

        return response()->json([
            'status' => true,
            'code'   => 200,
            'data'   => $items,
        ], 200);
    }

    public function charityChecklist(Request $request){
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to access your profile.',
                'code' => 401,
            ], 401);
        }

        $lists = $request->input('checklist');

        if (!$lists || !is_array($lists)) {
            return response()->json([
                'status' => false,
                'message' => 'The checklist field is required',
                'code' => 400,
            ], 400);
        }

        foreach ($lists as $listId) {

            $existing = CharityChecklist::where('user_id', auth()->id())
                ->where('wishlist_list_id', $listId)
                ->first();

            $wishlistList = WishlistList::find($listId);

            if ($existing || !$wishlistList) {
                continue;
            }

            $data = CharityChecklist::create([
                'user_id' => auth()->id(),
                'wishlist_list_id' => $listId,
                'status' => 'pending',
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Checklist created successfully',
            'code' => 201,
        ], 201);

    }

}
