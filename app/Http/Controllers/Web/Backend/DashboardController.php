<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
   public function index()
    {
        $query = User::where('role', 'user');

        // GENDER STATS
        $genderStats = $query->select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->get()
            ->mapWithKeys(fn($item) => [$item->gender => $item->total]);

        $maleCount = $genderStats['male'] ?? 0;
        $femaleCount = $genderStats['female'] ?? 0;
        $genderTotal = $maleCount + $femaleCount;

        $malePercent = $genderTotal > 0 ? round(($maleCount / $genderTotal) * 100, 0) : 0;
        $femalePercent = $genderTotal > 0 ? round(($femaleCount / $genderTotal) * 100, 0) : 0;

        // COUNTRY STATS (fixed)
        $countryStatsRaw = User::where('role', 'user')
            ->select('country', DB::raw('count(*) as total'))
            ->groupBy('country')
            ->orderByDesc('total')
            ->get();

        $topTotal = $countryStatsRaw->sum('total');

        $countryStats = $countryStatsRaw->map(function ($item) use ($topTotal) {
            return [
                'country' => $item->country ?? 'Unknown',
                'count' => $item->total,
                'percent' => $topTotal > 0 ? round(($item->total / $topTotal) * 100, 0) : 0,
            ];
        });

        // AGE RANGE STATS
        $ageRanges = [
            '10-20' => [10, 20],
            '21-30' => [21, 30],
            '31-40' => [31, 40],
            '41-50' => [41, 50],
            '51-60' => [51, 60],
            '60+'   => [61, 100]
        ];

        $totalUsers = User::where('role', 'user')->count();

        $ageStats = collect($ageRanges)->map(function ($range, $label) use ($totalUsers) {
            $count = User::where('role', 'user')
                ->whereBetween('age', $range)
                ->count();

            $percent = $totalUsers > 0 ? round(($count / $totalUsers) * 100, 0) : 0;

            return [
                'range' => $label,
                'count' => $count,
                'percent' => $percent,
            ];
        })->filter(fn($item) => $item['count'] > 0)->values();

        // CATEGORY STATS
        $allCategories = User::where('role', 'user')
            ->pluck('categories')
            ->filter()
            ->flatMap(function ($item) {
                return is_array($item) ? $item : json_decode($item, true);
            });

        $categoryCounts = $allCategories->countBy();
        $categoryTotal = $categoryCounts->sum();

        $categoryStats = $categoryCounts->map(function ($count, $category) use ($categoryTotal) {
            return [
                'category' => $category,
                'count' => $count,
                'percent' => $categoryTotal > 0 ? round(($count / $categoryTotal) * 100, 0) : 0,
            ];
        })->values();

        return view('backend.layouts.dashboard', compact(
            'maleCount',
            'femaleCount',
            'malePercent',
            'femalePercent',
            'countryStats',
            'ageStats',
            'categoryStats'
        ));
    }




}
