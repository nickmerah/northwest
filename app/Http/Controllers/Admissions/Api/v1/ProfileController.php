<?php

namespace App\Http\Controllers\Admissions\Api\v1;

use App\Helpers\ApiResponse;
use App\Http\Requests\Profile;
use App\Services\BiodataService;
use App\Http\Controllers\Controller;
use App\Services\ApiResponseService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProfileController extends Controller
{

    protected $biodataService;
    protected $apiResponse;

    public function __construct(BiodataService $biodataService, ApiResponseService $apiResponse)
    {
        $this->biodataService = $biodataService;
        $this->apiResponse = $apiResponse;
    }

    public function getProfile(): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->biodataService->getBiodata(),
            'Applicant profile retrieved successfully.',
            'Unable to retrieve applicant`s profile.',
            Response::HTTP_OK,
            fn($response) => !empty($response)
        );
    }

    public function saveProfile(Profile $request): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->biodataService->saveBiodata($request),
            'Applicant profile saved successfully.',
            'Unable to save applicant`s profile.',
            Response::HTTP_OK,
            fn($response) => !empty($response)
        );
    }
}
