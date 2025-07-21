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
                $total = 1;
                break;

            case $data instanceof Collection:
                $total = $data->count();
                $data = $data->toArray();
                break;

            case is_array($data):
                $total = count($data);
                break;

            case $data instanceof Model:
                $total = 1;
                $data = [$data];
                break;

            default:
                $total = 0;
                $data = [];
                break;
        }
        $perPage = (int) request()->get('per_page', 100);
        $page = (int)request()->get('page', 1);
        $offset = ($page - 1) * $perPage;
        $paginatedData = array_slice($data, $offset, $perPage);

        return response()->json([
            'status' => $status,
            'message' => $message,
            'record_count' => count($paginatedData),
            'data' => $paginatedData,
            'pagination' => [
                'total' => $total,
                'per_page' => (int) $perPage,
                'current_page' => (int) $page,
                'last_page' => (int) ceil($total / $perPage),
            ],
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
