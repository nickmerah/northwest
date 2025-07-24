<?php

namespace App\Services;

use App\Interfaces\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Interfaces\AccountRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractPaymentGateway implements PaymentGateway
{
    protected AccountRepositoryInterface $accountRepository;
    protected PaymentRepositoryInterface $paymentRepository;

    public function __construct(AccountRepositoryInterface $accountRepository, PaymentRepositoryInterface  $paymentRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->paymentRepository = $paymentRepository;
    }

    protected function getUserId(): int
    {
        return Auth::user()?->log_id ?? abort(Response::HTTP_BAD_REQUEST, 'Unauthenticated user.');
    }

    protected function getApplicant(): array
    {
        $userId = $this->getUserId();

        return Cache::get("applicant:{$userId}")
            ?? abort(Response::HTTP_BAD_REQUEST, 'Cached applicant not found.');
    }

    protected function ensurePortalIsOpen(array $applicant): void
    {
        if (!$applicant['portalStatus']) {
            abort(Response::HTTP_BAD_REQUEST, 'Portal is CLOSED to Admission Application.');
        }
    }

    protected function getTransactionDetails(array $applicant, int $feeId): ?array
    {
        $paymentDetails =  $this->paymentRepository->getTransactionID($applicant, $feeId);

        if (!$paymentDetails) {
            return null;
        }

        if ($paymentDetails['paymentStatus'] === 'Paid') {
            abort(Response::HTTP_BAD_REQUEST, 'Fees already Paid, Proceed to print your receipt.');
        }

        return $paymentDetails;
    }

    protected function getApplicantFeeDetails(array $applicant, int $feeId): ?array
    {
        $feeDetails =  $this->paymentRepository->getApplicantFee($applicant, $feeId);

        if (empty($feeDetails)) {
            abort(Response::HTTP_BAD_REQUEST, 'Error generating fees');
        }

        return $feeDetails;
    }
}
