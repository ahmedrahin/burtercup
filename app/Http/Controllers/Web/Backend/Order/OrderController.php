<?php

namespace App\Http\Controllers\Web\Backend\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderHistory;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Order::orderBy('id', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('user_id', function ($row) {
                    $avatar = $row->user->avatar ?? 'user.png';
                    return '<div style="display: flex;align-items: center;gap: 11px;" >
                                <div class="image">
                                    <a href="' . route('admin.show', $row->id) . '">
                                        <img src="' . asset($avatar) . '" alt="" style="width: 40px; height: 40px; border-radius: 50%;">
                                    </a>
                                </div>
                                <div>
                                    <a href="' . route('admin.show', $row->id) . '" class="body-title-2">' . $row->user->name . '</a>
                                </div>
                            </div>';
                })
                ->addColumn('order_id', function ($row) {
                    return '<div class="block-tracking m-auto">' . $row->order_id . '</div>';
                })
                ->addColumn('grand_total', function ($row) {
                    return '$' . number_format($row->grand_total, 2);
                })
                ->addColumn('quantity', function ($row) {
                    return $row->OrderItems->sum('quantity');
                })
                ->addColumn('order_date', function ($row) {
                    return Carbon::parse($row->order_date)->format('d M, Y');
                })
                ->addColumn('transaction_id', function ($row) {
                    return $row->transaction_id;
                })
                ->addColumn('delivery_status', function ($row) {
                    $status = '';
                    $class = '';

                    if ($row->delivery_status == 'confirmed') {
                        $status = $row->delivery_status;
                        $class = 'block-available';
                    } elseif ($row->delivery_status == 'pending') {
                        $status = $row->delivery_status;
                        $class = 'block-pending';
                    } elseif ($row->delivery_status == 'processing') {
                        $status = $row->delivery_status;
                        $class = 'badge bg-secondary';
                    } elseif ($row->delivery_status == 'ready to ship') {
                        $status = $row->delivery_status;
                        $class = 'block-warning';
                    } elseif ($row->delivery_status == 'delivered') {
                        $status = $row->delivery_status;
                        $class = 'badge bg-warning';
                    } elseif ($row->delivery_status == 'cancel') {
                        $status = $row->delivery_status;
                        $class = 'block-not-available';
                    }

                    return '<div class="' . $class . ' m-auto">' . $status . '</div>';
                })

                ->addColumn('viewed', function ($row) {
                    return '<i class="icon-eye ' . ($row->viewed ? "seen" : "") . '"></i>';
                })

                ->addColumn('details', function ($row) {
                    return '<a href=" ' . route('order.show', $row->id) . ' " class="btn btn-primary btn-details" target="_blank">Details</a>';
                })
                ->rawColumns(['user_id', 'order_id', 'delivery_status', 'viewed', 'details'])
                ->make(true);
        }

        return view('backend.layouts.order.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Order::with([
            'orderItems.product',
            'delivery'
        ])->findOrFail($id);
        return view('backend.layouts.order.details', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function OrderStatus(Request $request)
    {
        $order = Order::with('orderItems.product')->find($request->order_id);

        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $newStatus = $request->status;
        $oldStatus = $order->delivery_status;
        $restoreStatuses = ['canceled', 'fake'];

        // Moving INTO canceled/fake → restore stock
        if (in_array($newStatus, $restoreStatuses) && !in_array($oldStatus, $restoreStatuses)) {
            foreach ($order->orderItems as $item) {
                if ($item->product) {
                    $item->product->increment('quantity', $item->quantity);
                }
            }
        }

        // Moving OUT of canceled/fake → reduce stock again
        if (in_array($oldStatus, $restoreStatuses) && !in_array($newStatus, $restoreStatuses)) {
            foreach ($order->orderItems as $item) {
                if ($item->product && $item->product->quantity >= $item->quantity) {
                    $item->product->decrement('quantity', $item->quantity);
                }
            }
        }

        // Update order status & timestamp
        $order->update([
            'delivery_status' => $newStatus,
            'order_status_date' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        // Add order history note
        $statusNotes = [
            'pending' => 'Order is now pending.',
            'confirmed' => 'Order confirmed.',
            'canceled' => 'Order has been canceled.',
            'fake' => 'Order marked as fake.',
        ];

        $note = $statusNotes[$newStatus] ?? "Delivery status changed to {$newStatus}.";

        OrderHistory::create([
            'order_id' => $order->id,
            'status' => $newStatus,
            'note' => $note,
        ]);

        return response()->json(['message' => $note, 'success' => true]);
    }


    public function viewOrder(Request $request)
    {
        $order = Order::find($request->order_id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        $order->viewed = 1;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order viewed updated successfully.',
        ], 200);
    }
}
