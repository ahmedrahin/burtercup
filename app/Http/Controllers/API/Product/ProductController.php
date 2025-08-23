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
use App\Models\ProductSize;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{
    public function selectedCategories()
    {
        $user = auth('api')->user();

        $categoryKeys = $user->categories ?? [];
        $subcategories = SubCategory::whereIn('category_key', $categoryKeys)->get()->groupBy('category_key');

        $data = [];

        foreach ($categoryKeys as $key) {
            if (!isset(config('categories')[$key])) {
                continue;
            }

            $categoryConfig = config('categories')[$key];

            $data[] = [
                'key' => $key,
                'name' => $categoryConfig['name'],
                'image' => $categoryConfig['image'] ?? null,
                'subcategories' => isset($subcategories[$key])
                    ? $subcategories[$key]->map(fn($sub) => [
                        'id' => $sub->id,
                        'name' => $sub->name,
                    ])->values()
                    : [],
            ];
        }

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
    }

    public function addProduct(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:products,name',
            'quantity' => 'required|min:1',
            'coins' => 'required',
            'category' => 'required',
            'image' => 'required|image',
            'condition' => 'required|string'
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
            $imagePath = Helper::fileUpload($request->file('image'), 'product', $randomString);
        }


        // Ensure unique slug
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $count = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        $data = [
            'name' => $request->name,
            'slug' => $slug,
            'coin' => $request->coins,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'status' => 1,
            'is_new' => $request->is_new ?? 0,
            'is_featured' => $request->is_featured ?? 0,
            'user_id' => auth()->id(),
            'add_source' => 'app',
            'category' => $request->category,
            'subcategory_id' => $request->subcategory_id,
            'condition' => $request->condition,
        ];

        if (isset($imagePath)) {
            $data['thumb_image'] = $imagePath;
        }

        $product = Product::create($data);

        $images = $request->file(key: 'gellary_images');
        if ($request->hasFile('gellary_images')) {
            foreach ($images as $image) {
                $randomString = (string) Str::uuid();
                $galleryImagePath = Helper::fileUpload($image, 'product/gellary', $randomString);

                $product->gellary_images()->create([
                    'image' => $galleryImagePath,
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Product added successfully.',
            'data' => $product,
        ], 200);

    }

    public function productEdit($id)
    {
        $data = Product::with(['variants', 'productSizes', 'gellary_images'])->find($id);

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);

    }

    public function productUpdate(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:products,name,' . $product->id,
            'quantity' => 'required|min:1',
            'coins' => 'required',
            'category' => 'required',
            'image' => 'nullable|image',
            'condition' => 'required|string'
        ], messages: [
            'image.required' => 'Please select at least one product image'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
                'status' => false,
            ], 400);
        }

        // Handle main image
        if ($request->hasFile('image')) {
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'product', $randomString);
            $product->thumb_image = $imagePath;
        }

        // Ensure unique slug if name changed
        if ($product->name != $request->name) {
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $count = 1;

            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }
            $product->slug = $slug;
        }

        // Update product fields
        $product->name = $request->name;
        $product->coin = $request->coins;
        $product->quantity = $request->quantity;
        $product->description = $request->description;
        $product->status = $request->status ?? $product->status;
        $product->is_new = $request->is_new ?? $product->is_new;
        $product->is_featured = $request->is_featured ?? $product->is_featured;
        $product->category = $request->category;
        $product->condition = $request->condition;
        $product->subcategory_id = $request->subcategory_id ?? $product->subcategory_id;

        $product->save();

        if ($request->hasFile('gellary_images')) {
            // Delete old gallery images (DB + file)
            foreach ($product->gellary_images as $oldImage) {
                if (file_exists(public_path($oldImage->image))) {
                    unlink(public_path($oldImage->image));
                }
                $oldImage->delete();
            }

            // Upload new gallery images
            foreach ($request->file('gellary_images') as $image) {
                $randomString = (string) Str::uuid();
                $galleryImagePath = Helper::fileUpload($image, 'product/gellary', $randomString);

                $product->gellary_images()->create([
                    'image' => $galleryImagePath,
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully.',
            'data' => $product,
        ], 200);
    }

    public function myItemList()
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $items = Product::with(['user:id,name,avatar'])
            ->where('user_id', $user->id)
            ->get(['id', 'user_id', 'name', 'thumb_image', 'coin']);


        $items->map(function ($product) use ($user) {
            $product->wishlist_count = Wishlist::where('product_id', $product->id)->count();
            $product->is_wishlisted = Wishlist::where('product_id', $product->id)
                ->where('user_id', $user->id)
                ->exists();
            return $product;
        });

        return response()->json([
            'status' => true,
            'data' => $items,
        ], 200);
    }

    public function productOptions($id)
    {
        $data = Product::with(['variants', 'productSizes'])->find($id);

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        $sizes = AttributeValue::whereNotNull('size_value')->get()->select('id', 'size_value');
        $selectedSizes = $data->variants->pluck('size_value')->filter()->unique()->toArray();
        $productSizes = $data->productSizes;

        return response()->json([
            'status' => true,
            'sizes' => $sizes,
            'selectedSizes' => $selectedSizes,
            'productSizes' => $productSizes,
        ], 200);
    }

    public function updateOptions(Request $request, $id)
    {
        $product = Product::with(['variants', 'productSizes'])->findOrFail($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        // Get unique sizes from request
        $sizes = array_unique($request->input('sizes', []));

        if (!empty($sizes)) {
            $currentVariants = ProductVariant::where('product_id', $product->id)->get();

            $existingVariants = [];
            foreach ($currentVariants as $variant) {
                $key = $variant->size_value ?? '';
                $existingVariants[$key] = $variant;
            }

            $newVariantKeys = [];

            if (!empty($sizes)) {
                foreach ($sizes as $size) {
                    $key = $size;
                    $newVariantKeys[] = $key;

                    if (!array_key_exists($key, $existingVariants)) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'size_value' => $size,
                            'color_value' => null,
                        ]);
                    }
                }
            }

            // Remove variants that are no longer in sizes
            foreach ($existingVariants as $key => $variant) {
                if (!in_array($key, $newVariantKeys)) {
                    $variant->delete();
                }
            }
        }

        if (
            $request->filled('length_cm') ||
            $request->filled('length_in') ||
            $request->filled('width_cm') ||
            $request->filled('width_in') ||
            $request->filled('height_cm') ||
            $request->filled('height_in')
        ) {
            ProductSize::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'length_cm' => $request->length_cm,
                    'length_in' => $request->length_in,
                    'width_cm' => $request->width_cm,
                    'width_in' => $request->width_in,
                    'height_cm' => $request->height_cm,
                    'height_in' => $request->height_in,
                ]
            );
        }


        return response()->json([
            'status' => true,
            'message' => 'Product sizes updated successfully.'
        ], 200);
    }

    public function productDetails($id)
    {
        try {
            $user = auth('api')->user();
            $product = Product::with([
                'gellary_images:product_id,image',
                'productSizes'
                // 'reviews.user:id,name,email,avatar'
            ])
                ->where('id', $id)
                ->where('status', 1)
                ->where(function ($query) {
                    $query->whereNull('expire_date')
                        ->orWhere('expire_date', '>', now());
                })
                ->first();

            // If product is not found, return 404 response
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found or inactive/expired.',
                ], 404);
            }

            $product->wishlist_count = Wishlist::where('product_id', $product->id)->count();

            // Check if the product is wishlisted by the user
            $product->is_wishlisted = false;
            if ($user) {
                $product->is_wishlisted = Wishlist::where('product_id', $product->id)
                    ->where('user_id', $user->id)
                    ->exists();
            }

            $variants = ProductVariant::where('product_id', $product->id)->get();

            $sizes = $variants->filter(function ($variant) {
                return $variant->size_value;
            })->map(function ($variant) {
                $attribute = AttributeValue::where('size_value', $variant->size_value)->first();
                return [
                    'glass_opacity' => $variant->size_value,
                    'option' => $attribute ? $attribute->option : null
                ];
            })->unique()->values();

            return response()->json([
                'success' => true,
                'code' => 200,
                'data' => $product,
                'sizes' => $sizes,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function productDelete($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        if ($product->user_id !== auth('api')->id()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        // Delete the product
        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully.',
        ], 200);
    }

    public function searchProducts(Request $request)
    {
        $user = auth('api')->user();
        $search = $request->get('query');

        $products = Product::with(['user:id,name,avatar', 'category', 'tags'])
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('expire_date')
                    ->orWhere('expire_date', '>', now());
            })
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('category', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('tags', fn($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->get(['id', 'user_id', 'name', 'thumb_image', 'coin', 'slug']);

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
        SearchHistory::create([
            'user_id' => $user->id,
            'search_query' => $search
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Search query recorded successfully.',
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
            'message' => 'Sorted results',
            'total' => $result->count(),
            'data' => $result,
        ]);
    }

    public function getCategories()
    {
        $categories = collect(config('categories'))->map(function ($category, $key) {
            $subcategories = SubCategory::where('category_key', $key)->get(['id', 'category_key', 'name', 'image']);

            return [
                'key' => $key,
                'name' => $category['name'] ?? $category,
                'image' => $category['image'] ?? null,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $categories,
        ], 200);
    }

    public function getSubCategories(Request $request){
        $categoryKey = $request->input('category_key');

        if (!$categoryKey) {
            return response()->json([
                'status' => false,
                'message' => 'Category key is required',
            ], 400);
        }

        $subcategories = SubCategory::where('category_key', $categoryKey)->get(['id', 'category_key', 'name', 'image']);

        return response()->json([
            'status' => true,
            'data' => $subcategories,
        ]);
    }

    public function productList(){
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $products = Product::with(['user:id,name,avatar'])
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('expire_date')
                    ->orWhere('expire_date', '>', now());
            })
            ->get(['id', 'user_id', 'name', 'thumb_image', 'coin', 'slug']);

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
            'message' => 'Product list',
            'total' => $result->count(),
            'data' => $result,
        ]);
    }

    public function categoryProductList(Request $request, $category){
        $user = auth('api')->user();

        $products = Product::with(['user:id,name,avatar'])
            ->where('status', 1)
            ->where('category', $category)
            ->where(function ($q) {
                $q->whereNull('expire_date')
                    ->orWhere('expire_date', '>', now());
            })
            ->get(['id', 'user_id', 'name', 'thumb_image', 'coin', 'slug']);

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
            'message' => 'Category product list',
            'total' => $result->count(),
            'data' => $result,
        ]);
    }

    public function subcategoryProductList(Request $request, $subcategory){
        $user = auth('api')->user();

        $products = Product::with(['user:id,name,avatar'])
            ->where('status', 1)
            ->where('subcategory_id', $subcategory)
            ->where(function ($q) {
                $q->whereNull('expire_date')
                    ->orWhere('expire_date', '>', now());
            })
            ->get(['id', 'user_id', 'name', 'thumb_image', 'coin', 'slug']);

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
            'message' => 'Subcategory product list',
            'total' => $result->count(),
            'data' => $result,
        ]);
    }

}
