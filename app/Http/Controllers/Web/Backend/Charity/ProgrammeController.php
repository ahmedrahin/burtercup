<?php

namespace App\Http\Controllers\Web\Backend\Charity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Programme;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use Yajra\DataTables\DataTables;

class ProgrammeController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Programme::orderBy('id', 'desc')->get();

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

                ->addColumn('title', function ($row) {
                    return $row->title;
                })

                ->addColumn('type', function ($row) {
                    return ucfirst($row->type) ?? '';
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
                                <a href="' . route('programmes.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>
                                <button class="item trash" data-id="' . $row->id . '"><i class="icon-trash-2"></i></button>
                            </div>';
                })

                ->rawColumns(['name', 'status', 'action', 'image', 'option'])
                ->make(true);
        }

        return view('backend.layouts.charity.programme.list');
    }

    public function create()
    {
        return view('backend.layouts.charity.programme.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'country' => 'nullable|string|max:255',
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $programme = new Programme();
        $programme->country = $request->input('country');
        $programme->type = $request->input('type');
        $programme->title = $request->input('title');
        $programme->description = $request->input('description');

        if ($request->hasFile('image')) {
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'programme', $randomString);
            $programme->image = $imagePath;
        }

        if ($request->hasFile('thumbnail')) {
            $randomString = (string) Str::uuid();
            $thumbnailPath = Helper::fileUpload($request->file('thumbnail'), 'programme', $randomString);
            $programme->thumbnail = $thumbnailPath;
        }

        $programme->save();
        return redirect()->route('programmes.index')->with('success', 'Programme created successfully.');
    }

    public function edit($id)
    {
        $data = Programme::findOrFail($id);
        return view('backend.layouts.charity.programme.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $programme = Programme::findOrFail($id);
        $request->validate([
            'country' => 'nullable|string|max:255',
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $programme->country = $request->input('country');
        $programme->type = $request->input('type');
        $programme->title = $request->input('title');
        $programme->description = $request->input('description');

        if ($request->hasFile('image')) {
             if ($programme->image && file_exists(public_path($programme->image))) {
                unlink(public_path($programme->image));
            }
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'programme', $randomString);
            $programme->image = $imagePath;
        }

        if ($request->hasFile('thumbnail')) {
            if ($programme->thumbnail && file_exists(public_path($programme->thumbnail))) {
                unlink(public_path($programme->thumbnail));
            }
            $randomString = (string) Str::uuid();
            $thumbnailPath = Helper::fileUpload($request->file('thumbnail'), 'programme', $randomString);
            $programme->thumbnail = $thumbnailPath;
        }

        $programme->save();
        return redirect()->route('programmes.index')->with('success', 'Programme updated successfully.');
    }

    public function updateStatus(Request $request)
    {
        // Find the brand
        $data = Programme::findOrFail($request->id);

        // Update the status
        $data->status = $request->status;
        $data->save();

        $message = $request->status == 'inactive' ? "{$data->title} is inactive" : "{$data->title} is active";
        $type = $request->status == 'inactive' ? 'info' : 'success';

        return response()->json(['message' => $message, 'type' => $type]);
    }

    public function destroy(string $id)
    {
        $programme = Programme::findOrFail($id);

        // Delete main programme image
        if ($programme->image && file_exists(public_path($programme->image))) {
            unlink(public_path($programme->image));
            unlink(public_path($programme->thumbnail));
        }

        $programme->delete();
        return response()->json(['message' => 'Programme deleted successfully.']);
    }

    public function digitalProgrammes(Request $request)
    {
        if ($request->ajax()) {
            $data = Programme::with('donations')->where('type', 'digital')->orderBy('id', 'desc')->get();

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

                ->addColumn('title', function ($row) {
                    return $row->title;
                })

                ->addColumn('donations', function ($row) {
                    $count = $row->donations()->count();
                    return '<span class="badge bg-'. ($count > 0 ? 'success' : 'danger') .'">'  . $count . '</span>';
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

                ->addColumn('details', function ($row) {
                    return '<div class="">
                                <a style="font-size:14px;" href="' . route('donation.list', $row->id) . '" class="item details"><i class="icon-eye"></i> View Donors</a>
                            </div>';
                })

                ->addColumn('action', function ($row) {
                    return '<div class="list-icon-function flex items-center gap-2 justify-end">
                                <a href="' . route('programmes.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>
                                <button class="item trash" data-id="' . $row->id . '"><i class="icon-trash-2"></i></button>
                            </div>';
                })

                ->rawColumns(['name', 'status', 'action', 'image', 'option', 'donations', 'details'])
                ->make(true);
        }

        return view('backend.layouts.charity.programme.digital');
    }

    public function donationList($id)
    {
        $programme = Programme::with('donations')->findOrFail($id);
        $donations = $programme->donations;

        return view('backend.layouts.charity.programme.donation_list', compact('programme', 'donations'));
    }

}
