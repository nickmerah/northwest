<?php

namespace App\Http\Controllers\Admissions\Api\v1;

use App\Helpers\ApiResponse;
use App\Services\ApplicantService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DashBoardController extends Controller
{
    protected $applicantService;

    public function __construct(ApplicantService $applicantService)
    {
        $this->applicantService = $applicantService;
    }

    public function index(Request $request)
    {
        $response = $this->applicantService->getDashBoardData($request);

        try {

            if ($response && $response['success']) {
                return ApiResponse::success(
                    status: 'success',
                    message: 'Dashboard Data successful retrieved.',
                    data: $response,
                    statusCode: Response::HTTP_OK
                );
            }

            return ApiResponse::error(
                status: 'error',
                message: 'Error retrieving Dashboard Data.',
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
