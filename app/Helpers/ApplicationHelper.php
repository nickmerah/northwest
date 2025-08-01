<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

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
        // TODO, get passport if it exist

        $path =  asset('public/images/avatar.jpg');

        return  $path;
    }
}
