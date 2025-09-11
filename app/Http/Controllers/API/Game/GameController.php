<?php

namespace App\Http\Controllers\API\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GameCategory;

class GameController extends Controller
{
   public function GameCategory()
    {
        $user = auth('api')->user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to access your profile.',
                'code' => 401,
            ], 401);
        }

        // Get active categories
        $categories = GameCategory::where('status', 'active')->get();

        $categories = $categories->map(function ($category) use ($user) {
            $isUnlocked = false;

            switch ($user->subscription) {
                case 'free':
                    $isUnlocked = $category->free == 1;
                    break;

                case 'premium':
                    $isUnlocked = $category->premium == 1;
                    break;

                case 'platinum':
                    $isUnlocked = $category->platinum == 1;
                    break;
            }

            // Append "locked/unlocked" attribute
            $category->locked = $isUnlocked ? false : true;

            return $category;
        });

        return response()->json([
            'success' => true,
            'message' => 'Versus categories',
            'status' => 200,
            'categories' => $categories
        ]);
    }

}
