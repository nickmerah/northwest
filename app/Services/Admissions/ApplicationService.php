<?php

namespace App\Services\Admissions;

use Illuminate\Support\Facades\Http;

class ApplicationService
{
    public function logout(): array
    {
        $response = Http::withToken(session('access_token'))->get(
            config('app.url') . '/api/v1/logout'
        );

        try {

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => $response['message'],
                    'data'    => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'unable to process payment.',
                'data'    => $response->json(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'data'    => [],
            ];
        }
    }
}
