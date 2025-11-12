<?php

namespace App\Http\Controllers\Web\Backend\Charity\Wishlist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CharityWishlist;
use App\Models\WishlistList;
use Yajra\DataTables\DataTables;

class CharityWishlistListController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = WishlistList::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                   return $row->title;
                })
                ->addColumn('wishlist', function ($row) {
                   return $row->wishlist->title ?? '';
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
                                <a href="' . route('wishlist-list.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>
                                <button class="item trash" data-id="' . $row->id . '"><i class="icon-trash-2"></i></button>
                            </div>';
                })
                ->rawColumns(['image', 'name', 'product', 'status', 'action', 'is_featured', 'menu_featured'])
                ->make(true);
        }

        return view('backend.layouts.charity.wishlist.gift.list');
    }

    public function create()
    {
        $wishlists = CharityWishlist::all();
        return view('backend.layouts.charity.wishlist.gift.create', compact('wishlists'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'wishlist' => 'required'
        ]);

        $data = WishlistList::create([
            'title' => $validated['name'],
            'charity_wishlist_id' => $request->wishlist,
            'note' => $request->note,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Gift created successfully.',
        ]);
    }

    public function update(Request $request, string $id)
    {
        $user = WishlistList::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'wishlist' => 'required'
        ]);

        $user->title = $validated['name'];
        $user->charity_wishlist_id = $request->wishlist;
        $user->note = $request->note;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist item updated successfully.',
            'admin' => $user
        ]);

    }

    public function edit(string $id)
    {
        $wishlists = CharityWishlist::all();
        $data = WishlistList::findOrFail($id);
        return view('backend.layouts.charity.wishlist.gift.edit', compact('wishlists', 'data'));
    }

    public function destroy(string $id)
    {
        $data = WishlistList::findOrFail($id);
        $data->delete();

        return response()->json(['message' => 'Category deleted successfully.']);
    }

    public function updateStatus(Request $request)
    {
        // Find the brand
        $data = WishlistList::findOrFail($request->id);

        // Update the status
        $data->status = $request->status;
        $data->save();

        $message = $request->status == 'inactive' ? "{$data->title} is inactive" : "{$data->title} is active";
        $type = $request->status == 'inactive' ? 'info' : 'success';

        return response()->json(['message' => $message, 'type' => $type]);
    }


     public function gifts(Request $request)
    {
        if ($request->ajax()) {

            $data = WishlistList::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                
                ->rawColumns(['image', 'name', 'product', 'status', 'action', 'is_featured', 'menu_featured'])
                ->make(true);
        }

        return view('backend.layouts.charity.wishlist.gift.gifts');
    }

}
