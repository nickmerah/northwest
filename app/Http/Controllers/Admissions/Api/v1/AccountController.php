<?php

namespace App\Http\Controllers\Admissions\Api\v1;


use App\Helpers\ApiResponse;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\AccountLogin;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRegister;
use App\Http\Requests\AccountResetPassword;
use App\Http\Resources\AppRegisteredResource;
use Symfony\Component\HttpFoundation\Response;


class AccountController extends Controller
{
    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function register(AccountRegister $request): JsonResponse
    {
        try {
            $response = $this->accountService->accountRegister($request);

            if ($response && $response->wasRecentlyCreated) {
                return ApiResponse::success(
                    status: 'success',
                    message: 'Account has been registered successfully.',
                    data: new AppRegisteredResource($response),
                    statusCode: Response::HTTP_CREATED
                );
            }

            return ApiResponse::error(
                status: 'error',
                message: 'Account registration failed.',
                statusCode: Response::HTTP_BAD_REQUEST
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                status: 'error',
                message: $e->getMessage(),
                statusCode: Response::HTTP_BAD_REQUEST
            );
        }
    }

    public function login(AccountLogin $request): JsonResponse
    {
        try {
            $response = $this->accountService->accountLogin($request);

            if ($response && $response['success']) {
                return ApiResponse::success(
                    status: 'success',
                    message: 'Login successful.',
                    data: $response,
                    statusCode: Response::HTTP_OK
                );
            }

            return ApiResponse::error(
                status: 'error',
                message: 'Invalid credentials.',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                status: 'error',
                message: 'Invalid credentials.',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }
    }

    public function resetpassword(AccountResetPassword $request): JsonResponse
    {
        try {
            $response = $this->accountService->resetPassword($request);

            if ($response && $response['success']) {
                return ApiResponse::success(
                    status: 'success',
                    message: 'Password was reset successful.',
                    data: $response,
                    statusCode: Response::HTTP_OK
                );
            }

            return ApiResponse::error(
                status: 'error',
                message: 'Invalid credentials.',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                status: 'error',
                message: 'Invalid credentials.',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }
    }
}
