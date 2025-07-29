<?php

namespace App\Http\Controllers\Admissions\Api\v1;

use Illuminate\Http\Request;
use App\Services\ApplicantService;
use App\Http\Controllers\Controller;
use App\Services\ApiResponseService;

class DashBoardController extends Controller
{
    protected $applicantService;
    protected $apiResponse;

    public function __construct(ApplicantService $applicantService, ApiResponseService $apiResponse)
    {
        $this->applicantService = $applicantService;
        $this->apiResponse = $apiResponse;
    }

    public function index(Request $request)
    {
        return $this->apiResponse->respond(
            fn() => $this->applicantService->getDashBoardData($request),
            'Dashboard Data successfully retrieved.',
            'Error retrieving Dashboard Data.'
        );
    }

    public function declaration()
    {
        return $this->apiResponse->respond(
            fn() => $this->applicantService->getDeclaration(),
            'Declaration successfully retrieved.',
            'Error retrieving Declaration.'
        );
    }

    public function saveDeclaration()
    {
        return $this->apiResponse->respond(
            fn() => $this->applicantService->saveDeclaration(),
            'Declaration saved successfully.',
            'Error saving Declaration.'
        );
    }
}
