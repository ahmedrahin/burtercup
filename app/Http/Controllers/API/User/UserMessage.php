<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Message;
use Exception;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class UserMessage extends Controller
{
    public function sendMessage(Request $request){
        $user = auth()->user();

        if(!$user){
            return response()->json([
                'success' => false,
                'message' => 'user not found'
            ], 500);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

        Message::create([
            'user_id' => $user->id,
            'message' => $request->message
        ]);

        return response()->json([
            'status' => true,
            'message' => 'your message has been sent'
        ], status: 200);
    }


    public function share(Request $request){
        $user = auth()->user();

        if(!$user){
            return response()->json([
                'success' => false,
                'message' => 'user not found'
            ], 500);
        }

        if($user->share){
            return response()->json([
                'success' => false,
                'message' => 'You have already got 50 coins'
            ], 500);
        }

        $user->update([
            'coins' => $user->coins + 50,
            'share' => 1
        ]);

        return response()->json([
            'status' => true,
            'message' => "you've got 50 more barter coins"
        ], status: 200);
    }

}
