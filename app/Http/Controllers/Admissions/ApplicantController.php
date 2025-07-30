<?php

namespace App\Http\Controllers\Admissions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Services\Admissions\ApplicationService;
use Illuminate\Routing\Controller as BaseController;


class ApplicantController extends BaseController
{
    protected $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    public function logout()
    {
        if (session()->has('user')) {
            $userId = session('user')['id'];
            Cache::forget("dashboard:$userId");
        }

        Session::flush();
        Auth::logout();

        return redirect()->route('admissions.starting')->with('error', 'You have been logged out');
    }
}
