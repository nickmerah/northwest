<?php

namespace App\Repositories;

use App\Models\Admissions\AppFee;
use Illuminate\Support\Facades\DB;
use App\Models\Admissions\AppTransaction;
use App\Interfaces\PaymentRepositoryInterface;
use App\Models\Admissions\AppSession;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function getTransactionID(array $applicant, int $feeId): ?array
    {
        $data = AppTransaction::where([
            'log_id' => $applicant['id'],
            'fee_id' => $feeId,
        ])->first(['trans_no', 'trans_custom1']);

        if (!$data) {
            return null;
        }

        return [
            'transactionID' => $data->trans_no,
            'paymentStatus' => $data->trans_custom1,
        ];
    }

    public function getApplicantFee(array $applicant, int $feeId): array
    {
        $applicantFees = AppFee::getApplicantFees($applicant);
        $feesToPay = array_filter($applicantFees, function ($fee) use ($feeId) {
            return $fee['feeId'] == $feeId;
        });

        return $feesToPay[0] ?? [];
    }

    public function logTransaction(string $paymentResponse, string $responseType): void
    {
        DB::table('remitalogs')->insert([
            'json_data' => $paymentResponse,
            'requesttype' => $responseType
        ]);
    }

    public function saveTransaction(array $applicant, array $feesToPay, array $paystackResponse, string $gateway, string $redirectUrl): void
    {
        $session = AppSession::getAppCurrentSession();
        $now = now();

        $fullnames = trim("{$applicant['surname']} {$applicant['firstname']} {$applicant['othernames']}");

        AppTransaction::insert([
            'log_id'         => $applicant['id'],
            'fee_id'         => $feesToPay['feeId'],
            'fee_name'       => $feesToPay['feeName'],
            'fee_amount'     => $feesToPay['amount'],
            'trans_no'       => $paystackResponse['data']['reference'],
            'rrr'            => $paystackResponse['data']['access_code'],
            'paychannel'     => $gateway,
            'fullnames'      => $fullnames,
            'appno'          => $applicant['applicationNumber'],
            'semail'         => $applicant['email'],
            'trans_date'     => $now,
            'generated_date' => $now,
            't_date'         => $now->toDateString(),
            'trans_year'     => $session,
            'redirect_url'     => $redirectUrl,
        ]);
    }

    public function fetchTransactionDetails(string $transactionId): array
    {
        $data = AppTransaction::where([
            'trans_no' => $transactionId,
        ])->first(['trans_no', 'trans_custom1']);

        if (!$data) {
            return [];
        }

        return [
            'transactionID' => $data->trans_no,
            'paymentStatus' => $data->trans_custom1,
        ];
    }
}
