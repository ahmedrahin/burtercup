<?php

namespace App\Http\Controllers\Web\Backend\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductReview;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ProductReview::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('user', function ($row) {
                    if( !is_null($row->user_id) && isset($row->user) ){
                        $name = '<a href="" target="_blank" style="font-weight:600;">' . $row->user->name . '</a>';
                        $name .= '<div style="font-size:11px; color:#a7a7a7;">' . $row->user->email . '</div>';
                    }else {
                        $name = '<span style="font-weight:600;">' . $row->name . ' (guest)</span>';
                        $name .= '<div style="font-size:11px; color:#a7a7a7;">' . $row->email . '</div>';
                    }
                   return $name;
                })

                ->addColumn('product', function ($row) {
                    if( isset($row->product) ){
                        $name = '<a href=" ' . route('product.show',$row->product->id) . '" target="_blank" style="font-weight:600;">' . $row->product->name . '</a>';
                    }
                    return $name ?? '';
                 })
                ->addColumn('comment', function ($row) {
                    return '<span style="font-size:11px; color:#a7a7a7;">' . $row->comment . '</span>';
                 })
                 ->addColumn('rating', function ($row) {
                    $rating = floatval($row->rating);
                    $output = '';
                    for ($i = 1; $i <= floor($rating); $i++) {
                        $output .= '<i class="bi bi-star-fill text-warning me-1"></i>';
                    }
                    if ($rating - floor($rating) >= 0.5) {
                        $output .= '<i class="bi bi-star-half text-warning me-1"></i>';
                    }

                    // Empty stars
                    $remaining = 5 - ceil($rating);
                    for ($i = 0; $i < $remaining; $i++) {
                        $output .= '<i class="bi bi-star text-muted me-1"></i>';
                    }

                    return $output;
                })

                ->addColumn('date', function ($row) {
                    return Carbon::parse($row->created_at)->format('d M, Y');
                 })

                ->addColumn('action', function ($row) {
                    return '<div class="list-icon-function flex items-center gap-2 justify-end">
                                <button class="item trash" data-id="' . $row->id . '"><i class="icon-trash-2"></i></button>
                            </div>';
                })
                ->rawColumns([ 'user', 'product', 'comment', 'rating', 'selling', 'date', 'action'])
                ->make(true);
        }

        return view('backend.layouts.review.review');
    }

    public function destroy(string $id)
    {
        $data = ProductReview::findOrFail($id);
        $data->delete();

        return response()->json(['message' => 'Product Review deleted successfully.']);
    }


}
