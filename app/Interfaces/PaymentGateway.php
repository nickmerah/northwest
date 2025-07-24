<?php

namespace App\Interfaces;

interface PaymentGateway
{
    public function processPayment(int $feeType, string $redirectUrl): array;
    public function savePayment(array $applicant, array $feesToPay, array $paystackResponse, string $gateway, string $redirectUrl): void;
    public function checkPayment(string $transactionId): array;
    public function retrieveTransactionDetails(string $transactionId): array;
}
