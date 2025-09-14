<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SliderBanner;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class SliderBannerController extends Controller
{
    public function index(){
        $images = SliderBanner::all();
        return view('backend.layouts.slider.index', compact('images'));
    }

    public function store(Request $request){
        $request->validate([
            'image' => 'image'
        ]);
        if ($request->hasFile('image')) {
            $randomString = (string) Str::uuid();
            $image = Helper::fileUpload($request->file('image'), 'slider', $randomString);

            // Save to database
            $sliderBanner = new SliderBanner();
            $sliderBanner->image = $image;
            $sliderBanner->save();
        }

    }

    public function destroy($id)
    {
        $sliderBanner = SliderBanner::find($id);

        if (!$sliderBanner) {
            return response()->json(['success' => false, 'message' => 'Image not found'], 404);
        }

        // Delete image file from storage
        $filePath = public_path($sliderBanner->image);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        // Delete image record from database
        $sliderBanner->delete();

        return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
    }

}
