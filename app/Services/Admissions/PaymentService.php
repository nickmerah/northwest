<?php

namespace App\Services\Admissions;

use Illuminate\Support\Facades\Http;

class PaymentService
{
    public function makePayment(int $feeId): array
    {

        $response = Http::withToken(session('access_token'))->post(
            config('app.url') . '/api/v1/makepayment',
            [
                'gateway'     => 'PayStack',
                'feeType'   => $feeId,
                'redirectUrl' => 'https://localhost/northwest/admissions/dashboard',
            ]
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

    public function verifyPayment()
    {
        $response = Http::withToken(session('access_token'))->post(
            config('app.url') . '/api/v1/verifypayment',
            [
                'gateway'     => 'PayStack',
            ]
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
                'message' => 'unable to verify payment.',
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

    public function getPaymentHistory()
    {
        $response = Http::withToken(session('access_token'))->get(
            config('app.url') . '/api/v1/paymenthistory',
            [
                'gateway'     => 'PayStack',
            ]
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
                'message' => 'Unable to retrieve payment records.',
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
