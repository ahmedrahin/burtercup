<?php

namespace App\Http\Controllers\Web\Backend\Charity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sponsor;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use Yajra\DataTables\DataTables;

class SponsorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Sponsor::orderBy('id', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('image', function ($row) {
                    $imageUrl = $row->image ? asset($row->image) : asset('blank-image.svg');
                    return '<div class="popup-gallery">
                                <a href="' . $imageUrl . '" class="popup-image">
                                    <img src="' . $imageUrl . '" width="50" style="border-radius:5px;">
                                </a>
                            </div>';
                })

                ->addColumn('action', function ($row) {
                    return '<div class="list-icon-function flex items-center gap-2 justify-end">
                                <a href="' . route('sponsor.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>
                                <button class="item trash" data-id="' . $row->id . '"><i class="icon-trash-2"></i></button>
                            </div>';
                })

                ->rawColumns(['name', 'status', 'action', 'image', 'option'])
                ->make(true);
        }

        return view('backend.layouts.sponsor.list');
    }

    public function create()
    {
        return view('backend.layouts.sponsor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image',
        ]);

        $data = new Sponsor();
        $data->link = $request->input('link');
        $data->details = $request->input('details');

        if ($request->hasFile('image')) {
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'sponsor', $randomString);
            $data->image = $imagePath;
        }

        if ($request->hasFile('latest_1')) {
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('latest_1'), 'sponsor', $randomString);
            $data->latest_1 = $imagePath;
        }
        if ($request->hasFile('latest_1')) {
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('latest_2'), 'sponsor', $randomString);
            $data->latest_2 = $imagePath;
        }
        if ($request->hasFile('latest_3')) {
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('latest_3'), 'sponsor', $randomString);
            $data->latest_3 = $imagePath;
        }

        $data->save();
        return redirect()->route('sponsor.index')->with('success', 'Sponsor created successfully.');
    }

    public function edit($id)
    {
        $data = Sponsor::findOrFail($id);
        return view('backend.layouts.sponsor.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'nullable|image',
            'latest_1' => 'nullable|image',
            'latest_2' => 'nullable|image',
            'latest_3' => 'nullable|image',
        ]);

        $data = Sponsor::findOrFail($id);
        $data->link = $request->input('link');
        $data->details = $request->input('details');

        // Helper for deleting old file safely
        $deleteOldFile = function ($oldPath) {
            if ($oldPath && file_exists(public_path($oldPath))) {
                @unlink(public_path($oldPath));
            }
        };

        // ✅ Main image
        if ($request->hasFile('image')) {
            $deleteOldFile($data->image);
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'sponsor', $randomString);
            $data->image = $imagePath;
        }

        // ✅ latest_1
        if ($request->hasFile('latest_1')) {
            $deleteOldFile($data->latest_1);
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('latest_1'), 'sponsor', $randomString);
            $data->latest_1 = $imagePath;
        }

        // ✅ latest_2
        if ($request->hasFile('latest_2')) {
            $deleteOldFile($data->latest_2);
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('latest_2'), 'sponsor', $randomString);
            $data->latest_2 = $imagePath;
        }

        // ✅ latest_3
        if ($request->hasFile('latest_3')) {
            $deleteOldFile($data->latest_3);
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('latest_3'), 'sponsor', $randomString);
            $data->latest_3 = $imagePath;
        }

        $data->save();

        return redirect()->route('sponsor.index')->with('success', 'Sponsor updated successfully.');
    }

    public function destroy($id)
    {
        $data = Sponsor::findOrFail($id);

        $paths = [
            $data->image,
            $data->latest_1,
            $data->latest_2,
            $data->latest_3,
        ];

        foreach ($paths as $path) {
            if ($path && file_exists(public_path($path))) {
                @unlink(public_path($path));
            }
        }

        $data->delete();

        return response()->json(['message' => 'Sponsore deleted successfully.']);
    }

     public function list(Request $request)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to access your profile.',
                'code' => 401,
            ], 401);
        }

        $sponsor = Sponsor::orderByDesc('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'programmes retrieved successfully',
            'status' => 200,
            'data' => $sponsor
        ]);
    }

    public function details(Request $request, $id)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to access your profile.',
                'code' => 401,
            ], 401);
        }

        $sponsor = Sponsor::find($id);

        if(!$sponsor){
             return response()->json([
                'status' => false,
                'message' => 'sponsor not found',
                'code' => 401,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'programmes retrieved successfully',
            'status' => 200,
            'data' => $sponsor
        ]);
    }

}
