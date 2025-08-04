<?php

namespace App\Http\Controllers\Admissions\Api\v1;

use App\Http\Requests\Jamb;
use App\Http\Requests\Olevel;
use App\Services\ResultsService;
use App\Http\Requests\Certificate;
use App\Http\Controllers\Controller;
use App\Services\ApiResponseService;
use App\Http\Requests\SchoolAttended;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ResultsController extends Controller
{

    protected $resultsService;
    protected $apiResponse;

    public function __construct(ResultsService $resultsService, ApiResponseService $apiResponse)
    {
        $this->resultsService = $resultsService;
        $this->apiResponse = $apiResponse;
    }

    public function getOlevels(): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->resultsService->getOlevels(),
            'O`level Details retrieved successfully.',
            'Unable to retrieve applicant`s O`level Details.',
            Response::HTTP_OK,
            fn($response) => !empty($response)
        );
    }

    public function saveOlevels(Olevel $request): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->resultsService->saveOlevels($request),
            'O`level Details saved successfully.',
            'Unable to save applicant`s O`level Details.',
            Response::HTTP_OK,
            fn($response) => !empty($response)
        );
    }

    public function getJamb(): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->resultsService->getJamb(),
            'Jamb Details retrieved successfully.',
            'Unable to retrieve applicant`s Jamb Details.',
            Response::HTTP_OK,
            fn($response) => !empty($response)
        );
    }

    public function saveJamb(Jamb $request): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->resultsService->saveJamb($request),
            'Jamb Details saved successfully.',
            'Unable to save applicant`s Jamb Details.',
            Response::HTTP_OK,
            fn($response) => !empty($response)
        );
    }

    public function getSchoolAttended(): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->resultsService->getSchoolAttended(),
            'School Attended Details retrieved successfully.',
            'Unable to retrieve applicant`s School Attended Details.',
            Response::HTTP_OK,
            fn($response) => !empty($response)
        );
    }

    public function saveSchoolAttended(SchoolAttended $request): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->resultsService->saveSchoolAttended($request),
            'School Attended Details saved successfully.',
            'Unable to save applicant`s School Attended Details.',
            Response::HTTP_OK,
            fn($response) => !empty($response)
        );
    }

    public function getUploadedResults(): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->resultsService->getUploadedResults(),
            'Uploaded Results retrieved successfully.',
            'Unable to retrieve applicant`s Uploaded Results.',
            Response::HTTP_OK,
            fn($response) => !empty($response)
        );
    }

    public function uploadResult(Certificate $request): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->resultsService->saveUploadedResult($request),
            'Results uploaded successfully.',
            'Unable to upload applicant`s Results.',
            Response::HTTP_OK,
            fn($response) => !empty($response)
        );
    }

    public function removeResult(): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->resultsService->removeResult(),
            'Results removed successfully.',
            'Unable to remove applicant`s Results.',
            Response::HTTP_OK,
            fn($response) => !empty($response)
        );
    }
}
