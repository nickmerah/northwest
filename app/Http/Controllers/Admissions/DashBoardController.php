<?php

namespace App\Http\Controllers\Admissions;

use Illuminate\View\View;
use App\Services\Admissions\ApplicationService;
use Illuminate\Routing\Controller as BaseController;


class DashBoardController extends BaseController
{
    protected $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    public function index(): View
    {
        $data = $this->applicationService->refreshApplicantCache(session('user')['id'], session('access_token'));

        return view('admissions.applicants.dashboard', compact('data'));
    }
}
