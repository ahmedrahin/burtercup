<?php

namespace App\Helpers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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

    public static function sendNotifyMobile($token, $data): void
    {
        Log::info("workign");
        try {
            // Debugging autoloader
            if (!class_exists(Factory::class)) {
                Log::error('Class "Kreait\\Firebase\\Factory" not found. Check if the Firebase SDK is installed.');
            }
            // $factory = (new Factory)->withServiceAccount(storage_path('app/private/masjid-suite-firebase-adminsdk-geodk-dd6693d7aa.json'));
            $factory = (new Factory)->withServiceAccount(public_path('google-services.json'));

            $messaging = $factory->createMessaging();
            $notification = FirebaseNotification::create($data['title'], Str::limit($data['message'], 100));

            $message = CloudMessage::withTarget('token', $token)->withNotification($notification);
            $messaging->send($message);

            Log::info('Firebase notification sent successfully');

        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

}
