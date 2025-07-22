<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\JsonResource;

class ApiResponse
{

    public function __construct()
    {
        // Initialization if needed
    }


    /**
     * Generate a success response.
     *
     * @param string $status
     * @param string|null $message
     * @param array $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($status = 'success', $message = null, $data = [], $statusCode = Response::HTTP_OK)
    {

        switch (true) {
            case $data instanceof JsonResource:
                $data = $data->resolve();
                break;

            case $data instanceof Collection:
                $data = $data->toArray();
                break;

            case is_array($data):
                break;

            case $data instanceof Model:
                $data = [$data];
                break;

            default:
                $data = [];
                break;
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Generate a success response.
     *
     * @param string $status
     * @param string|null $message
     * @param array $data
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($status = 'error', $message = null, $statusCode = Response::HTTP_BAD_REQUEST)
    {
        return response()->json([
            'status' => $status,
            'message' => $message
        ], $statusCode);
    }
}
