<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\UserAddress;

class UserAddressController extends Controller
{
    public function newAddress(Request $request){
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to update your profile.',
                'code' => 401,
            ], 401);
        }

        // Validate input fields
        $validator = Validator::make($request->all(), [
            'address_name' => 'required',
            'address'   => 'required|string|max:255',
            'city'      => 'nullable',
            'state'     => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

        $address = new UserAddress();
        $address->user_id    = $user->id;
        $address->address_name  = $request->address_name;
        $address->address    = $request->address;
        $address->city       = $request->city;
        $address->state      = $request->state;
        $address->postal_code      = $request->postal_code;
        $address->is_default = $request->is_default ? true : false;
        $address->country    = $request->country;
        $address->save();

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Address added successfully.',
            'data'    => $address,
        ], 200);
    }

    public function myAddresses(Request $request)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to update your profile.',
                'code' => 401,
            ], 401);
        }

        // Get default address
        $defaultAddress = $user->addresses()->where('is_default', 1)->first();

        // Get all other addresses except default
        $savedAddresses = $user->addresses()
            ->where('is_default', 0)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Address list fetched successfully.',
            'default_address' => $defaultAddress,
            'saved_addresses' => $savedAddresses,
        ], 200);
    }

    public function edit(string $id)
    {
        $user = auth('api')->user();
        $address = $user->addresses()->where('id', $id)->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found.',
                'code' => 404,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Address data fatch.',
            'data' => $address
        ], 200);

    }

    public function makeDefault(Request $request)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first.',
                'code' => 401,
            ], 401);
        }

        $addressId = $request->address_id;
        $address = UserAddress::where('id', $addressId)->where('user_id', $user->id)->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found.',
                'code' => 404,
            ], 404);
        }

        UserAddress::where('user_id', $user->id)->update(['is_default' => 0]);

        $address->is_default = 1;
        $address->save();

        return response()->json([
            'success' => true,
            'code' => 200,
                'data' => $address,
            'message' => 'Default address updated successfully.',
        ]);
    }

    public function updateAddress(Request $request)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first.',
                'code' => 401,
            ], 401);
        }

         // Validation
         $validator = Validator::make($request->all(), [
            'address_name' => 'required',
            'address'      => 'required|string|max:255',
            'city'         => 'nullable|string|max:100',
            'state'        => 'nullable|string|max:100',
            'is_default'   => 'nullable|boolean',
            'address_id'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }


        $address = $user->addresses()->where('id', $request->address_id)->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found.',
                'code' => 404,
            ], status: 404);
        }

        // Update address data
        $address->address_name  = $request->address_name;
        $address->address    = $request->address;
        $address->city       = $request->city;
        $address->state      = $request->state;
        $address->country    = $request->country;
        $address->postal_code = $request->postal_code;
        $address->save();

        return response()->json(data: [
            'success' => true,
            'code' => 200,
            'message' => 'Address updated successfully.',
            'data' => $address
        ], status: 200);
    }

    public function deleteAddress(Request $request, $id)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first.',
                'code' => 401,
            ], 401);
        }

        $address = $user->addresses()->where('id', $id)->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found.',
                'code' => 404,
            ], 404);
        }

        // If deleting the default address, make sure another address becomes default if available
        // if ($address->is_default) {
        //     $nextDefaultAddress = $user->addresses()->where('id', '!=', $id)->first();

        //     if ($nextDefaultAddress) {
        //         $nextDefaultAddress->is_default = 1;
        //         $nextDefaultAddress->save();
        //     }
        // }

        // Delete the address
        $address->delete();

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Address deleted successfully.',
        ], 200);
    }

}
