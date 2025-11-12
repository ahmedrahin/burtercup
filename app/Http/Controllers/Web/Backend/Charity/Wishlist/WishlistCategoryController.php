<?php

namespace App\Http\Controllers\Web\Backend\Charity\Wishlist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WishlistCategory;
use Yajra\DataTables\DataTables;

class WishlistCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = WishlistCategory::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    $indent = isset($row->is_sub) && $row->is_sub ? '&nbsp;&nbsp;&nbsp;&nbsp;↳ ' : '';
                    return $indent . $row->name;
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
                                <a href="' . route('wishlist-category.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>
                                <button class="item trash" data-id="' . $row->id . '"><i class="icon-trash-2"></i></button>
                            </div>';
                })
                ->rawColumns(['image', 'name', 'product', 'status', 'action', 'is_featured', 'menu_featured'])
                ->make(true);
        }

        return view('backend.layouts.charity.wishlist.category.list');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layouts.charity.wishlist.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $data = WishlistCategory::create([
            'name' => $validated['name'],
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
        $data = WishlistCategory::find($id);
        return view('backend.layouts.charity.wishlist.category.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $edit = WishlistCategory::find($id);

        $edit->update([
            'name' => $validated['name'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'data' => $edit
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = WishlistCategory::findOrFail($id);
        $data->delete();

        return response()->json(['message' => 'Category deleted successfully.']);
    }


    public function updateStatus(Request $request)
    {
        // Find the brand
        $data = WishlistCategory::findOrFail($request->id);

        // Update the status
        $data->status = $request->status;
        $data->save();

        $message = $request->status == 'inactive' ? "{$data->name} is inactive" : "{$data->name} is active";
        $type = $request->status == 'inactive' ? 'info' : 'success';

        return response()->json(['message' => $message, 'type' => $type]);
    }
}
