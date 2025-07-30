<?php

namespace App\Http\Controllers\Admissions;

use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controller as BaseController;


class DashBoardController extends BaseController
{
    public function index(): View
    {
        $response = Http::withToken(session('access_token'))->get(config('app.url') . '/api/v1/dashboard?include=firstChoiceCourse,programme');
        $data = (object) $response->json()['data'];

        Cache::put("dashboard:{session('user')['id']}", $data, now()->addHour());

        return view('admissions.applicants.dashboard', compact('data'));
    }
}
