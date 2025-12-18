<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Helpers\Helper;
use Carbon\Carbon;

class LeaderboardController extends Controller
{
    public function getLeaderboard()
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 401);
        }

        $startDate = Carbon::now()->subWeeks(2);

        // top buyers
        $topBuyer = Order::with(['user', 'orderItems'])
            ->whereNotNull('user_id')
            ->where('created_at', '>=', $startDate)
            ->get()
            ->groupBy('user_id')
            ->map(function ($orders) use ($user) {

                $totalCoins = $orders->sum(function ($order) {
                    return $order->orderItems->sum(function ($item) {
                        return $item->price * $item->quantity;
                    });
                });

                $buyer = $orders->first()->user;

                return [
                    'user_id' => $buyer->id,
                    'name' => $buyer->id == $user->id ? 'you' : $buyer->name,
                    'avatar' => $buyer->avatar ?? null,
                    'total_coins' => Helper::formatCurrencyShort($totalCoins) . ' pts',
                ];
            })
            ->sortByDesc('total_coins')
            ->take(10)
            ->values();

        // top sellers
        $topSeller = OrderItem::with(['product.user', 'order'])
            ->whereHas('order', function ($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate);
            })
            ->get()
            ->groupBy(fn($item) => $item->product->user_id)
            ->map(function ($items) use ($user) {

                $totalCoins = $items->sum(fn($item) => $item->price * $item->quantity);
                $seller = $items->first()->product->user;

                return [
                    'seller_id' => $seller->id,
                    'seller_name' => $seller->id == $user->id ? 'you' : $seller->name,
                    'avatar' => $seller->avatar ?? null,
                    'total_coins' => Helper::formatCurrencyShort($totalCoins) . ' pts',
                ];
            })
            ->sortByDesc('total_coins')
            ->take(10)
            ->values();

        return response()->json([
            'top_buyer' => $topBuyer,
            'top_seller' => $topSeller,
        ]);
    }
}
