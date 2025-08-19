<?php

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use App\Models\Wishlist;
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

        return response()->json([
            'status' => true,
            'data' => $user->categories,
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
        ];

        if (isset($imagePath)) {
            $data['thumb_image'] = $imagePath;
        }

        $product = Product::create($data);

        $images = $request->file('images');
        if ($request->hasFile('images')) {
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
        $data = Product::with(['variants', 'productSizes'])->find($id);

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
            'images.*' => 'nullable|image',
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

        $product->save();

        // Handle gallery images
        if ($request->hasFile('images')) {
            // Optional: delete old gallery images if needed
            // $product->gellary_images()->delete();

            foreach ($request->file('images') as $image) {
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
                'opacity' => $sizes,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
