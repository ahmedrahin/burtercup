<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Faq;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = Faq::orderByDesc('id')->get();

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('question', function ($row) {
                    return $row->question;
                })
                ->addColumn('type', function ($row) {
                    return $row->type;
                })
                ->addColumn('answer', function ($row) {
                    return '<span style="color: #a7a7a7;font-size: 11px;">' . $row->answer . '</span>';
                })

                ->addColumn('status', function ($row) {
                    $status = $row->status == 'active' ? 'checked' : '';

                    return '
                        <label class="custom-switch ">
                            <input type="checkbox" class="status-switch" id="status-' . $row->id . '" ' . $status . ' data-id="' . $row->id . '">
                            <span class="slider"></span>
                        </label>
                    ';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="list-icon-function flex items-center gap-2 justify-end">
                                <a href=" '. route('faq.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>
                                <button class="item trash" data-id="' . $row->id . '"><i class="icon-trash-2"></i></button>
                            </div>';
                })
                ->rawColumns(['status','action', 'answer'])
                ->make(true);
        }

        return view('backend.layouts.faq.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.layouts.faq.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'answer' => 'required',
            'question' => 'required',
            'type' => 'required',
        ]);

        // Create Admin User
        $data = Faq::create([
            'answer' => $request->answer,
            'type' => $request->type,
            'question' => $request->question,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'FAQ created successfully.',
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
        $data = Faq::find($id);
        return view('backend.layouts.faq.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Faq::findOrFail($id);

        $validated = $request->validate([
            'answer' => 'required',
            'question' => 'required',
            'type' => 'required',
        ]);

        $user->answer = $validated['answer'];
        $user->question = $validated['question'];
        $user->type = $validated['type'];
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'FAQ updated successfully.',
            'admin' => $user
        ]);
    }


    public function updateStatus(Request $request)
    {
        $user = Faq::findOrFail($request->user_id);

        $user->update(['status' => 'inactive']);

        // Update status
        $user->status = $request->status;
        $user->save();

        $message = $request->status == 'inactive' ? "FAQ is inactive" : "FAQ is active";
        $type = $request->status == 'inactive' ? 'info' : 'success';

        // Return a success response
        return response()->json(['message' => $message, 'type' => $type]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Faq::findOrFail($id);
        $data->delete();

        return response()->json(['message' => 'FAQ deleted successfully.']);
    }


    public function faqList() {
        $data = Faq::where('status', 1)->latest()->get()->groupBy('type');

        return response()->json([
            'success' => true,
            'message' => 'FAQ list',
            'data' => $data,
            'code' => 200,
        ]);
    }

   public function faqSearch(Request $request) {
        $search = $request->input('search');
        $type = $request->input('type');

        // Build the query
        $query = Faq::where('status', 1);

        // Filter by search keyword
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        // Filter by type if provided
        if ($type) {
            $query->where('type', $type);
        }

        // Get results and group by type
        $data = $query->latest()->get()
                    ->groupBy('type')
                    ->map(function($item) {
                        return $item->values();
                    });

        return response()->json([
            'success' => true,
            'message' => 'FAQ list',
            'data' => $data,
            'code' => 200,
        ]);
    }



}
