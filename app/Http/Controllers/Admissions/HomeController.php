<?php

namespace App\Http\Controllers\Admissions;

use Illuminate\View\View;
use App\Helpers\SchoolSettingsHelper;
use App\Services\Admissions\RegisterService;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Routing\Controller as BaseController;


class HomeController extends BaseController
{
    protected RegisterService $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

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

    public function store(Request $request)
    {
        $response = $this->registerService->register($request);

        if ($response['success']) {
            return view('admissions.success', [
                'message' => $response['data']['message'],
                'applicationNo' => $response['data']['data']['applicationNo'] ?? 'N/A',
            ]);
        }

        return redirect()->back()
            ->withInput()
            ->withErrors($response['data']['errors'] ?? [])
            ->with('error', $response['data']['message'] ?? $response['message'] ?? 'An error occurred during registration.');
    }

    public function success(): View
    {
        return view('admissions.success');
    }

    public function resetpass(Request $request)
    {
        $response = $this->registerService->resetPassword($request);

        if ($response['success']) {
            return view('admissions.forgotpass', [
                'message' => $response['data']['message'],
                'applicationNo' => $response['data']['data']['user'],
                'newPassword' => $response['data']['data']['passkey'],
                'registered' => true,
            ]);
        }

        return redirect()->back()
            ->withInput()
            ->withErrors($response['data']['errors'] ?? [])
            ->with('error', $response['data']['message'] ?? $response['message'] ?? 'An error occurred during registration.');
    }

    public function login(Request $request)
    {
        $response = $this->registerService->login($request);

        if ($response['success']) {
            $payload = $response['data']['data'];
            $token = $payload['token'];

            session([
                'access_token' => $token['access_token'],
                'user' => $payload['user'],
                'token_expires_at' => $token['expires_at'],
            ]);

            return redirect()->route('admissions.dashboard')->with('success', 'Login successful.');
        }

        return redirect()->back()
            ->withInput()
            ->withErrors(['regno' => $response['message'] ?? 'Login failed']);
    }
}
