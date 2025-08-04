<?php

namespace App\Services\Admissions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RegisterService
{
    protected function handleRequest(callable $callback, string $errorMessage): array
    {
        try {
            $response = $callback();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => $response['message'] ?? 'Request successful.',
                    'data'    => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => $errorMessage,
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

    protected function apiUrl(string $path): string
    {
        return config('app.url') . "/api/v1/{$path}";
    }

    protected function withJson(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withHeaders(['Accept' => 'application/json']);
    }

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

        $payload = $request->only([
            'surname',
            'firstname',
            'othernames',
            'progtype',
            'prog',
            'cos_id',
            'cos_id_two',
            'password',
            'email',
            'phoneno'
        ]);

        return $this->handleRequest(
            fn() => $this->withJson()->post($this->apiUrl('register'), $payload),
            'Registration failed.'
        );
    }

    public function resetPassword(Request $request): array
    {
        $payload = ['applicationNo' => $request->input('regno')];

        return $this->handleRequest(
            fn() => $this->withJson()->post($this->apiUrl('forgotpassword'), $payload),
            'Reset Password failed.'
        );
    }

    public function login(Request $request): array
    {
        $payload = [
            'applicationNo' => $request->input('regno'),
            'password'      => $request->input('passkey'),
        ];

        return $this->handleRequest(
            fn() => $this->withJson()->post($this->apiUrl('login'), $payload),
            'Login failed.'
        );
    }
}
