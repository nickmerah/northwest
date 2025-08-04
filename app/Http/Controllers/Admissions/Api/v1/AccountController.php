<?php

namespace App\Http\Controllers\Admissions\Api\v1;

use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\AccountLogin;
use App\Http\Controllers\Controller;
use App\Services\ApiResponseService;
use App\Http\Requests\AccountRegister;
use App\Http\Requests\AccountResetPassword;
use App\Http\Resources\AppRegisteredResource;
use Symfony\Component\HttpFoundation\Response;


class AccountController extends Controller
{
    protected $accountService;
    protected $apiResponse;

    public function __construct(AccountService $accountService, ApiResponseService $apiResponse)
    {
        $this->accountService = $accountService;
        $this->apiResponse = $apiResponse;
    }

    public function register(AccountRegister $request): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->accountService->accountRegister($request),
            'Account has been registered successfully.',
            'Account registration failed.',
            Response::HTTP_CREATED,
            fn($response) => $response && $response->wasRecentlyCreated,
            fn($response) => new AppRegisteredResource($response)
        );
    }

    public function login(AccountLogin $request): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->accountService->accountLogin($request),
            'Login successful.',
            'Invalid credentials.',
            Response::HTTP_OK,
            fn($response) => is_array($response) && !empty($response['success']) && array_key_exists('user', $response)
        );
    }

    public function resetpassword(AccountResetPassword $request): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->accountService->resetPassword($request),
            'Password was reset successfully.',
            'Invalid credentials.',
            Response::HTTP_OK,
            fn($response) => $response && ($response['success'] ?? false)
        );
    }

    public function logout(): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->accountService->accountLogout(),
            'Logged out successfully.',
            'Logout failed.',
            Response::HTTP_OK,
            fn($response) => $response['success'] ?? false
        );
    }
}
