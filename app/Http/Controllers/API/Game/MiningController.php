<?php

namespace App\Http\Controllers\API\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarTransaction;
use App\Models\UserMiningStat;
use App\Models\WeeklyUserStat;
use App\Models\MiningReward;
use App\Models\UserActivityLog;
use Carbon\Carbon;

class MiningController extends Controller
{
    const TOTAL_MINING_SECONDS = 5000 * 3600;

    /*
    |--------------------------------------------------------------------------
    | 1. Get Mining Status
    |--------------------------------------------------------------------------
    */

    public function status()
    {
        $user = auth('api')->user();

        $stat = UserMiningStat::firstOrCreate(
            ['user_id' => $user->id],
            [
                'fire_bar' => 3,
                'pressure_bar' => 3,
                'minerals_bar' => 0,
                'mining_seconds' => 0
            ]
        );

        $this->calculateMining($stat);

        return response()->json([
            'fire_bar' => $stat->fire_bar,
            'pressure_bar' => $stat->pressure_bar,
            'minerals_bar' => $stat->minerals_bar,
            'mining_hours' => floor($stat->mining_seconds / 3600),
            'progress_percent' => round(($stat->mining_seconds / self::TOTAL_MINING_SECONDS) * 100, 2)
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Login → Fill Fire Bar
    |--------------------------------------------------------------------------
    */

    public function loginFire()
    {
        $user = auth('api')->user();
        $stat = UserMiningStat::where('user_id', $user->id)->first();

        if (!$stat) {
            return response()->json(['message' => 'Mining not initialized'], 400);
        }

        $stat->fire_bar += 1;
        $stat->save();

        return response()->json(['message' => 'Fire bar increased']);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. General Activity → Fill Pressure
    |--------------------------------------------------------------------------
    */

    public function addPressure()
    {
        $user = auth('api')->user();
        $stat = UserMiningStat::where('user_id', $user->id)->first();

        $stat->pressure_bar += 1;
        $stat->save();

        return response()->json(['message' => 'Pressure increased']);
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Upload Item → Fill Minerals
    |--------------------------------------------------------------------------
    */

    public function addMinerals()
    {
        $user = auth('api')->user();
        $stat = UserMiningStat::where('user_id', $user->id)->first();

        $stat->minerals_bar += 1;
        $stat->save();

        return response()->json(['message' => 'Minerals increased']);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Fill Bar With Coins
    |--------------------------------------------------------------------------
    */

    public function fillBar(Request $request)
    {
        $request->validate([
            'bar' => 'required|in:fire,pressure,minerals,all'
        ]);

        $user = auth('api')->user();
        $stat = UserMiningStat::where('user_id', $user->id)->first();

        if ($request->bar === 'all') {
            if ($user->coins < 120) {
                return response()->json(['message' => 'Not enough coins'], 400);
            }

            $user->coins -= 120;
            $stat->fire_bar = 5;
            $stat->pressure_bar = 5;
            $stat->minerals_bar = 5;
        } else {

            if ($user->coins < 50) {
                return response()->json(['message' => 'Not enough coins'], 400);
            }

            $user->coins -= 50;
            $stat->{$request->bar . '_bar'} = 5;
        }

        $user->save();
        $stat->save();

        return response()->json(['message' => 'Bar filled successfully']);
    }

    /*
    |--------------------------------------------------------------------------
    | Mining Calculation Logic
    |--------------------------------------------------------------------------
    */

    private function calculateMining($stat)
    {
        if (!$stat->last_mining_calculated_at) {
            $stat->last_mining_calculated_at = now();
            $stat->save();
            return;
        }

        if (
            $stat->fire_bar < 1 ||
            $stat->pressure_bar < 1 ||
            $stat->minerals_bar < 1
        ) {
            return; // mining paused
        }

        $secondsPassed = Carbon::parse($stat->last_mining_calculated_at)->diffInSeconds(now());

        $stat->mining_seconds += $secondsPassed;

        if ($stat->mining_seconds >= self::TOTAL_MINING_SECONDS) {

            MiningReward::create([
                'user_id' => $stat->user_id,
                'carat' => 1,
                'mining_seconds_used' => self::TOTAL_MINING_SECONDS,
                'rewarded_at' => now()
            ]);

            $stat->mining_seconds = 0;
        }

        $stat->last_mining_calculated_at = now();
        $stat->save();
    }
}