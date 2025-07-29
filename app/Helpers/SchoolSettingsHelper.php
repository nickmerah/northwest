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

    public static function olevelGrades(): array
    {
        return [
            'A1',
            'AR',
            'B2',
            'B3',
            'C4',
            'C5',
            'C6',
            'D7',
            'D8',
            'F9',
            'ABS',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'E8',
            'P',
        ];
    }
}
