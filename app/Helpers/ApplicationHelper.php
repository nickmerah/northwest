<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ApplicationHelper
{
    public static function getApplicanData()
    {
        $userId = session('user')['id'];
        return Cache::get("dashboard:{$userId}");
    }

    public static function getApplicantPassport()
    {
        $data = self::getApplicanData();
        $profilePicture = $data->user['profilePicture'];
        $photoPath = storage_path('app/public/app_passport/' . $profilePicture);

        if (file_exists($photoPath)) {
            return  Storage::disk('public')->url('app/public/app_passport/' . $profilePicture);
        }


        return  asset('public/images/avatar.jpg');
    }
}
