<?php

namespace App\Http\Controllers\Admissions\Api\v1;

use App\Helpers\ApiResponse;
use App\Services\BiodataService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{

    protected $biodataService;

    public function __construct(BiodataService $biodataService)
    {
        $this->biodataService = $biodataService;
    }

    public function getProfile(): JsonResponse
    {
        try {
            $response = $this->biodataService->getBiodata();

            if ($response) {
                return ApiResponse::success(
                    status: 'success',
                    message: 'Applicant profile retrieved successfully',
                    data: $response,
                    statusCode: Response::HTTP_OK
                );
            }

            return ApiResponse::error(
                status: 'error',
                message: 'Unable to retrieve applicant`s profile.',
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

    public function saveProfile(Profile $request): JsonResponse
    {
        try {
            $response = $this->biodataService->saveBiodata($request);

            if ($response) {
                return ApiResponse::success(
                    status: 'success',
                    message: 'Applicant profile saved successfully',
                    data: $response,
                    statusCode: Response::HTTP_OK
                );
            }

            return ApiResponse::error(
                status: 'error',
                message: 'Unable to saved applicant`s profile.',
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
