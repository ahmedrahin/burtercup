<?php

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
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
            'add_source' => 'admin',
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

    public function productOptions($id)
    {
        $sizes = AttributeValue::whereNotNull('size_value')->get()->select('id', 'size_value');

        $data = Product::with(['variants', 'productSizes'])->find($id);
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

        // Get unique sizes from request
        $sizes = array_unique($request->input('sizes', []));

        // Fetch existing variants for this product
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

        return response()->json([
            'status' => true,
            'message' => 'Product sizes updated successfully.'
        ], 200);
    }


}
