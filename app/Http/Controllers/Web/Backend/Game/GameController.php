<?php

namespace App\Http\Controllers\Web\Backend\Game;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;
use Illuminate\Support\Str;
use App\Helpers\Helper;
use App\Models\GameCategory;
use App\Models\Product;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class GameController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Game::orderBy('id', 'desc')->get();

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('image', function ($row) {
                    $imageUrl = $row->image ? asset($row->image) : asset('blank-image.svg');
                    return '<div class="popup-gallery">
                                <a href="' . $imageUrl . '" class="popup-image">
                                    <img src="' . $imageUrl . '" width="50" style="border-radius:5px;">
                                </a>
                            </div>';
                })

                ->addColumn('name', function ($row) {
                    return $row->name;
                })

                ->addColumn('category', function ($row) {
                    return $row->category->name ?? '';
                })
                ->addColumn('option', function ($row) {
                    return '<div class="badge bg-info">' . $row->options->count() . '</div>';
                })

                ->addColumn('status', function ($row) {
                    $status = $row->status == 'active' ? 'checked' : '';
                    return '
                        <label class="custom-switch">
                            <input type="checkbox" class="status-switch" id="status-' . $row->id . '" ' . $status . ' data-id="' . $row->id . '">
                            <span class="slider"></span>
                        </label>
                    ';
                })

                ->addColumn('action', function ($row) {
                    return '<div class="list-icon-function flex items-center gap-2 justify-end">
                                <a href="' . route('game.edit', $row->id) . '" class="item edit"><i class="icon-edit-3"></i></a>
                                <button class="item trash" data-id="' . $row->id . '"><i class="icon-trash-2"></i></button>
                            </div>';
                })

                ->rawColumns(['name', 'status', 'action', 'image', 'option'])
                ->make(true);
        }

        return view('backend.layouts.game.game.list');
    }

    public function create()
    {
        $categories = GameCategory::all();
        return view('backend.layouts.game.game.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required',
            'short_title' => 'required',
            'image' => 'required|image',
            'name' => 'required|string|max:255'
        ]);

        if ($request->hasFile('image')) {
            $randomString = (string) Str::uuid();
            $imagePath = Helper::fileUpload($request->file('image'), 'game', $randomString);
        }

        $game = Game::create([
            'name' => $request->name,
            'image' => $imagePath,
            'short_title' => $request->short_title,
            'game_category_id' => $request->category,
        ]);

        if ($request->has('options')) {
            foreach ($request->options as $opt) {
                if (
                    empty($opt['option_a_name']) ||
                    empty($opt['option_b_name']) ||
                    empty($opt['option_a_image']) ||
                    empty($opt['option_b_image'])
                ) {
                    continue;
                }

                $optionAImagePath = Helper::fileUpload($opt['option_a_image'], 'game/game_options', (string) Str::uuid());
                $optionBImagePath = Helper::fileUpload($opt['option_b_image'], 'game/game_options', (string) Str::uuid());

                $game->options()->create([
                    'option_a_image' => $optionAImagePath,
                    'option_a_name' => $opt['option_a_name'],
                    'option_b_image' => $optionBImagePath,
                    'option_b_name' => $opt['option_b_name'],
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Game created successfully.',
        ]);

    }

    public function edit($id)
    {
        $categories = GameCategory::all();
        $data = Game::findOrFail($id);
        return view('backend.layouts.game.game.edit', compact('categories', 'data'));
    }

    public function update(Request $request, $id)
    {
        $game = Game::with('options')->findOrFail($id);

        // Validate main game fields
        $validated = $request->validate([
            'category' => 'required|exists:game_categories,id',
            'name' => 'required|string|max:255',
            'short_title' => 'required|string|max:255',
            'image' => 'nullable|image',

            'options.*.option_a_name' => 'nullable|string|max:255',
            'options.*.option_b_name' => 'nullable|string|max:255',
            'options.*.option_a_image' => 'nullable|image',
            'options.*.option_b_image' => 'nullable|image',
        ]);

        // Update main game image
        if ($request->hasFile('image')) {
            if ($game->image && file_exists(public_path($game->image))) {
                unlink(public_path($game->image));
            }
            $game->image = Helper::fileUpload($request->file('image'), 'game', (string) Str::uuid());
        }

        // Update main fields
        $game->name = $request->name;
        $game->short_title = $request->short_title;
        $game->game_category_id = $request->category;
        $game->save();

        // Handle options
        if ($request->has('options')) {

            // Collect all option IDs from request
            $submittedIds = collect($request->options)->pluck('id')->filter()->toArray();

            // Delete any existing options that are not in submitted IDs
            $game->options()->whereNotIn('id', $submittedIds)->each(function ($opt) {
                if ($opt->option_a_image && file_exists(public_path($opt->option_a_image))) {
                    unlink(public_path($opt->option_a_image));
                }
                if ($opt->option_b_image && file_exists(public_path($opt->option_b_image))) {
                    unlink(public_path($opt->option_b_image));
                }
                $opt->delete();
            });

            foreach ($request->options as $optData) {
                // Skip empty option blocks
                if (
                    empty($optData['option_a_name']) && empty($optData['option_b_name']) &&
                    empty($optData['option_a_image']) && empty($optData['option_b_image'])
                )
                    continue;

                if (isset($optData['id'])) {
                    // Existing option -> update
                    $existingOption = $game->options()->find($optData['id']);
                    if (!$existingOption)
                        continue;

                    // Update images if new file uploaded
                    if (isset($optData['option_a_image'])) {
                        if ($existingOption->option_a_image && file_exists(public_path($existingOption->option_a_image))) {
                            unlink(public_path($existingOption->option_a_image));
                        }
                        $existingOption->option_a_image = Helper::fileUpload($optData['option_a_image'], 'game/game_options', (string) \Str::uuid());
                    }
                    if (isset($optData['option_b_image'])) {
                        if ($existingOption->option_b_image && file_exists(public_path($existingOption->option_b_image))) {
                            unlink(public_path($existingOption->option_b_image));
                        }
                        $existingOption->option_b_image = Helper::fileUpload($optData['option_b_image'], 'game/game_options', (string) \Str::uuid());
                    }

                    // Update titles
                    $existingOption->option_a_name = $optData['option_a_name'] ?? $existingOption->option_a_name;
                    $existingOption->option_b_name = $optData['option_b_name'] ?? $existingOption->option_b_name;

                    $existingOption->save();

                } else {
                    // New option -> create
                    $optionAImagePath = isset($optData['option_a_image']) ? Helper::fileUpload($optData['option_a_image'], 'game/game_options', (string) \Str::uuid()) : null;
                    $optionBImagePath = isset($optData['option_b_image']) ? Helper::fileUpload($optData['option_b_image'], 'game/game_options', (string) \Str::uuid()) : null;

                    $game->options()->create([
                        'option_a_image' => $optionAImagePath,
                        'option_a_name' => $optData['option_a_name'] ?? null,
                        'option_b_image' => $optionBImagePath,
                        'option_b_name' => $optData['option_b_name'] ?? null,
                    ]);
                }
            }
        }


        return response()->json([
            'success' => true,
            'message' => 'Game updated successfully.',
            'data' => $game->load('options')
        ]);
    }

    public function updateStatus(Request $request)
    {
        // Find the brand
        $data = Game::findOrFail($request->id);

        // Update the status
        $data->status = $request->status;
        $data->save();

        $message = $request->status == 'inactive' ? "{$data->name} is inactive" : "{$data->name} is active";
        $type = $request->status == 'inactive' ? 'info' : 'success';

        return response()->json(['message' => $message, 'type' => $type]);
    }

    public function destroy(string $id)
    {
        $game = Game::with('options')->findOrFail($id);

        // Delete main game image
        if ($game->image && file_exists(public_path($game->image))) {
            unlink(public_path($game->image));
        }

        // Delete each option images
        foreach ($game->options as $opt) {
            if ($opt->option_a_image && file_exists(public_path($opt->option_a_image))) {
                unlink(public_path($opt->option_a_image));
            }

            if ($opt->option_b_image && file_exists(public_path($opt->option_b_image))) {
                unlink(public_path($opt->option_b_image));
            }
        }

        // Finally delete game (and options via cascade if set)
        $game->delete();

        return response()->json(['message' => 'Game deleted successfully.']);
    }

}
