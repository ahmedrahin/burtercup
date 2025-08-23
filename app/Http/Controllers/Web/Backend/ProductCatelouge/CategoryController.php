<?php

namespace App\Http\Controllers\Web\Backend\ProductCatelouge;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use App\Models\SubCategory;
use Yajra\DataTables\DataTables;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $allCategories = SubCategory::orderBy('id', 'desc')->get();

            $data = collect();
            foreach ($allCategories->whereNull('parent_id') as $parent) {
                $data->push($parent);
                $children = $allCategories->where('parent_id', $parent->id);
                foreach ($children as $child) {
                    $child->is_sub = true;
                    $data->push($child);
                }
            }

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('image', function ($row) {
                    if (!empty($row->image)) {
                        $imageUrl = asset($row->image);
                        return '<div class="popup-gallery">
                                    <a href="' . $imageUrl . '" class="popup-image">
                                        <img src="' . $imageUrl . '" width="50" style="border-radius:5px;">
                                    </a>
                                </div>';
                    } else {
                        $placeholder = asset('blank-image.svg');
                        return '<img src="' . $placeholder . '" width="50" style="border-radius:5px;">';
                    }
                })

                ->addColumn('name', function ($row) {
                    $indent = isset($row->is_sub) && $row->is_sub ? '&nbsp;&nbsp;&nbsp;&nbsp;↳ ' : '';
                    return $indent . $row->name;
                })

                ->addColumn('parent_category', function ($row) {
                    return $row->category_name;
                })


                ->addColumn('status', function ($row) {
                    $status = $row->status == 'active' ? 'checked' : '';
                    return '
                        <label class="custom-switch">
                            <input type="checkbox" class="status-switch" id="status-' . $row->id . '" ' . $status . ' data-id="' . $row->id . '">
                            <span class="slider"></span>
                        </label>
                    ';
                })

                ->addColumn('action', function ($row) {
                    return '<div class="list-icon-function flex items-center gap-2 justify-end">
                                <a href="' . route('category.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>
                                <button class="item trash" data-id="' . $row->id . '"><i class="icon-trash-2"></i></button>
                            </div>';
                })

                ->rawColumns(['image', 'name', 'product', 'status', 'action', 'is_featured', 'menu_featured'])
                ->make(true);
        }

        return view('backend.layouts.category.list');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = config('categories');
        return view('backend.layouts.category.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name' => 'required|string|max:255'
        ]);

        if ($request->hasFile('image')) {
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'category', $randomString);
        }

        // Generate unique slug
        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $count = 1;

        while (SubCategory::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        // Create brand
        $data = SubCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'image' => $imagePath ?? null,
            'category_key' => $request->input('parent_id'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data' => $data
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $categories = config('categories');
        $data = SubCategory::find($id);
        return view('backend.layouts.category.edit', compact('data', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name' => 'required|string|max:255'
        ]);

        $edit = SubCategory::find($id);
        $imagePath = $edit->image; // keep old image as default

        // Handle image remove
        if ($request->has('remove') && $request->remove == 1) {
            if ($edit->image && File::exists(public_path($edit->image))) {
                File::delete(public_path($edit->image));
            }
            $imagePath = null;
        }

        // Handle image upload
        elseif ($request->hasFile('image')) {
            if ($edit->image && File::exists(public_path($edit->image))) {
                File::delete(public_path($edit->image));
            }

            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'category', $randomString);
        }
        // Generate unique slug
        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $count = 1;

        while (SubCategory::where('slug', $slug)->where('id', '!=', $edit->id)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        $edit->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'image' => $imagePath,
            'category_key' => $request->input('parent_id'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'data' => $edit
        ]);
    }

    // Recursive check
    private function isDescendant($categoryId, $potentialParentId)
    {
        $child = Category::find($potentialParentId);
        while ($child) {
            if ($child->id == $categoryId) {
                return true;
            }
            $child = $child->parent;
        }
        return false;
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = SubCategory::findOrFail($id);
        $data->delete();

        return response()->json(['message' => 'Category deleted successfully.']);
    }

    public function toggleFeatured(Request $request)
    {
        $category = Category::find($request->id);
        if ($category) {
            $category->is_featured = $request->is_featured;
            $category->save();

            return response()->json(['status' => true, 'message' => 'Featured status updated']);
        }

        return response()->json(['status' => false, 'message' => 'Category not found'], 404);
    }

    public function toggleMenuFeatured(Request $request)
    {
        $category = Category::find($request->id);

        if (!$category) {
            return response()->json(['status' => false, 'message' => 'Category not found'], 404);
        }

        if ($request->menu_featured == 1) {
            $featuredCount = Category::where('menu_featured', 1)->count();

            if ($featuredCount >= 4 && $category->menu_featured != 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'You can only set maximum 4 featured categories for menu.'
                ]);
            }
        }

        $category->menu_featured = $request->menu_featured;
        $category->save();

        return response()->json(['status' => true, 'message' => 'Featured status updated']);
    }


    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:categories,id',
            'status' => 'required|in:active,inactive',
        ]);

        // Find the brand
        $data = SubCategory::findOrFail($request->id);

        // Update the status
        $data->status = $request->status;
        $data->save();

        $message = $request->status == 'inactive' ? "{$data->name} is inactive" : "{$data->name} is active";
        $type = $request->status == 'inactive' ? 'info' : 'success';

        return response()->json(['message' => $message, 'type' => $type]);
    }

    public function getSubcategories($category_id)
    {
        $subcategories = Category::where('parent_id', $category_id)->get(columns: ['id', 'name']);

        return response()->json($subcategories);
    }

}
