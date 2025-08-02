<?php

namespace App\Helpers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Helper {
    //! File or Image Upload
    public static function fileUpload($file, $folder, $name)
    {
        // Check if file is not null
        if ($file) {
            $imageName = Str::slug($name) . '._' . rand(1,9999) . '.' . $file->extension();
            $file->move(public_path('uploads/' . $folder), $imageName);
            $path = 'uploads/' . $folder . '/' . $imageName;
            return $path;
        }
        return null;
    }

}
