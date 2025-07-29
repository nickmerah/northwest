<?php

namespace App\Http\Controllers\Admissions;

use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller as BaseController;


class DashBoardController extends BaseController
{
    public function index(): View
    {
        $response = Http::withToken(session('access_token'))->get(config('app.url') . '/api/v1/dashboard?include=firstChoiceCourse,programme');
        $data = (object) $response->json()['data'];

        return view('admissions.applicants.dashboard', compact('data'));
    }
}
