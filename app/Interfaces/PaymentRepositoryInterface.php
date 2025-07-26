<?php

namespace App\Interfaces;

interface PaymentRepositoryInterface
{
    public function getTransactionID(array $data, int $feeId): ?array;

    public function getApplicantFee(array $data, int $feeId): array;

    public function logTransaction(string $data, string $responseType): void;

    public function saveTransaction(array $data, array $feesToPay, array $paystackResponse, string $gateway, string $redirectUrl): void;

    public function fetchTransactionDetails(string $transactionId): array;

    public function updatePayment(string $transactionId): array;

    public function getAllTransactionsByGateway(string $gateway, int $applicantId): array;

    public static function getAllPaidTransactions(): array;
}
