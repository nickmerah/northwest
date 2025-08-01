<?php

namespace App\Services\Admissions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

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
                'message' => 'unable to logout applicant.',
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

    public function states(): array
    {
        $response = Http::get(
            config('app.url') . '/api/v1/getstateoforigin'
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
                'message' => 'unable to retrieve states of origin.',
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

    public function saveprofile(Request $request): array
    {

        $updateWithPassport = $request->input('updatePassport') ?? 0;

        $client = Http::withToken(session('access_token'))->asMultipart()
            ->withHeaders(['Accept' => 'application/json',]);

        if ($updateWithPassport && $request->hasFile('file')) {
            $client->attach(
                'profilePicture',
                file_get_contents($request->file('file')->getRealPath()),
                $request->file('file')->getClientOriginalName()
            );
        }

        $response = $client->post(config('app.url') . '/api/v1/biodata', [
            'othernames'             => $request->input('othernames'),
            'gender'                 => $request->input('gender'),
            'maritalStatus'          => $request->input('marital_status'),
            'stateofOrigin'          => $request->input('state'),
            'lga'                    => $request->input('lga'),
            'birthDate'              => $request->input('yob') . '-' . $request->input('mob') . '-' . $request->input('dob'),
            'contactAddress'         => $request->input('contact_address'),
            'studentHomeAddress'     => $request->input('student_homeaddress'),
            'homeTown'               => $request->input('hometown'),
            'nextofKin'              => $request->input('nok'),
            'nextofKinAddress'       => $request->input('nok_address'),
            'nextofKinEmail'         => $request->input('nok_email'),
            'nextofKinPhoneNo'       => $request->input('nok_tel'),
            'nextofKinRelationship'  => $request->input('nok_rel'),
            'updateWithPassport'     => $updateWithPassport,
        ]);

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
                'message' => 'Registration failed.',
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

    public function refreshApplicantCache(int $userId, string $token): object
    {
        $response = Http::withToken($token)
            ->get(config('app.url') . '/api/v1/dashboard?include=firstChoiceCourse,programme,stateofOrigin,lga');

        $data = (object) $response->json()['data'];
        Cache::put("dashboard:{$userId}", $data, now()->addHour());

        return $data;
    }

    public function getOlevelResults(): array
    {
        $response = Http::withToken(session('access_token'))->get(
            config('app.url') . '/api/v1/olevels'
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
                'message' => "Unable to retrieve applicant's O'level results.",
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

    public function getOlevelSubjects(): array
    {
        $response = Http::get(config('app.url') . '/api/v1/getolevelsubjects');

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
                'message' => "Unable to retrieve applicant's O'level results.",
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

    public function getOlevelGrades(): array
    {
        $response = Http::get(config('app.url') . '/api/v1/getolevelgrades');

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
                'message' => "Unable to retrieve applicant's O'level results.",
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

    public function saveolevel(Request $request): array
    {

        $first = $request->input('first');
        $second = $request->input('second');

        $firstSubjects = array_filter($first['subjectName'] ?? [], fn($val) => !empty($val));
        $firstGrades = array_filter($first['grade'] ?? [], fn($val) => !empty($val));

        $first['subjectName'] = array_values($firstSubjects);
        $first['grade'] = array_values($firstGrades);
        $first['sitting'] = "First";

        $payload = [
            'first' => $first,
        ];


        if (!empty(array_filter($second['subjectName'] ?? [])) && !empty(array_filter($second['grade'] ?? []))) {
            $secondSubjects = array_filter($second['subjectName'] ?? [], fn($val) => !empty($val));
            $secondGrades = array_filter($second['grade'] ?? [], fn($val) => !empty($val));

            $second['subjectName'] = array_values($secondSubjects);
            $second['grade'] = array_values($secondGrades);
            $second['sitting'] = "Second";

            $payload['second'] = $second;
        }

        $response =  Http::withToken(session('access_token'))
            ->withHeaders(['Accept' => 'application/json',])
            ->post(config('app.url') . '/api/v1/olevels', $payload);

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
                'message' => 'Registration failed.',
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

    public function getJambResults(): array
    {
        $response = Http::withToken(session('access_token'))->get(
            config('app.url') . '/api/v1/jamb'
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
                'message' => "Unable to retrieve applicant'sJamb  results.",
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

    public function savejamb(Request $request): array
    {
        $response = Http::withToken(session('access_token'))->withHeaders([
            'Accept' => 'application/json',
        ])->post(config('app.url') . '/api/v1/jamb', [
            'jambNo'     => $request->input('jambNo'),
            'subjectName' => array_filter($request->input('subjectName', [])),
            'jambScore'   => array_filter($request->input('jambScore', [])),
        ]);

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
                'message' => 'Registration failed.',
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
