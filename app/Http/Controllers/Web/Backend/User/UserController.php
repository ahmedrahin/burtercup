<?php

namespace App\Http\Controllers\Web\Backend\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::query()
                ->orderByDesc('id')
                ->where('role', 'user')
                ->select(['id', 'name', 'email', 'coins', 'avatar', 'status', 'last_login_at', 'gender', 'country', 'age', 'categories','created_at']); // include categories

            // Apply filters if present
            if ($request->filled('gender')) {
                $users->where('gender', $request->gender);
            }

            if ($request->filled('country')) {
                $users->where('country', $request->country);
            }

            if ($request->filled('category')) {
                $category = $request->category;

                $users->where(function($query) use ($category) {
                    // Match category inside categories JSON array stored as JSON string or array
                    $query->whereJsonContains('categories', $category)
                        ->orWhere('categories', 'like', '%"'.$category.'"%');
                });
            }

            if ($request->filled('age')) {
                $users->where('age', $request->age);
            }

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('avatar', function ($row) {
                    $avatar = $row->avatar ?? 'user.png';
                    return '<div style="display: flex;align-items: center;gap: 11px;" >
                                <div>
                                    <a href="' . route('user.show', $row->id) . '" class="body-title-2">' . $row->name . '</a>
                                    <br>
                                    <span class="text-muted">' . $row->email . '</span>
                                </div>
                            </div>';
                })
                ->editColumn('coins', function ($row) {
                    $class = $row->coins > 0 ? 'bg-success' : 'bg-danger';
                    return '<div class="badge ' . $class . ' fw-bold">' . $row->coins . '</div>';
                })
                ->editColumn('gender', function ($row) {
                    return $row->gender ?? '-';
                })
                ->editColumn('age', function ($row) {
                    return $row->age ?? '-';
                })
                ->editColumn('country', function ($row) {
                    return ucfirst($row->country);
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d M, Y');
                })
                ->addColumn('status', function ($row) {
                    $status = $row->status == 'active' ? 'checked' : '';
                    $disabled = $row->id == 1 ? 'disabled' : '';
                    return '
                        <label class="custom-switch ' . $disabled . '">
                            <input type="checkbox" class="status-switch" id="status-' . $row->id . '" ' . $status . ' ' . $disabled . ' data-id="' . $row->id . '">
                            <span class="slider"></span>
                        </label>
                    ';
                })
                ->addColumn('details', function ($row) {
                    return '<div class="list-icon-function flex items-center gap-2 justify-end">
                                <a href="' . route('user.show', $row->id) . '" class="item edit" target="_blank"><i class="icon-eye"></i></a>
                            </div>';
                })
                ->rawColumns(['avatar', 'details', 'status', 'coins', 'last_login_at', 'country'])
                ->make(true);
        }

        return view('backend.layouts.user.list');
    }


    public function show($id){
        $data = User::find($id);
        return view('backend.layouts.user.details', compact('data'));
    }

    public function messages(Request $request){
        if ($request->ajax()) {
            $data = Message::latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return '<a class="body-text" href=" ' . route('user.show',$row->user_id) . ' " target="_blank">' . $row->user->name . '</a>';
                })
                ->addColumn('email', function ($row) {
                    return '<a class="body-text" href="mailto:'. $row->user->email .';">' . $row->user->email . '</a>';
                })
                ->addColumn('message', function ($row) {
                    return '<span style="font-size: 12px;">' . $row->message . '</span>';
                })
                ->addColumn('date', function ($row) {
                    return Carbon::parse($row->created_at)->format('d M, Y');
                })
                ->addColumn('time', function ($row) {
                    return $row->created_at->diffForHumans();
                })

                ->rawColumns(['name', 'email', 'message', 'date', 'time'])
                ->make(true);
        }

        return view('backend.layouts.user.messages');
    }


    public function updateStatus(Request $request)
    {
        // Find the brand
        $data = User::findOrFail($request->user_id);

        // Update the status
        $data->status = $request->status;
        $data->save();

        $message = $request->status == 'inactive' ? "{$data->name} is inactive" : "{$data->name} is active";
        $type = $request->status == 'inactive' ? 'info' : 'success';

        return response()->json(['message' => $message, 'type' => $type]);
    }
}
