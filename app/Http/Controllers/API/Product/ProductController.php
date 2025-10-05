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
use App\Models\ProductGellary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class ProductController extends Controller
{
    public function selectedCategories()
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $userCategories = $user->categories ?? [];

        if (!is_array($userCategories)) {
            $userCategories = json_decode($userCategories, true) ?? [];
        }

        if (empty($userCategories)) {
            return response()->json([
                'status' => true,
                'data' => [],
            ], 200);
        }

        $data = [];

        foreach ($userCategories as $categoryName) {

            // Convert user category name to snake_case to match config keys
            $configKey = Str::snake($categoryName);

            // Get category config
            $categoryConfig = config("categories.$configKey");

            if (!$categoryConfig) {
                $categoryConfig = [
                    'id'    => null,
                    'name'  => $categoryName,
                    'image' => null,
                ];
            }

            // Fetch subcategories using category_id
            $subs = SubCategory::where('category_id', $categoryConfig['id'])
                ->get(['id', 'name', 'image','category_id'])
                ->map(fn($sub) => [
                    'id'    => $sub->id,
                    'name'  => $sub->name,
                    'image' => $sub->image ? asset($sub->image) : null,
                    'category_id' => $sub->category_id
                ]);

            $data[] = [
                'id'            => $categoryConfig['id'],
                'key'           => $configKey,
                'name'          => $categoryConfig['name'],
                'image'         => $categoryConfig['image'] ? asset($categoryConfig['image']) : null,
                'subcategories' => $subs,
            ];
        }

        return response()->json([
            'status'  => true,
            'code'    => 200,
            'message' => 'User selected categories with subcategories',
            'data'    => $data,
        ], 200);
    }

    public function addProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:products,name',
            'quantity' => 'required|min:1',
            'coins' => 'required',
            'category_id' => 'required',
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

        $category = collect(config('categories'))->where('id', $request->category_id)->first();

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
            ], 404);
        }

        if($request->subcategory_id){
            $subcategory = SubCategory::find($request->subcategory_id);
            if (!$subcategory) {
                return response()->json([
                    'status' => false,
                    'message' => 'Subcategory not found',
                ], 404);
            }
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
            'category_id' => $request->category_id,
            'category' => $category['name'],
            'subcategory' => $subcategory->name ?? null,
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
            'code' => 200,
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
            'code' => 200,
            'message' => 'Product edit fatched.',
            'data' => $data,
        ], 200);

    }

    public function productUpdate(Request $request, $id)
    {
        $product = Product::findOrFail($id);

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

        $category = collect(config('categories'))->where('id', $request->category_id)->first();
        if($request->subcategory_id){
            $subcategory = SubCategory::find($request->subcategory_id);
            if (!$subcategory) {
                return response()->json([
                    'status' => false,
                    'message' => 'Subcategory not found',
                ], 404);
            }
        }

        if (!$category && $request->category_id) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found',
            ], 404);
        }

        // Update product fields
        $product->name = $request->name ?? $product->name;
        $product->coin = $request->coins ?? $product->coin;
        $product->quantity = $request->quantity ?? $product->quantity;
        $product->description = $request->description ?? $product->description;
        $product->status = $request->status ?? $product->status;
        $product->is_new = $request->is_new ?? $product->is_new;
        $product->is_featured = $request->is_featured ?? $product->is_featured;
        $product->category_id = $request->category_id ?? $product->category_id;
        $product->condition = $request->condition ?? $product->condition;
        $product->subcategory_id = $request->subcategory_id ?? $product->subcategory_id;
        $product->category = $category['name'] ?? $product->category;
        $product->subcategory = $subcategory->name ?? $product->subcategory;

        $product->save();

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Product updated successfully.',
            'data' => $product,
        ], 200);
    }

    public function galleryAdd(Request $request, $id){
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found.',
            ], 404);
        }

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
            'code' => 200,
            'message' => 'Product gallery images added successfully.',
            'data' => $product->gellary_images,
        ], 200);
    }

    public function galleryUpdate(Request $request, $id)
    {
        $gallery = ProductGellary::find($id);

        if (!$gallery) {
            return response()->json([
                'status' => false,
                'message' => 'Product Gallery not found.',
            ], 404);
        }

        if ($request->hasFile('gellary_image')) {
            if (file_exists(public_path($gallery->image))) {
                unlink(public_path($gallery->image));
            }

            $randomString = (string) Str::uuid();
            $galleryImagePath = Helper::fileUpload($request->file('gellary_image'), 'product/gellary', $randomString);

            $gallery->update([
                'image' => $galleryImagePath,
            ]);
        }

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Product gallery updated successfully.',
            'data' => $gallery,
        ], 200);
    }

    public function galleryDelete(Request $request, $id)
    {
        $gallery = ProductGellary::find($id);

        if (!$gallery) {
            return response()->json([
                'status' => false,
                'message' => 'Product Gallery not found.',
            ], 404);
        }

        if (file_exists(public_path($gallery->image))) {
            unlink(public_path($gallery->image));
        }
        $gallery->delete();

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'Product gallery image deleted successfully.',
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
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('expire_date')
                    ->orWhere('expire_date', '>', now());
            })
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
             'code' => 200,
            'message' => 'My items product fatched',
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
            'code' => 200,
            'message' => 'Product variant or options.',
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
            'code' => 200,
            'message' => 'Product sizes updated successfully.',
            'data' => $sizes
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
                    'size' => $variant->size_value,
                    'option' => $attribute ? $attribute->option : null
                ];
            })->unique()->values();

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'product details data',
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
            'code' => 200,
            'message' => 'Product deleted successfully.',
        ], 200);
    }

    public function getCategories()
    {
         $categories = collect(config('categories'))->map(function ($category, $key) {
            $subcategories = SubCategory::where('category_id', $category['id'])->get(['id', 'category_id', 'name', 'image']);

            return [
                'id' => $category['id'],
                'key' => $key,
                'name' => $category['name'] ?? $category,
                'image' => $category['image'] ?? null,
                'subcategories' => $subcategories,
                'product_count' => Product::where('category_id', $category['id'])->count(),
            ];
        })->values();

        return response()->json([
            'status' => true,
            'code' => 200,
            'message' => 'all categories list with subcategories',
            'data' => $categories,
        ], 200);
    }

    public function getSubCategories(Request $request)
    {
        $categoryKey = $request->input('id');

        if (!$categoryKey) {
            return response()->json([
                'status' => false,
                'message' => 'Category key is required',
            ], 400);
        }

        $subcategories = SubCategory::where('category_id', $categoryKey)->get(['id', 'category_id', 'category_key', 'name', 'image']);

        return response()->json([
            'status' => true,
             'code' => 200,
            'message' => 'get subcategories',
            'data' => $subcategories,
        ]);
    }

    public function productList()
    {
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
             'code' => 200,
            'message' => 'Product list',
            'total' => $result->count(),
            'data' => $result,
        ]);
    }

    public function categoryProductList(Request $request, $category)
    {
        $user = auth('api')->user();

        $products = Product::with(['user:id,name,avatar'])
            ->where('status', 1)
            ->where('category_id', $category)
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
            'code' => 200,
            'message' => 'Category product list',
            'total' => $result->count(),
            'data' => $result,
        ]);
    }

    public function subcategoryProductList(Request $request, $subcategory)
    {
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
            'code' => 200,
            'message' => 'Subcategory product list',
            'total' => $result->count(),
            'data' => $result,
        ]);
    }

}
