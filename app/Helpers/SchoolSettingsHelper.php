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
            1 => 'A1',
            2 => 'AR',
            3 => 'B2',
            4 => 'B3',
            5 => 'C4',
            6 => 'C5',
            7 => 'C6',
            8 => 'D7',
            9 => 'D8',
            10 => 'F9',
            11 => 'ABS',
            12 => 'A',
            13 => 'B',
            14 => 'C',
            15 => 'D',
            16 => 'E',
            17 => 'F',
            18 => 'E8',
            19 => 'P',
        ];
    }
}
