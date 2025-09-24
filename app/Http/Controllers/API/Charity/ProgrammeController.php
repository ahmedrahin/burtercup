<?php

namespace App\Http\Controllers\API\Charity;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Programme;
use App\Models\UserDonation;
use App\Models\Volunteer;
use Illuminate\Support\Facades\Validator;

class ProgrammeController extends Controller
{
    public function programmeList(Request $request){
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to access your profile.',
                'code' => 401,
            ], 401);
        }

        $query = $request->input('query');

        if($query == 'all'){
            $programmes = Programme::where('status', 'active')->get(['title', 'image', 'country', 'id']);
        } elseif($query == 'local'){
            $programmes = Programme::whereNotNull('country')->where('country', $user->country)->where('status', 'active')->get(['title', 'image', 'country', 'id']);
        }elseif($query == 'global'){
            $programmes = Programme::whereNull('country')->where('status', 'active')->get(['title', 'image', 'country', 'id']);
        } else {
            $programmes = Programme::where('status', 'active')->get(['title', 'image', 'country', 'id']);
        }

        return response()->json([
            'success' => true,
            'message' => 'programmes retrieved successfully',
            'status' => 200,
            'data' => $programmes
        ]);
    }

    public function details($id){
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to access your profile.',
                'code' => 401,
            ], 401);
        }

        $programme = Programme::where('id', $id)->where('status', 'active')->first();

        if(!$programme){
            return response()->json([
                'success' => false,
                'message' => 'Programme not found',
                'status' => 404,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'programme details retrieved successfully',
            'status' => 200,
            'data' => $programme
        ]);
    }

    public function donate(Request $request, $id){
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to access your profile.',
                'code' => 401,
            ], 401);
        }

        $programme = Programme::where('id', $id)->where('status', 'active')->first();

        if(!$programme){
            return response()->json([
                'success' => false,
                'message' => 'Programme not found',
                'status' => 404,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all(),
                'status' => false,
            ], 400);
        }

        $userCoinBalance = $user->coins;

        if ($userCoinBalance < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient coin balance',
                'status' => 400,
            ], 400);
        }

        $donation = UserDonation::create([
            'user_id' => $user->id,
            'programme_id' => $programme->id,
            'amount' => $request->amount,
        ]);

        $user->coins -= $request->amount;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'We Thank you for your contiribution',
            'status' => 200,
            'data' => $donation
        ]);
    }

    public function voluenterRegister(Request $request, $id){
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to access your profile.',
                'code' => 401,
            ], 401);
        }

        $programme = Programme::where('id', $id)->where('status', 'active')->where('type', 'physical')->first();

        if(!$programme){
            return response()->json([
                'success' => false,
                'message' => 'Programme not found',
                'status' => 404,
            ], 404);
        }

        $alreadyExist = Volunteer::where('user_id', $user->id)->where('programme_id', $programme->id)->first();

         if($alreadyExist){
            return response()->json([
                'success' => false,
                'message' => 'You already register this programme',
                'status' => 404,
            ], 404);
        }

        $data = Volunteer::create([
            'user_id' => $user->id,
            'programme_id' => $programme->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thanks for register',
            'status' => 200,
            'data' => $data
        ]);
    }

}
