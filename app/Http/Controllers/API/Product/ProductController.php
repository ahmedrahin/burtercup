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
    public function addProduct(Request $request){

         $validator = Validator::make($request->all(), [
            'name'                      => 'required|string|unique:products,name',
            'quantity'                  => 'required|min:1',
            'coins'                     => 'required',
            'category'                     => 'required',

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
            'status' => $request->status,
            'is_new' => $request->is_new ?? 0,
            'is_featured' => $request->is_featured ?? 0,
            'user_id' => auth()->id(),
            'add_source' => 'admin'
        ];

        if (isset($imagePath)) {
            $data['thumb_image'] = $imagePath;
        }

        $product = Product::create($data);


    }
}
