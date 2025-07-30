<?php

namespace App\Helpers;

use App\Models\Programmes;
use App\Models\ProgrammeType;
use Illuminate\Support\Carbon;
use App\Models\Admissions\AppLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Admissions\AppSession;
use Illuminate\Support\Facades\Cache;
use App\Models\Admissions\AppTransaction;
use App\Interfaces\AccountRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

class ApplicationHelper
{
    public static function getApplicantPassport()
    {
        $data = Cache::get("dashboard:{session('user')['id']}");
        $profilePicture = $data->user['profilePicture'];
        // TODO, get passport if it exist

        $path =  asset('public/images/avatar.jpg');

        return  $path;
    }
}
