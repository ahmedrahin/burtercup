<?php

namespace App\Http\Controllers\Web\Backend\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttributeValue;

class AttributeController extends Controller
{
    public function index(){
        $sizes = AttributeValue::whereNotNull('size_value')->get();
        $colors = AttributeValue::whereNotNull('color_value')->get();
        return view('backend.layouts.product.variant.attribute', compact('sizes', 'colors'));
    }

    public function store(Request $request){
        $request->validate([
            'value' => 'required',
        ]);

        $data = new AttributeValue();
        if( $request->size ){
            $data->size_value = $request->value;
        }

        if( $request->color ){
            $data->color_value = $request->value;
            $data->option = $request->color_code;
        }

        $data->save();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }

    public function destroy(string $id){
        $delete = AttributeValue::find($id);

        if($delete){
            $delete->delete();
            return redirect()->back();
            // return response()->json([
            //     'success' => true
            // ]);
        }
    }

}
