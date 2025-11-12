<?php

namespace App\Http\Controllers\Web\Backend\Charity\Wishlist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WishlistCategory;
use App\Models\CharityWishlist;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use Illuminate\Support\Facades\File;

class CharityWishlistController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = CharityWishlist::orderBy('id', 'desc')->get();

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
                    return $row->title;
                })

                ->addColumn('category', function ($row) {
                    return $row->category->name;
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
                                <a href="' . route('wishlist.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>
                                <button class="item trash" data-id="' . $row->id . '"><i class="icon-trash-2"></i></button>
                            </div>';
                })

                ->rawColumns(['image', 'name', 'product', 'status', 'action', 'is_featured', 'category'])
                ->make(true);
        }

        return view('backend.layouts.charity.wishlist.list');
    }

    public function create()
    {
        $categories = WishlistCategory::get();
        return view('backend.layouts.charity.wishlist.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'coin' => 'required'
        ]);

        if ($request->hasFile('image')) {
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'wishlist', $randomString);
        }

        $data = CharityWishlist::create([
            'title' => $validated['name'],
            'image' => $imagePath ?? null,
            'category_id' => $request->category,
            'coin' => $request->coin
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Wishlist created successfully.',
            'data' => $data
        ]);
    }

    public function edit(string $id)
    {
        $data = CharityWishlist::findOrFail($id);
        $categories = WishlistCategory::get();
        return view('backend.layouts.charity.wishlist.edit', compact('categories', 'data'));
    }

    public function update(Request $request, string $id)
    {
        $user = CharityWishlist::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'coin' => 'required'
        ]);

        if($request->hasFile('image')) {
            if ($user->image && File::exists(public_path($user->image))) {
                File::delete(public_path($user->image));
            }

            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'wishlist', $randomString);

            if ($user->image && file_exists(public_path($user->image))) {
                @unlink(public_path($user->image));
            }

            $user->image = $imagePath;
        }

        $user->title = $validated['name'];
        $user->coin = $request->coin;
        $user->note = $request->note;
        $user->category_id = $request->category;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist updated successfully.',
            'admin' => $user
        ]);

    }

    public function destroy(string $id)
    {
        $data = CharityWishlist::findOrFail($id);
        $data->delete();

        return response()->json(['message' => 'Category deleted successfully.']);
    }


    public function updateStatus(Request $request)
    {
        // Find the brand
        $data = CharityWishlist::findOrFail($request->id);

        // Update the status
        $data->status = $request->status;
        $data->save();

        $message = $request->status == 'inactive' ? "{$data->title} is inactive" : "{$data->title} is active";
        $type = $request->status == 'inactive' ? 'info' : 'success';

        return response()->json(['message' => $message, 'type' => $type]);
    }

}
