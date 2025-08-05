<?php

namespace App\Http\Controllers\Web\Backend\ProductCatelouge;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Helpers\Helper;

class BrandController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $data = Brand::orderBy('id', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('logo', function ($row) {
                    if (!empty($row->logo)) {
                        $imageUrl = asset($row->logo);
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
                    return '<div class="body-text">' . $row->name . '</div>';
                })
                ->addColumn('product', function ($row) {
                    return '<div class="badge ' . ($row->products->count() > 0 ? 'bg-success' : 'bg-danger') . '">' . $row->products->count() . '</div>';
                })
                ->editColumn('description', function ($row) {
                    return str::limit($row->description, 80);
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
                                <a href=" '. route('brand.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>
                                <button class="item trash" data-id="' . $row->id . '"><i class="icon-trash-2"></i></button>
                            </div>';
                })
                ->rawColumns(['logo', 'status', 'action', 'name', 'product'])
                ->make(true);
        }

        return view('backend.layouts.brand.list');
    }

    public function create(){
        return view('backend.layouts.brand.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name' => 'required|string|max:255'
        ]);

        if ($request->hasFile('image')) {
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'brand', $randomString);
        }

        // Generate unique slug
        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $count = 1;

        while (Brand::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        // Create brand
        $data = Brand::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $request->description,
            'logo' => $imagePath ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Brand created successfully.',
            'data' => $data
        ]);
    }

    public function edit(string $id){
        $data = Brand::find($id);
        return view('backend.layouts.brand.edit', compact('data'));
    }

    public function update(Request $request, string $id){
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name' => 'required|string|max:255'
        ]);

        $edit = Brand::find($id);

        if ($request->has('remove') && $request->remove == 1) {
            if ($edit->logo && File::exists(public_path($edit->logo))) {
                File::delete(public_path($edit->logo));
            }
            $edit->logo = null;
        }
        elseif($request->hasFile('image')) {
            if ($edit->logo && File::exists(public_path($edit->logo))) {
                File::delete(public_path($edit->logo));
            }

            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'brand', $randomString);

            if ($edit->logo && file_exists(public_path($edit->logo))) {
                @unlink(public_path($edit->logo));
            }
            $edit->logo = $imagePath;
        }

        // Generate unique slug
        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $count = 1;

        while (Brand::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        // Create brand
        $data = $edit->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $request->description,
            'logo' => $imagePath ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Brand updated successfully.',
            'data' => $data
        ]);
    }

    public function destroy(string $id)
    {
        $data = Brand::findOrFail($id);
        $data->delete();

        return response()->json(['message' => 'Brand deleted successfully.']);
    }


    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:brands,id',
            'status' => 'required|in:active,inactive',
        ]);

        // Find the brand
        $data = Brand::findOrFail($request->id);

        // Update the status
        $data->status = $request->status;
        $data->save();

        $message = $request->status == 'inactive' ? "{$data->name} is inactive" : "{$data->name} is active";
        $type = $request->status == 'inactive' ? 'info' : 'success';

        return response()->json(['message' => $message, 'type' => $type]);
    }


}
