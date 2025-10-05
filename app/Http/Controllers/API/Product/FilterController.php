<?php

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use App\Models\Wishlist;
use App\Models\SubCategory;
use App\Models\SearchHistory;

class FilterController extends Controller
{
    public function searchProducts(Request $request)
    {
        $user = auth('api')->user();

        $search = $request->get('query');
        $minPrice = trim($request->input('min_price'));
        $maxPrice = trim($request->input('max_price'));
        $condition = $request->input('condition');
        $deliveryMethod = $request->input('delivery_method');
        $sortBy = $request->input('sort_by');
        // expected: featured, new, high_to_low, low_to_high

        $products = Product::with(['user:id,name,avatar', 'tags'])
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('expire_date')
                    ->orWhere('expire_date', '>', now());
            });

        // 🔍 Search by name or tags
        if (!empty($search)) {
            $products->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('tags', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        // 💰 Price filter
        if (is_numeric($minPrice)) {
            $products->where('coin', '>=', $minPrice);
        }
        if (is_numeric($maxPrice)) {
            $products->where('coin', '<=', $maxPrice);
        }

        // 📦 Condition filter
        if ($condition !== null) {
            if (in_array($condition, ['brand_new', 'used', 'like_new', 'well_used'])) {
                $products->where('condition', $condition);
            } else {
                $products->whereRaw('1 = 0'); // invalid → empty
            }
        }

        // 🚚 Delivery method filter
        if ($deliveryMethod !== null) {
            if (in_array($deliveryMethod, ['meet_up', 'shipping'])) {
                $products->where('delivery_method', $deliveryMethod);
            } else {
                $products->whereRaw('1 = 0');
            }
        }

        switch ($sortBy) {
            case 'featured':
                $products->where('is_featured', 1)->orderBy('created_at', 'desc');
                break;

            case 'new':
                $products->where('is_new', 1)->orderBy('is_new', 'desc');
                break;

            case 'high_to_low':
                $products->orderBy('coin', 'desc');
                break;

            case 'low_to_high':
                $products->orderBy('coin', 'asc');
                break;

            default:
                $products->orderBy('created_at', 'desc');
                break;
        }

        $products = $products->get(['id', 'user_id', 'name', 'thumb_image', 'coin', 'slug']);

        $result = $products->map(function ($product) use ($user) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'thumb_image' => $product->thumb_image,
                'slug' => $product->slug,
                'coin' => $product->coin,
                'user' => $product->user, // only id, name, avatar
                'wishlist_count' => Wishlist::where('product_id', $product->id)->count(),
                'is_wishlisted' => $user
                    ? Wishlist::where('product_id', $product->id)
                        ->where('user_id', $user->id)
                        ->exists()
                    : false,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Search results',
            'total' => $result->count(),
            'data' => $result,
        ]);
    }

    public function searchQuery(Request $request)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $search = $request->get('query');
        $data = SearchHistory::create([
            'user_id' => $user->id,
            'search_query' => $search
        ]);

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Search query recorded successfully.',
            'data' => $data
        ], 200);

    }

    public function clearSearchHistory()
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        SearchHistory::where('user_id', $user->id)->delete();

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Search history cleared successfully.',
        ], 200);
    }

    public function applyFilters(Request $request)
    {
        $user = auth('api')->user();
        $minPrice = trim($request->input('min_price'));
        $maxPrice = trim($request->input('max_price'));
        $condition = $request->input('condition');
        $deliveryMethod = $request->input('delivery_method');

        $products = Product::with(['user:id,name,avatar'])
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('expire_date')
                    ->orWhere('expire_date', '>', now());
            });

        // Price filters
        if (is_numeric($minPrice)) {
            $products->where('coin', '>=', $minPrice);
        }
        if (is_numeric($maxPrice)) {
            $products->where('coin', '<=', $maxPrice);
        }

        if ($condition !== null) {
            if (in_array($condition, ['brand_new', 'used', 'like_new', 'well_used'])) {
                $products->where('condition', $condition);
            } else {
                // If condition provided but invalid, force empty result
                $products->whereRaw('1 = 0');
            }
        }

        // Delivery method filter
        if ($deliveryMethod !== null) {
            if (in_array($deliveryMethod, ['meet_up', 'shipping'])) {
                $products->where('delivery_method', $deliveryMethod);
            } else {
                // If delivery method provided but invalid, force empty result
                $products->whereRaw('1 = 0');
            }
        }

        $products = $products->get(['id', 'user_id', 'name', 'thumb_image', 'coin', 'slug']);

        $result = $products->map(function ($product) use ($user) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'thumb_image' => $product->thumb_image,
                'slug' => $product->slug,
                'coin' => $product->coin,
                'user' => $product->user,
                'wishlist_count' => Wishlist::where('product_id', $product->id)->count(),
                'is_wishlisted' => $user
                    ? Wishlist::where('product_id', $product->id)
                        ->where('user_id', $user->id)
                        ->exists()
                    : false,
            ];
        });

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Filtered results',
            'total' => $result->count(),
            'data' => $result,
        ]);
    }

    public function sort(Request $request)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $sortBy = $request->input('sort_by');
        // expected: featured, new, high_to_low, low_to_high

        $products = Product::with(['user:id,name,avatar'])
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('expire_date')
                    ->orWhere('expire_date', '>', now());
            });

        //Sorting logic
        switch ($sortBy) {
            case 'featured':
                $products->where('is_featured', 1)
                    ->orderBy('created_at', 'desc'); // latest featured first
                break;

            case 'new':
                $products->where('is_new', 1)
                    ->orderBy('is_new', 'desc'); // newest items first
                break;

            case 'high_to_low':
                $products->orderBy('coin', 'desc');
                break;

            case 'low_to_high':
                $products->orderBy('coin', 'asc');
                break;

            default:
                $products->orderBy('created_at', 'desc');
                break;
        }

        $products = $products->get(['id', 'user_id', 'name', 'thumb_image', 'coin', 'slug']);

        $result = $products->map(function ($product) use ($user) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'thumb_image' => $product->thumb_image,
                'slug' => $product->slug,
                'coin' => $product->coin,
                'user' => $product->user, // only id, name, avatar
                'wishlist_count' => Wishlist::where('product_id', $product->id)->count(),
                'is_wishlisted' => $user
                    ? Wishlist::where('product_id', $product->id)
                        ->where('user_id', $user->id)
                        ->exists()
                    : false,
            ];
        });

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Sorted results',
            'total' => $result->count(),
            'data' => $result,
        ]);
    }
}
