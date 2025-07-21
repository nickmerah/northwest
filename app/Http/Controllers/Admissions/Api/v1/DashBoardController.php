<?php

namespace App\Http\Controllers\Admissions\Api\v1;

use App\Helpers\ApiResponse;
use App\Services\ApplicantService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DashBoardController extends Controller
{
    protected $applicantService;

    public function __construct(ApplicantService $applicantService)
    {
        $this->applicantService = $applicantService;
    }

    public function index()
    {

        try {
            $response = $this->applicantService->getDashBoardData();

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
                message: 'Error retrieving Dashboard Data.',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }
    }
}
