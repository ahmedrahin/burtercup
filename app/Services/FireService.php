<?php

namespace App\Services;

use App\Models\UserMiningStat;
use App\Models\WeeklyUserStat;
use Carbon\Carbon;

class FireService
{
    const FIRE_MAX = 5;
    const FIRE_INCREMENT_MINUTES = 15;
    const HEARTBEAT_MINUTES = 5;

    public function giveWeeklyFire($user)
    {
        $weekStart = Carbon::now()->startOfWeek();

        $weekly = WeeklyUserStat::firstOrCreate(
            ['user_id' => $user->id, 'week_start' => $weekStart],
            ['login_minutes' => 0]
        );

        $weekly->login_minutes += self::HEARTBEAT_MINUTES;
        $weekly->save();

        // Get or create user mining stats
        $stat = UserMiningStat::firstOrCreate(
            ['user_id' => $user->id],
            [
                'fire_bar' => 3,
                'last_fire_given_at' => now()
            ]
        );

        //Check if weekly usage reached threshold
        if ($weekly->login_minutes >= self::FIRE_INCREMENT_MINUTES && !$weekly->is_fire_bar_increase) {
            if ($stat->fire_bar < 5) {
                $stat->fire_bar += 1;
            }

            $stat->last_fire_given_at = now();

            // Mark weekly Fire given
            $weekly->is_fire_bar_increase = true;

            // Reset login_minutes
            $weekly->login_minutes = 0;
        }

        $weekly->save();
        $stat->save();


        return $stat;
    }
}