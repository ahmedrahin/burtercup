<?php

namespace App\Http\Controllers\Web\Backend\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use Yajra\DataTables\DataTables;
use App\Models\DeliveryOption;

class DeliveryController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $data = DeliveryOption::orderBy('id', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('name', function ($row) {
                    return '<div class="body-text">' . $row->name . '</div>';
                })
                ->addColumn('day', function ($row) {
                    return '<div class="body-text">' . $row->day . '</div>';
                })
                ->addColumn('price', function ($row) {
                    return  '$'. $row->price;
                })

                ->addColumn('status', function ($row) {
                    $checked = $row->status == 'active' ? 'checked' : '';
                    return '
                        <label class="custom-switch">
                            <input type="checkbox" class="status-switch" id="status-' . $row->id . '" ' . $checked . ' data-id="' . $row->id . '">
                            <span class="slider"></span>
                        </label>';
                })

                ->addColumn('action', function ($row) {
                    return '<div class="list-icon-function flex items-center gap-2 justify-end">
                                <a href=" '. route('delivery-option.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>
                                <button class="item trash" data-id="' . $row->id . '"><i class="icon-trash-2"></i></button>
                            </div>';
                })
                ->rawColumns(['name', 'price', 'description', 'action', 'image', 'day', 'status'])
                ->make(true);
        }

        return view('backend.layouts.delivery.list');
    }


    public function create(){
        return view('backend.layouts.delivery.create');
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'accessories', $randomString);
        }

        DeliveryOption::create([
            'name' => $request->name,
            'day' => $request->day,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imagePath ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully.',
        ]);
    }

    public function edit($id){
        $data = DeliveryOption::find($id);
        return view('backend.layouts.delivery.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $edit = DeliveryOption::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            
        ]);

        // Image Removal
        if ($request->has('remove') && $request->remove == 1) {
            if ($edit->image && File::exists(public_path($edit->image))) {
                File::delete(public_path($edit->image));
            }
            $edit->image = null;
        }

        // Image Upload
        if ($request->hasFile('image')) {
            if ($edit->image && File::exists(public_path($edit->image))) {
                File::delete(public_path($edit->image));
            }

            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'accessories', $randomString);
            $edit->image = $imagePath;
        }

        // Update Model Attributes
        $edit->name = $request->name;
        $edit->day = $request->day;
        $edit->price = $request->price;
        $edit->description = $request->description;

        // Save the changes
        $edit->save();

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully.',
        ]);
    }
    public function destroy($id){
        $data = DeliveryOption::findOrFail($id);
        if ($data->image && File::exists(public_path($data->image))) {
            File::delete(public_path($data->image));
        }
        $data->delete();

        return response()->json(['message' => 'Deleted successfully.']);
    }

    public function updateStatus(Request $request)
    {


        // Find the brand
        $data = DeliveryOption::findOrFail($request->id);

        // Update the status
        $data->status = $request->status;
        $data->save();

        $message = $request->status == 'inactive' ? "{$data->name} is inactive" : "{$data->name} is active";
        $type = $request->status == 'inactive' ? 'info' : 'success';

        return response()->json(['message' => $message, 'type' => $type]);
    }



}
