<?php

namespace App\Services\Admissions;

use Illuminate\Support\Facades\Http;

class PaymentService
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

    protected function withAuth(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withToken(session('access_token'))->withHeaders(['Accept' => 'application/json']);
    }

    protected function apiUrl(string $path): string
    {
        return config('app.url') . "/api/v1/{$path}";
    }

    public function makePayment(int $feeId): array
    {
        $payload = [
            'gateway'     => 'PayStack',
            'feeType'     => $feeId,
            'redirectUrl' => 'https://localhost/northwest/admissions/dashboard',
        ];

        return $this->handleRequest(
            fn() => $this->withAuth()->post($this->apiUrl('makepayment'), $payload),
            'unable to process payment.'
        );
    }

    public function verifyPayment(): array
    {
        $payload = ['gateway' => 'PayStack'];

        return $this->handleRequest(
            fn() => $this->withAuth()->post($this->apiUrl('verifypayment'), $payload),
            'unable to verify payment.'
        );
    }

    public function getPaymentHistory(): array
    {
        return $this->handleRequest(
            fn() => $this->withAuth()->get($this->apiUrl('paymenthistory'), ['gateway' => 'PayStack']),
            'Unable to retrieve payment records.'
        );
    }
}
