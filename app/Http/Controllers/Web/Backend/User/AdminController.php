<?php

namespace App\Http\Controllers\Web\Backend\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::orderByDesc('last_login_at')->where('role', 'admin')->select(['id', 'name', 'email', 'avatar', 'status', 'last_login_at']);

            return DataTables::of($users)
                ->addColumn('avatar', function ($row) {
                    $avatar = $row->avatar ?? 'user.png';
                    return '<div style="display: flex;align-items: center;gap: 11px;" >
                                <div class="image">
                                    <a href="' . route('admin.show', $row->id) . '">
                                        <img src="' . asset($avatar) . '" alt="" style="width: 40px; height: 40px; border-radius: 50%;">
                                    </a>
                                </div>
                                <div>
                                    <a href="' . route('admin.show', $row->id) . '" class="body-title-2">' . $row->name . '</a>
                                </div>
                            </div>';
                })
                ->addColumn('email', function ($row) {
                    return '<div class="body-text">' . $row->email . '</div>';
                })
                ->editColumn('last_login_at', function ($row) {
                    return '<div class="badge badge-light fw-bold">' . ($row->last_login_at ? Carbon::parse($row->last_login_at)->diffForHumans() : 'No login yet') . '</div>';
                })
                ->addColumn('status', function ($row) {
                    $status = $row->status == 'active' ? 'checked' : '';
                    $disabled = $row->id == 1 ? 'disabled' : '';
                    return '
                        <label class="custom-switch '. $disabled . '">
                            <input type="checkbox" class="status-switch" id="status-' . $row->id . '" ' . $status . ' ' . $disabled . ' data-id="' . $row->id . '">
                            <span class="slider"></span>
                        </label>
                    ';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="list-icon-function flex items-center gap-2 justify-end">
                                <div class="item eye"><i class="icon-eye"></i></div>
                                <a href=" '. route('admin.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>
                                <div class="item trash"><i class="icon-trash-2"></i></div>
                            </div>';
                })
                ->rawColumns(['avatar', 'email', 'action', 'status', 'last_login_at'])
                ->make(true);
        }

        return view('backend.layouts.admin.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layouts.admin.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', Rules\Password::defaults()],
        ]);

        if ($request->hasFile('image')) {
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'admin', $randomString);
        }

        // Create Admin User
        $admin = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'admin',
            'avatar' => $imagePath ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Admin account created successfully.',
            'admin' => $admin
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = User::find($id);
        return view('backend.layouts.admin.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        if ($request->has('remove') && $request->remove == 1) {
            if ($user->avatar && File::exists(public_path($user->avatar))) {
                File::delete(public_path($user->avatar));
            }
            $user->avatar = null;
        }
        elseif($request->hasFile('image')) {
            if ($user->avatar && File::exists(public_path($user->avatar))) {
                File::delete(public_path($user->avatar));
            }

            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'admin', $randomString);

            if ($user->avatar && file_exists(public_path($user->avatar))) {
                @unlink(public_path($user->avatar));
            }

            $user->avatar = $imagePath;
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Admin account updated successfully.',
            'admin' => $user
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = Auth::user();

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
        return response()->json(['success' => true, 'message' => 'Password changed successfully.']);
    }


    public function updateStatus(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        $user = User::findOrFail($request->user_id);

        // Prevent disabling the super admin (user_id = 1)
        if ($request->user_id == 1) {
            return response()->json(['type' => 'warning', 'message' => 'Cannot inactive this user']);
        }

        // Prevent a user from disabling their own account
        if (auth()->id() == $user->id && $request->status == 'inactive') {
            $user->update(['status' => 'inactive']);

            // Log the user out
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            // Delete the user's session
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();

            return response()->json(['message' => 'Your account has been disabled and you have been logged out.', 'type' => 'info', 'logout' => true]);
        }

        // Update status
        $user->status = $request->status;
        $user->save();

        $message = $request->status == 'inactive' ? "{$user->name} is inactive" : "{$user->name} is active";
        $type = $request->status == 'inactive' ? 'info' : 'success';

        // Return a success response
        return response()->json(['message' => $message, 'type' => $type]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
