<?php

namespace App\Http\Controllers\API\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GameCategory;
use App\Models\Game;
use App\Models\GameOption;
use App\Models\UserGame;

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

    public function game($id){
        $user = auth('api')->user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to access your profile.',
                'code' => 401,
            ], 401);
        }

        $category = GameCategory::where('status', 'active')->where('id', $id)->first();
        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'category not found',
                'code' => 401,
            ], 401);
        }

        $game = Game::with('options')->where('status', 'active')->where('game_category_id', $category->id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Games fatched',
            'status' => 200,
            'data' => $game
        ]);
    }

    public function game_options($id){
        $user = auth('api')->user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to access your profile.',
                'code' => 401,
            ], 401);
        }

        $game = Game::where('status', 'active')->where('id', $id)->first();
        if (!$game) {
            return response()->json([
                'status' => false,
                'message' => 'game not found',
                'code' => 401,
            ], 401);
        }

        $gameOptions = GameOption::where('game_id', $game->id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Game options fatched',
            'status' => 200,
            'data' => $gameOptions
        ]);
    }

    public function playGame(Request $request){
        $user = auth('api')->user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to access your profile.',
                'code' => 401,
            ], 401);
        }

        $data = UserGame::create([
            'user_id' => $user->id,
            'game_id' => $request->game_id,
            'option_name' => $request->option_name,
            'option_image' => $request->option_image,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'submit',
            'status' => 200,
            'data' => $data
        ]);

    }

    public function collectCoin(Request $request){
         $user = auth('api')->user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. Please log in first to access your profile.',
                'code' => 401,
            ], 401);
        }

        $data = $user->update([
            'coins' => $user->coins + 25,
            'active_coins' => $user->active_coins + 25,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'got coins',
            'status' => 200,
            'data' => $data
        ]);

    }

}
