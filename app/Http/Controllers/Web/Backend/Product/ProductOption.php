<?php

namespace App\Http\Controllers\WEb\Backend\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\ProductGlasses;
use App\Models\ColorGlass;
use App\Models\ExtendePrescription;
use App\Models\ProductAccessories;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Helpers\Helper;


class ProductOption extends Controller
{
    public function typeGlass(Request $request){
        if ($request->ajax()) {
            $data = ProductGlasses::orderBy('id', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return '<div class="body-text">' . $row->name . '</div>';
                })
                ->addColumn('price', function ($row) {
                    return  '$'. $row->price;
                })

                ->editColumn('description', function ($row) {
                    return '<span style="font-size:13px;">' . $row->description . '</span>';
                })

                ->addColumn('action', function ($row) {
                    return '<div class="list-icon-function flex items-center gap-2 justify-end">
                                <a href=" '. route('options.type.glass.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>
                                <button class="item trash" data-id="' . $row->id . '"><i class="icon-trash-2"></i></button>
                            </div>';
                })
                ->rawColumns(['name', 'price', 'description', 'action'])
                ->make(true);
        }

        return view('backend.layouts.option.glass.type_of_glass');
    }

    public function typeGlassCreate(){
        return view('backend.layouts.option.glass.create');
    }

    public function typeGlassStore(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required'
        ]);

        ProductGlasses::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully.',
        ]);

    }

    public function typeGlassEdit($id){
        $data = ProductGlasses::find($id);
        return view('backend.layouts.option.glass.edit', compact('data'));
    }


    public function typeGlassUpdate(Request $request, $id){
        $data = ProductGlasses::find($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required'
        ]);

        $data->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully.',
        ]);
    }

    public function typeGlassDelete($id){
        $data = ProductGlasses::findOrFail($id);
        $data->delete();

        return response()->json(['message' => 'Deleted successfully.']);
    }

    public function colorGlass(Request $request){
        if ($request->ajax()) {
            $data = ColorGlass::orderBy('id', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return '<div class="body-text">' . $row->name . '</div>';
                })
                ->addColumn('price', function ($row) {
                    return  '$'. $row->price;
                })

                ->editColumn('description', function ($row) {
                    return '<span style="font-size:13px;">' . $row->description . '</span>';
                })

                ->addColumn('action', function ($row) {
                    return '<div class="list-icon-function flex items-center gap-2 justify-end">
                                <a href=" '. route('options.type.color.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>
                                <button class="item trash" data-id="' . $row->id . '"><i class="icon-trash-2"></i></button>
                            </div>';
                })
                ->rawColumns(['name', 'price', 'description', 'action'])
                ->make(true);
        }

        return view('backend.layouts.option.color.color_of_glass');
    }

    public function typecolorCreate(){
        return view('backend.layouts.option.color.create');
    }


    public function typecolorStore(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required'
        ]);

        ColorGlass::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'non_tended' => $request->non_tended,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully.',
        ]);
    }


    public function typecolorEdit($id){
        $data = ColorGlass::find($id);
        return view('backend.layouts.option.color.edit', compact('data'));
    }

    public function typecolorUpdate(Request $request, $id){
        $data = ColorGlass::find($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required'
        ]);

        $data->update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'non_tended' => $request->non_tended,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully.',
        ]);
    }

    public function typecolorDelete($id){
        $data = ColorGlass::findOrFail($id);
        $data->delete();

        return response()->json(['message' => 'Deleted successfully.']);
    }

    public function accessoriesList(Request $request){
        if ($request->ajax()) {
            $data = ProductAccessories::orderBy('id', 'desc')->get();

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
                    return '<div class="body-text">' . $row->name . '</div>';
                })
                ->addColumn('price', function ($row) {
                    return  '$'. $row->price;
                })

                ->editColumn('description', function ($row) {
                    return '<span style="font-size:13px;">' . $row->description . '</span>';
                })

                ->addColumn('action', function ($row) {
                    return '<div class="list-icon-function flex items-center gap-2 justify-end">
                                <a href=" '. route('options.accessories.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>
                                <button class="item trash" data-id="' . $row->id . '"><i class="icon-trash-2"></i></button>
                            </div>';
                })
                ->rawColumns(['name', 'price', 'description', 'action', 'image'])
                ->make(true);
        }

        return view('backend.layouts.option.accessories.accessories');
    }


    public function accessoriesCreate(){
        return view('backend.layouts.option.accessories.create');
    }

    public function accessoriesStore(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'accessories', $randomString);
        }

        ProductAccessories::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imagePath ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully.',
        ]);
    }

    public function accessoriesEdit($id){
        $data = ProductAccessories::find($id);
        return view('backend.layouts.option.accessories.edit', compact('data'));
    }

    public function accessoriesUpdate(Request $request, $id)
    {
        $edit = ProductAccessories::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
        $edit->price = $request->price;
        $edit->description = $request->description;

        // Save the changes
        $edit->save();

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully.',
        ]);
    }
    public function accessoriesDelete($id){
        $data = ProductAccessories::findOrFail($id);
        if ($data->image && File::exists(public_path($data->image))) {
            File::delete(public_path($data->image));
        }
        $data->delete();

        return response()->json(['message' => 'Brand deleted successfully.']);
    }


    public function extentedList(Request $request){
        if ($request->ajax()) {
            $data = ExtendePrescription::orderBy('id', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('name', function ($row) {
                    return '<div class="body-text">' . $row->name . '</div>';
                })
                ->addColumn('price', function ($row) {
                    return  '$'. $row->price;
                })

                ->addColumn('action', function ($row) {
                    return '<div class="list-icon-function flex items-center gap-2 justify-end">
                                <a href=" '. route('options.extented.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>
                                <button class="item trash" data-id="' . $row->id . '"><i class="icon-trash-2"></i></button>
                            </div>';
                })
                ->rawColumns(['name', 'price', 'action'])
                ->make(true);
        }

        return view('backend.layouts.option.extented.extented');
    }

    public function extentedCreate(){
        return view('backend.layouts.option.extented.create');
    }


    public function extentedStore(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        ExtendePrescription::create([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully.',
        ]);

    }

    public function extentedEdit($id){
        $data = ExtendePrescription::find($id);
        return view('backend.layouts.option.extented.edit', compact('data'));
    }


    public function extentedUpdate(Request $request, $id){
        $data = ExtendePrescription::find($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        $data->update([
            'name' => $request->name,
            'price' => $request->price
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully.',
        ]);
    }

    public function extentedDelete($id){
        $data = ExtendePrescription::findOrFail($id);
        $data->delete();

        return response()->json(['message' => 'Deleted successfully.']);
    }

}
