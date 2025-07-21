<?php

namespace App\Http\Controllers\Admissions;

use Illuminate\View\View;

use App\Helpers\SchoolSettingsHelper;
use App\Http\Requests\AccountRegister;
use Illuminate\Routing\Controller as BaseController;

class HomeController extends BaseController
{

    public function index(): View
    {
        $schoolInfo = SchoolSettingsHelper::getSchoolInfo();

        return view('admissions.home', compact('schoolInfo'));
    }

    public function admreq(): View
    {
        return view('admissions.admreq');
    }

    public function startpart(): View
    {
        $schoolInfo = SchoolSettingsHelper::getSchoolInfo();
        $min_number = 2;
        $max_number = 20;

        $random_number1 = mt_rand($min_number, $max_number);
        $random_number2 = mt_rand($min_number, $max_number);
        return view('admissions.start', compact('schoolInfo', 'random_number1', 'random_number2'));
    }

    public function starting(): View
    {
        $schoolInfo = SchoolSettingsHelper::getSchoolInfo();

        return view('admissions.login', compact('schoolInfo'));
    }

    public function fpass(): View
    {
        return view('admissions.forgotpass');
    }

    public function faker(): View
    {
        return view('admissions.adminlogin');
    }

    public function store(AccountRegister $request) {
        
    }
}
