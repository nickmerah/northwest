<?php

namespace App\Http\Controllers\Admissions\Api\v1;

use App\Http\Requests\Jamb;
use App\Helpers\ApiResponse;
use App\Http\Requests\Olevel;
use App\Services\ResultsService;
use App\Http\Requests\Certificate;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolAttended;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ResultsController extends Controller
{

    protected $resultsService;

    public function __construct(ResultsService $resultsService)
    {
        $this->resultsService = $resultsService;
    }

    public function getOlevels(): JsonResponse
    {
        try {
            $response = $this->resultsService->getOlevels();

            if ($response) {
                return ApiResponse::success(
                    status: 'success',
                    message: 'O`level Details retrieved successfully',
                    data: $response,
                    statusCode: Response::HTTP_OK
                );
            }

            return ApiResponse::error(
                status: 'error',
                message: 'Unable to retrieve applicant`s O`level Details.',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                status: 'error',
                message: $e->getMessage(),
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }
    }

    public function saveOlevels(Olevel $request): JsonResponse
    {
        try {
            $response = $this->resultsService->saveOlevels($request);

            if ($response) {
                return ApiResponse::success(
                    status: 'success',
                    message: 'O`level Details saved successfully',
                    data: $response,
                    statusCode: Response::HTTP_OK
                );
            }

            return ApiResponse::error(
                status: 'error',
                message: 'Unable to saved applicant`s O`level Details.',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                status: 'error',
                message: $e->getMessage(),
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }
    }

    public function getJamb(): JsonResponse
    {
        try {
            $response = $this->resultsService->getJamb();

            if ($response) {
                return ApiResponse::success(
                    status: 'success',
                    message: 'Jamb Details retrieved successfully',
                    data: $response,
                    statusCode: Response::HTTP_OK
                );
            }

            return ApiResponse::error(
                status: 'error',
                message: 'Unable to retrieve applicant`s Jamb Details.',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                status: 'error',
                message: $e->getMessage(),
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }
    }

    public function saveJamb(Jamb $request): JsonResponse
    {
        try {
            $response = $this->resultsService->saveJamb($request);

            if ($response) {
                return ApiResponse::success(
                    status: 'success',
                    message: 'Jamb Details saved successfully',
                    data: $response,
                    statusCode: Response::HTTP_OK
                );
            }

            return ApiResponse::error(
                status: 'error',
                message: 'Unable to saved applicant`s Jamb Details.',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                status: 'error',
                message: $e->getMessage(),
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }
    }

    public function getSchoolAttended(): JsonResponse
    {
        try {
            $response = $this->resultsService->getSchoolAttended();

            if ($response) {
                return ApiResponse::success(
                    status: 'success',
                    message: 'School Attended Details retrieved successfully',
                    data: $response,
                    statusCode: Response::HTTP_OK
                );
            }

            return ApiResponse::error(
                status: 'error',
                message: 'Unable to retrieve applicant`s School Attended Details.',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                status: 'error',
                message: $e->getMessage(),
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }
    }

    public function saveSchoolAttended(SchoolAttended $request): JsonResponse
    {
        try {
            $response = $this->resultsService->saveSchoolAttended($request);

            if ($response) {
                return ApiResponse::success(
                    status: 'success',
                    message: 'School Attended Details saved successfully',
                    data: $response,
                    statusCode: Response::HTTP_OK
                );
            }

            return ApiResponse::error(
                status: 'error',
                message: 'Unable to saved applicant`s School Attended Details.',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                status: 'error',
                message: $e->getMessage(),
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }
    }

    public function getUploadedResults(): JsonResponse
    {
        try {
            $response = $this->resultsService->getUploadedResults();

            if ($response) {
                return ApiResponse::success(
                    status: 'success',
                    message: 'Uploaded Results retrieved successfully',
                    data: $response,
                    statusCode: Response::HTTP_OK
                );
            }

            return ApiResponse::error(
                status: 'error',
                message: 'Unable to retrieve applicant`s Uploaded Results.',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                status: 'error',
                message: $e->getMessage(),
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }
    }

    public function uploadResult(Certificate $request): JsonResponse
    {
        try {
            $response = $this->resultsService->saveUploadedResult($request);

            if ($response) {
                return ApiResponse::success(
                    status: 'success',
                    message: 'Results uploaded successfully',
                    data: $response,
                    statusCode: Response::HTTP_OK
                );
            }

            return ApiResponse::error(
                status: 'error',
                message: 'Unable to upload applicant`s Results.',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                status: 'error',
                message: $e->getMessage(),
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }
    }

    public function removeResult(): JsonResponse
    {
        try {
            $response = $this->resultsService->removeResult();

            if ($response) {
                return ApiResponse::success(
                    status: 'success',
                    message: 'Results removed successfully',
                    data: $response,
                    statusCode: Response::HTTP_OK
                );
            }

            return ApiResponse::error(
                status: 'error',
                message: 'Unable to remove applicant`s Results.',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                status: 'error',
                message: $e->getMessage(),
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }
    }
}
