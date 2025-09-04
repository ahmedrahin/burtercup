<?php

namespace App\Http\Controllers\API\User;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{

    public function updateProfile(Request $request)
    {
        // Get the authenticated user
        $user = auth('api')->user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to update your profile.',
                'code' => 401,
            ], 401);
        }

        // Validate input fields
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

        // Update user profile details
        $user->name = $request->input('name');
        $user->last_name = $request->input('last_name');
        $user->phone = $request->input('phone');
        $user->email = $request->input('email');
        $user->address = $request->input('address');
        $user->save();

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Profile updated successfully.',
            'data' => [
                'name' => $user->name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
            ],
        ]);
    }

    public function updateEmail(Request $request){
         $user = auth('api')->user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to update your profile.',
                'code' => 401,
            ], 401);
        }

        // Validate input fields
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }


        $user->email = $request->input('email');
        $user->save();

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'E-mail updated successfully.',
            'data' => [
                'email' => $user->email,
            ],
        ]);

    }

    public function updatePhone(Request $request){
         $user = auth('api')->user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to update your profile.',
                'code' => 401,
            ], 401);
        }

        // Validate input fields
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }


        $user->phone = $request->input('phone');
        $user->save();

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => 'Phone Number updated successfully.',
            'data' => [
                'phone' => $user->phone,
            ],
        ]);

    }

    public function uploadAvatar(Request $request)
    {
        $user = auth()->user();

        // Validate the avatar file
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->first(),
                'code' => 422,
            ],422);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {

            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }

            $avatarName = $user->name;
            $avatar = Helper::fileUpload(
                $request->file('avatar'),
                'user',
                $avatarName
            );

            $user->avatar = $avatar;
            $user->save();

            // Directly return the full URL for avatar
            return response()->json([
                'status' => true,
                'message' => 'Avatar uploaded successfully.',
                'avatar_url' => $avatar ? asset($avatar) : null,
                'code' => 200,
            ]);
        }

        return response()->json([
            'success' => false,
            'status' => 400,
            'message' => 'No avatar file uploaded.',
        ]);
    }


    public function getProfile()
    {
        $user = auth('api')->user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to access your profile.',
                'code' => 401,
            ], 401);
        }

        // Structure the user data
        $user_data = [
            'id' => $user->id,
            'name' => $user->name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'description' => $user->description,
            'address' => $user->address,
            'email_verified_at' => $user->email_verified_at,
            'avatar' => $user->avatar ? asset($user->avatar) : null,
        ];

        return response()->json([
            'success' => true,
            'status' => 200,
            'userdata' => $user_data,
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'errors' => [
                    'current_password' => ['Current password is incorrect.']
                ]
            ], 422);
        }

        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'errors' => [
                    'new_password' => ['New password must be different from the current password.']
                ]
            ], 422);
        }

        $user->update([
            'password' => bcrypt($request->new_password),
        ]);
        return response()->json(['success' => true, 'message' => 'Password updated successfully.']);
    }

    public function deleteAccount(){
        $user = auth('api')->user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to access your profile.',
                'code' => 401,
            ], 401);
        }

        $user->delete();
         JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'success' => true,
            'message' => 'Your account has been deleted!',
            'status' => 200,
        ]);
    }

    public function inactiveAccount(){
        $user = auth('api')->user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to access your profile.',
                'code' => 401,
            ], 401);
        }

        $user->update(['status' => 'inactive']);
         JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'success' => true,
            'message' => 'Your account has been inactive now!',
            'status' => 200,
        ]);
    }

}
