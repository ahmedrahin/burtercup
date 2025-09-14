<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SliderBanner;

class HomePagesController extends Controller
{
    public function homeBanner(Request $request){
        $data = SliderBanner::latest()->get();
        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'fetch banner images',
            'data' => $data
        ]);
    }
}
