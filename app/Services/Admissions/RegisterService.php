<?php

namespace App\Services\Admissions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RegisterService
{
    public function register(Request $request): array
    {
        $expected = (int) $request->input('first_number') + (int) $request->input('second_number');
        $actual = (int) $request->input('captchaResult');

        if ($actual !== $expected) {
            return [
                'success' => false,
                'message' => 'Registration failed.',
                'data' => [
                    'message' => 'Incorrect answer to the math question.',
                    'errors' => [
                        'captchaResult' => ['Incorrect answer to the math question.']
                    ],
                ],
            ];
        }


        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post(config('app.url') . '/api/v1/register', [
            'surname'     => $request->input('surname'),
            'firstname'   => $request->input('firstname'),
            'othernames'  => $request->input('othernames'),
            'progtype'    => $request->input('progtype'),
            'prog'        => $request->input('prog'),
            'cos_id'      => $request->input('cos_id'),
            'cos_id_two'  => $request->input('cos_id_two'),
            'password'    => $request->input('password'),
            'email'       => $request->input('email'),
            'phoneno'     => $request->input('phoneno'),
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

    public function resetPassword(Request $request): array
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post(config('app.url') . '/api/v1/forgotpassword', [
            'applicationNo'     => $request->input('regno'),
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
                'message' => 'Reset Password failed.',
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

    public function login(Request $request): array
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post(config('app.url') . '/api/v1/login', [
            'applicationNo'     => $request->input('regno'),
            'password'     => $request->input('passkey'),
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
                'message' => $response['message'],
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
