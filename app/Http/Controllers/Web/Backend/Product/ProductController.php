<?php

namespace App\Http\Controllers\Web\Backend\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use App\Models\PorductTag;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use App\Models\ProductSize;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('image', function ($row) {
                    $imageUrl = $row->thumb_image ? asset($row->thumb_image) : asset('blank-image.svg');
                    return '<div class="popup-gallery">
                                <a href="' . $imageUrl . '" class="popup-image">
                                    <img src="' . $imageUrl . '" width="50" style="border-radius:5px;">
                                </a>
                            </div>';
                })

                ->addColumn('name', function ($row) {
                    $name = '<a href="" target="_blank" style="font-weight:600;">' . Str::limit($row->name, 25) . '</a>';

                    if ($row->expire_date && Carbon::parse($row->expire_date)->isFuture()) {
                        $remaining = Carbon::now()->diff(Carbon::parse($row->expire_date))->forHumans([
                            'parts' => 2,
                            'join' => true,
                            'short' => true,
                        ]);

                        $name .= '<div style="font-size:11px; color:#a7a7a7;">' . $remaining . ' left</div>';
                    }

                    return $name;
                })

                ->addColumn('quantity', function ($row) {
                    if( $row->quantity > 5 ){
                        $qty = '<div class="badge bg-success">' . $row->quantity . '</div>';
                    }
                    elseif( $row->quantity <= 5 && $row->quantity >= 1 ){
                        $qty = '<div class="badge bg-warning">' . $row->quantity . '</div>';
                    }
                    else {
                        $qty = '<div class="badge bg-danger">out of stock</div>';
                    }
                    return $qty;
                })

                ->addColumn('coin', function ($row) {
                    return $row->coin;
                })

                ->editColumn('category', function ($row) {
                    return optional($row->category)->name ?? '<span class="text-danger" style="opacity: .7;font-size: 12px;">Uncategorized</span>';
                })

                ->editColumn('selling', function ($row) {
                    // $totalSales = $row->orderItems()->sum('quantity');
                    // $badgeClass = 'badge-light-primary';
                    // $label = $totalSales;

                    // if ($totalSales == 0) {
                    //     $badgeClass = 'badge-light-danger';
                    //     $label = 'No Sale';
                    // } elseif ($totalSales > 10) {
                    //     $badgeClass = 'badge-light-success';
                    //     $label = $totalSales;
                    // }
                    return '<span class="badge bg-danger">' . 0 . '</span>';
                })

                ->editColumn('is_featured', function ($row) {
                    $checked = $row->is_featured ? 'checked' : '';
                    return '<input type="checkbox" class="is-featured-checkbox" data-id="' . $row->id . '" ' . $checked . '>';
                })

                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d M, Y') : '';
                })

                ->addColumn('status', function ($row) {
                    $isExpired = $row->expire_date && Carbon::parse($row->expire_date)->isPast();

                    if ($isExpired) {
                        return '<span class="badge bg-danger">Expired</span>';
                    }

                    $checked = $row->status == 1 ? 'checked' : '';
                    return '
                        <label class="custom-switch">
                            <input type="checkbox" class="status-switch" id="status-' . $row->id . '" ' . $checked . ' data-id="' . $row->id . '">
                            <span class="slider"></span>
                        </label>';
                })

                ->addColumn('action', function ($row) {
                    return '<div class="list-icon-function flex items-center gap-2 justify-end">
                                <a class="item eye" href="' . route("product.show", $row->id) .'" target="_blank"><i class="icon-eye"></i></a>
                                <a href="' . route('product.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>

                            </div>';
                })
                ->rawColumns(['image', 'name', 'quantity', 'status', 'action', 'is_featured', 'price', 'category', 'selling'])
                ->make(true);
        }

        return view('backend.layouts.product.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sizes = AttributeValue::whereNotNull('size_value')->get();
        $colors = AttributeValue::whereNotNull('color_value')->get();
        return view('backend.layouts.product.create', compact('sizes', 'colors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $rules = [
            'name'                      => 'required|string|unique:products,name',
            'quantity'                  => 'required|min:1',
            'image'                     => 'required|image',
            'status'                    => 'required|boolean|in:1,2,3,0',
            'expire_date'               => 'nullable|date|after_or_equal:now',
            'coins'                     => 'required',
        ];

        $messages = [
            'expire_date.after_or_equal'  => 'The expiry date must be a current or future time.',
            'image.required' => 'Select a thumbnail image',
        ];

        $validated = $request->validate($rules, $messages);


        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        if ($request->hasFile('image')) {
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'product', $randomString);
        }


        // Ensure unique slug
        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $count = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        $data = [
            'name' => $validated['name'],
            'slug' => $slug,
            'coin' => $request->coins,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'status' => $request->status,
            'is_new' => $request->is_new ?? 0,
            'is_featured' => $request->is_featured ?? 0,
            'user_id' => auth()->id(),
            'add_source' => 'app',
            'category_id' => $request->category
        ];

        if ($request->has('expire_date') && !empty($request->expire_date)) {
            try {
                $expireDate = trim($request->expire_date);
                $data['expire_date'] = Carbon::createFromFormat('Y-m-d h:i A', $expireDate)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                dd("Error formatting expire_date: " . $e->getMessage());
            }
        } else {
            $data['expire_date'] = null;
        }

        if (isset($imagePath)) {
            $data['thumb_image'] = $imagePath;
        }

        $product = Product::create($data);

        $sizes = array_unique($request->input('sizes', []));
        $colors = array_unique($request->input('colors', []));

        if (!empty($sizes) || !empty($colors)) {
            if (!empty($sizes) && !empty($colors)) {
                foreach ($sizes as $size) {
                    ProductVariant::create([
                        'product_id'  => $product->id,
                        'size_value'  => $size,
                    ]);
                }

                foreach ($colors as $color) {
                    ProductVariant::create([
                        'product_id'  => $product->id,
                        'color_value' => $color,
                    ]);
                }
            }
            elseif (!empty($sizes)) {
                foreach ($sizes as $size) {
                    ProductVariant::create([
                        'product_id'  => $product->id,
                        'size_value'  => $size,
                        'color_value' => null,
                    ]);
                }
            }
            elseif (!empty($colors)) {
                foreach ($colors as $color) {
                    ProductVariant::create([
                        'product_id'  => $product->id,
                        'size_value'  => null,
                        'color_value' => $color,
                    ]);
                }
            }
        }

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

        if( $request->tags && !is_null($request->tags) ){
            $this->storeTags($request, $product);
        }

       // Check if at least one size field is filled
        if (
            $request->filled('length_cm') ||
            $request->filled('length_in') ||
            $request->filled('width_cm')  ||
            $request->filled('width_in')  ||
            $request->filled('height_cm') ||
            $request->filled('height_in')
        ) {
            ProductSize::create([
                'product_id' => $product->id,
                'length_cm'  => $request->length_cm,
                'length_in'  => $request->length_in,
                'width_cm'   => $request->width_cm,
                'width_in'   => $request->width_in,
                'height_cm'  => $request->height_cm,
                'height_in'  => $request->height_in,
            ]);
        }


        return response()->json([
            'message' => 'Product created successfully!',
            'product' => $product->id,
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with([
            'category:id,name',
            'brand:id,name',
            'gellary_images:id,product_id,image',
            'tags:id,product_id,name',
        ])->find($id);
        return view('backend.layouts.product.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Product::with(['tags', 'gellary_images', 'variants'])->find($id);
        $sizes = AttributeValue::whereNotNull('size_value')->get();
        $colors = AttributeValue::whereNotNull('color_value')->get();

        // Extract selected sizes/colors from product variants
        $selectedSizes = $data->variants->pluck('size_value')->filter()->unique()->toArray();
        $selectedColors = $data->variants->pluck('color_value')->filter()->unique()->toArray();

        $options = optional($data->productSizes->first());


        return view('backend.layouts.product.edit', compact('data', 'sizes', 'colors', 'selectedSizes', 'selectedColors', 'options'));
    }


    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, $id)
    {
        $product = Product::with(['gellary_images', 'variants', 'productSizes'])->findOrFail($id);

        // Validation
        $rules = [
            'name'       => 'required|string|unique:products,name,' . $product->id,
            'status'     => 'required|boolean|in:1,2,3,0',
            'expire_date'=> 'nullable|date|after_or_equal:now',
            'quantity'   => 'required|min:1',
            'coins'      => 'required',
        ];

        $validated = $request->validate($rules);

        // Handle thumbnail image
        if ($request->hasFile('image')) {
            if ($product->thumb_image && file_exists(public_path($product->thumb_image))) {
                unlink(public_path($product->thumb_image));
            }
            $imagePath = Helper::fileUpload($request->file('image'), 'product', (string) Str::uuid());
            $product->thumb_image = $imagePath;
        }

        // Update core product info
        $product->update([
            'name'        => $validated['name'],
            'description' => $request->description,
            'status'      => $request->status,
            'quantity'    => $validated['quantity'],
            'coin'        => $request->coins,
            'expire_date' => $request->expire_date ? Carbon::parse($request->expire_date)->format('Y-m-d H:i:s') : null,
            'is_new'      => $request->is_new ?? 0,
            'is_featured' => $request->is_featured ?? 0,
        ]);

        // Handle sizes/colors
        $sizes = array_unique($request->input('sizes', []));
        $colors = array_unique($request->input('colors', []));

        $currentVariants = ProductVariant::where('product_id', $product->id)->get();
        $existingVariants = [];

        foreach ($currentVariants as $variant) {
            $key = ($variant->size_value ?? '') . '|' . ($variant->color_value ?? '');
            $existingVariants[$key] = $variant;
        }

        $newVariantKeys = [];

        if (!empty($sizes) && !empty($colors)) {
            foreach ($sizes as $size) {
                foreach ($colors as $color) {
                    $key = $size . '|' . $color;
                    $newVariantKeys[] = $key;
                    if (!array_key_exists($key, $existingVariants)) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'size_value' => $size,
                            'color_value'=> $color,
                        ]);
                    }
                }
            }
        } elseif (!empty($sizes)) {
            foreach ($sizes as $size) {
                $key = $size . '|';
                $newVariantKeys[] = $key;
                if (!array_key_exists($key, $existingVariants)) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size_value' => $size,
                        'color_value'=> null,
                    ]);
                }
            }
        } elseif (!empty($colors)) {
            foreach ($colors as $color) {
                $key = '|' . $color;
                $newVariantKeys[] = $key;
                if (!array_key_exists($key, $existingVariants)) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'size_value' => null,
                        'color_value'=> $color,
                    ]);
                }
            }
        }

        // Remove old variants
        foreach ($existingVariants as $key => $variant) {
            if (!in_array($key, $newVariantKeys)) {
                $variant->delete();
            }
        }

        // Handle dimensions (length, width, height) using updateOrCreate
        if (
            $request->filled('length_cm') ||
            $request->filled('length_in') ||
            $request->filled('width_cm')  ||
            $request->filled('width_in')  ||
            $request->filled('height_cm') ||
            $request->filled('height_in')
        ) {
            ProductSize::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'length_cm' => $request->length_cm,
                    'length_in' => $request->length_in,
                    'width_cm'  => $request->width_cm,
                    'width_in'  => $request->width_in,
                    'height_cm' => $request->height_cm,
                    'height_in' => $request->height_in,
                ]
            );
        }

        // Handle new gallery images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $galleryImagePath = Helper::fileUpload($image, 'product/gellary', (string) Str::uuid());
                $product->gellary_images()->create(['image' => $galleryImagePath]);
            }
        }

        // Handle tags
        if ($request->filled('tags')) {
            $this->updateTags($request, $product);
        }

         return redirect()->route('product.index')->with('success', 'Product updated successfully!');
    }



    private function storeTags(Request $request, Product $product): void
    {
        $tags = explode(',', $request->tags);
        $tags = array_map('trim', $tags);

        foreach ($tags as $tag) {
            PorductTag::create([
                    'product_id' => $product->id,
                    'name' => $tag,
                ]);
            }
    }

    private function updateTags(Request $request, Product $product): void
    {
        $newTags = explode(',', $request->tags);
        $newTags = array_map('trim', $newTags);
        $newTags = array_filter($newTags);
        $existingTags = $product->tags->pluck('name')->map('trim')->toArray();

        sort($existingTags);
        sort($newTags);

        if ($existingTags === $newTags) {
            return;
        }

        $product->tags()->delete();

        foreach ($newTags as $tag) {
            PorductTag::create([
                'product_id' => $product->id,
                'name' => $tag,
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'status' => 'required|in:0,1',
        ]);

        $product = Product::findOrFail($request->id);
        $product->status = $request->status;
        $product->save();

        $message = $request->status == 0 ? "{$product->name} is inactive" : "{$product->name} is active";
        $type = $request->status == 0 ? 'info' : 'success';

        return response()->json([
            'message' => $message,
            'type' => $type
        ]);
    }

    public function toggleFeatured(Request $request)
    {
        $category = Product::find($request->id);
        if ($category) {
            $category->is_featured = $request->is_featured;
            $category->save();

            return response()->json(['status' => true, 'message' => 'Featured status updated']);
        }

        return response()->json(['status' => false, 'message' => 'Category not found'], 404);
    }
}
