<?php

namespace App\Interfaces;

interface PaymentGateway
{
    public function processPayment(int $feeType, string $redirectUrl): array;

    public function savePayment(array $applicant, array $feesToPay, array $paystackResponse, string $gateway, string $redirectUrl): array;

    public function checkPayment(string $gateway): array;

    public function retrieveTransactionDetails(string $transactionId): array;

    public function updateTransaction(array $transactionDetails): array;
}
