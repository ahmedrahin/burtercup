<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\File;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Exception;

class SystemSettingController extends Controller
{
    public function index()
    {
        $setting = SystemSetting::latest('id')->first();
        return view('backend.layouts.settings.system_settings', compact('setting'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'email' => 'required|email',
            'copyright_text' => 'nullable|string',
            'logo' => 'nullable|mimes:jpeg,jpg,png,ico,svg',
            'dark_logo' => 'nullable|mimes:jpeg,jpg,png,ico,svg',
            'favicon' => 'nullable|mimes:jpeg,jpg,png,ico,svg',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }


        try {
            $setting = SystemSetting::firstOrNew();
            $setting->title = $request->title;
            $setting->email = $request->email;
            $setting->system_name = $request->system_name;
            $setting->copyright_text = $request->copyright_text;
            $setting->description = $request->description;

            if($request->title){
                $this->updateEnvFile('APP_NAME', $setting->title);
            }

           // Handle logo removal
           if ($request->has('remove_logo') && $request->remove_logo == 1) {
                if ($setting->logo && File::exists(public_path($setting->logo))) {
                    File::delete(public_path($setting->logo));
                }
                $setting->logo = null;
            } elseif ($request->hasFile('logo')) {
                if ($setting->logo && File::exists(public_path($setting->logo))) {
                    File::delete(public_path($setting->logo));
                }
                $randomString = (string) Str::uuid();
                $logo = Helper::fileUpload($request->file('logo'), 'settings', $randomString);
                $setting->logo = $logo;
            }

            // Handle favicon removal
            if ($request->has('remove_fav') && $request->remove_fav == 1) {
                if ($setting->favicon && File::exists(public_path($setting->favicon))) {
                    File::delete(public_path($setting->favicon));
                }
                $setting->favicon = null;
            } elseif ($request->hasFile('favicon')) {
                if ($setting->favicon && File::exists(public_path($setting->favicon))) {
                    File::delete(public_path($setting->favicon));
                }
                $randomString = (string) Str::uuid();
                $favicon = Helper::fileUpload($request->file('favicon'), 'settings', $randomString);
                $setting->favicon = $favicon;
            }

            if ($request->has('remove_dark') && $request->remove_dark == 1) {
                if ($setting->dark_logo && File::exists(public_path($setting->dark_logo))) {
                    File::delete(public_path($setting->dark_logo));
                }
                $setting->dark_logo = null;
            } elseif ($request->hasFile('dark_image')) {
                if ($setting->favicon && File::exists(public_path($setting->dark_image))) {
                    File::delete(public_path($setting->dark_image));
                }
                $randomString = (string) Str::uuid();
                $dark_image = Helper::fileUpload($request->file('dark_image'), 'settings', $randomString);
                $setting->dark_logo = $dark_image;
            }

            $setting->save();

            return response()->json(['message' => 'System Settings Updated Successfully', 'success' => true]);
        } catch (Exception $e) {
            return redirect()->back()->with('notify', ['type' => 'warning', 'message' => 'Something went wrong']);
        }
    }

    private function updateEnvFile($key, $value)
    {
        $envPath = base_path('.env');

        if (File::exists($envPath)) {
            $envContent = File::get($envPath);

            // Update existing key or add if not exists
            if (preg_match("/^{$key}=/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}=\"{$value}\"", $envContent);
            } else {
                $envContent .= "\n{$key}=\"{$value}\"";
            }

            File::put($envPath, $envContent);
        }
    }
}
