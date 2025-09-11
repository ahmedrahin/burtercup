<?php

namespace App\Http\Controllers\Web\Backend\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GameCategory;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use App\Models\Product;
use Yajra\DataTables\DataTables;

class VersusCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = GameCategory::orderBy('id', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('name', function ($row) {
                    $indent = isset($row->is_sub) && $row->is_sub ? '&nbsp;&nbsp;&nbsp;&nbsp;↳ ' : '';
                    return $indent . $row->name;
                })

               ->addColumn('free', function ($row) {
                    return $row->free == 1
                        ? '<span class="badge bg-success">Yes</span>'
                        : '<span class="badge bg-danger">No</span>';
                })

                ->addColumn('premium', function ($row) {
                    return $row->premium == 1
                        ? '<span class="badge bg-success">Yes</span>'
                        : '<span class="badge bg-danger">No</span>';
                })

                ->addColumn('platinum', function ($row) {
                    return $row->platinum == 1
                        ? '<span class="badge bg-success">Yes</span>'
                        : '<span class="badge bg-danger">No</span>';
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
                                <a href="' . route('game-category.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>
                                <button class="item trash" data-id="' . $row->id . '"><i class="icon-trash-2"></i></button>
                            </div>';
                })

                ->rawColumns(['name','status', 'action', 'free', 'premium', 'platinum'])
                ->make(true);
        }

        return view('backend.layouts.game.category.list');
    }

    public function create()
    {
        return view('backend.layouts.game.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        // Create brand
        $data = GameCategory::create([
            'name' => $request->name,
            'free' => $request->free,
            'premium' => $request->premium,
            'platinum' => $request->platinum,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data' => $data
        ]);
    }

    public function edit(string $id)
    {
        $data = GameCategory::find($id);
        return view('backend.layouts.game.category.edit', compact('data',));
    }

    public function update(Request $request, $id)
    {
        $data = GameCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $data->update([
            'name' => $request->name,
            'free' => $request->free,
            'premium' => $request->premium,
            'platinum' => $request->platinum,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data' => $data
        ]);
    }

     public function updateStatus(Request $request)
    {
        // Find the brand
        $data = GameCategory::findOrFail($request->id);

        // Update the status
        $data->status = $request->status;
        $data->save();

        $message = $request->status == 'inactive' ? "{$data->name} is inactive" : "{$data->name} is active";
        $type = $request->status == 'inactive' ? 'info' : 'success';

        return response()->json(['message' => $message, 'type' => $type]);
    }

    public function destroy(string $id)
    {
        $data = GameCategory::findOrFail($id);
        $data->delete();

        return response()->json(['message' => 'Category deleted successfully.']);
    }
    
}
