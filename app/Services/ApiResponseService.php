<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseService
{
    public function respond(
        callable $callback,
        string $successMessage,
        string $errorMessage,
        int $successStatus = Response::HTTP_OK,
        ?callable $successCondition = null,
        ?callable $transform = null
    ) {
        try {
            $response = $callback();

            try {
                $isSuccess = $successCondition
                    ? $successCondition($response)
                    : ($response && (is_array($response) ? ($response['success'] ?? true) : true));
            } catch (\Throwable $e) {
                return ApiResponse::error(
                    status: 'error',
                    message: $e->getMessage(),
                    statusCode: Response::HTTP_UNAUTHORIZED
                );
            }

            if ($isSuccess) {
                try {
                    $data = $transform ? $transform($response) : $response;
                } catch (\Throwable $e) {
                    return ApiResponse::error(
                        status: 'error',
                        message: $e->getMessage(),
                        statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
                    );
                }

                return ApiResponse::success(
                    status: 'success',
                    message: $successMessage,
                    data: $data,
                    statusCode: $successStatus
                );
            }

            return ApiResponse::error(
                status: 'error',
                message: $errorMessage,
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                status: 'error',
                message: $e->getMessage(),
                statusCode: Response::HTTP_BAD_REQUEST
            );
        }
    }
}
