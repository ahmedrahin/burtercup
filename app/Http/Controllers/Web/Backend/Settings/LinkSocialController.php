<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SocialLink;

class LinkSocialController extends Controller
{
    public function index()
    {
        return view('backend.layouts.settings.social-link');
    }

    public function store(Request $request)
    {
        $request->validate([
            'facebook_link' => 'nullable|url|max:255',
            'instagram_link' => 'nullable|url|max:255',
            'twitter_link' => 'nullable|url|max:255',
            'tiktok_link' => 'nullable|url|max:255',
            'linkedin_link' => 'nullable|url|max:255',
            'github_link' => 'nullable|url|max:255',
            'youtube_link' => 'nullable|url|max:255',
            'whatsapp' => 'nullable|numeric',
        ]);

        $data                   = SocialLink::firstOrNew();
        $data->facebook_link    = $request->facebook_link;
        $data->instagram_link   = $request->instagram_link;
        $data->twitter_link     = $request->twitter_link;
        $data->tiktok_link      = $request->tiktok_link;
        $data->linkedin_link    = $request->linkedin_link;
        $data->github_link      = $request->github_link;
        $data->youtube_link     = $request->youtube_link;
        $data->whatsapp     = $request->whatsapp;
        $data->save();

        return response()->json(['message' => 'Social Data Updated Successfully', 'success' => true]);
    }
}
