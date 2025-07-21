<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Log;

class SchoolSettingsHelper
{
    /**
     * Retrieve school information from the API.
     *
     * @return array
     */
    public static function getSchoolInfo(): array
    {
        $appmarque = null;
        $appenddate = null;

        try {
            $response = Http::get(config('app.url') . '/api/v1/school-info');

            if ($response->successful()) {
                $data = $response->json();
                $appmarque = optional(head($response['data']))['appmarkuee'] ?? null;
                $appenddate = collect($data['data'])->first()['appenddate'] ?? null;
            }
        } catch (Exception $e) {
            Log::error('API Request failed: ' . $e->getMessage());
        }

        return [
            'appmarque' => $appmarque,
            'appenddate' => $appenddate,
        ];
    }
}
