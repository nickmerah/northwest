<?php

namespace App\Repositories;

use App\Models\Admissions\AppFee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Admissions\AppProfile;
use App\Models\Admissions\AppSession;
use App\Models\Admissions\AppTransaction;
use App\Interfaces\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function getTransactionID(array $applicant, int $feeId): ?array
    {
        $data = AppTransaction::where([
            'log_id' => $applicant['id'],
            'fee_id' => $feeId,
        ])->first(['trans_no', 'trans_custom1', 'rrr', 'fullnames', 'appno', 'trans_year', 't_date', 'fee_name', 'fee_amount']);

        if (!$data) {
            return null;
        }

        return [
            'transactionID' => $data->trans_no,
            'paymentStatus' => $data->trans_custom1,
            'paymentUrl' => $data->rrr,
            'fullNames' => $data->fullnames,
            'applicationNo' => $data->appno,
            'appYear' => $data->trans_year,
            'transactionDate' => $data->t_date,
            'feeName' => $data->fee_name,
            'feeAmount' => $data->fee_amount,
        ];
    }

    public function getApplicantFee(array $applicant, int $feeId): array
    {
        $applicantFees = AppFee::getApplicantFees($applicant);
        $isIndigene = AppProfile::isIndigene($applicant['id']);

        // Filter by feeId
        $feesToPay = array_filter($applicantFees, function ($fee) use ($feeId) {
            return $fee['feeId'] == $feeId;
        });

        $feesToPay = array_values($feesToPay);

        if (empty($feesToPay)) {
            return [];
        }

        // Handle Acceptance Fee (feeId == 2) with Deltan/General logic
        if ($feeId === 2) {
            $selectedFee = collect($feesToPay)->firstWhere('statusStatus', $isIndigene ? 'Deltan' : 'General')
                ?? $feesToPay[0];
            return $selectedFee;
        }

        return $feesToPay[0];
    }


    public function logTransaction(string $paymentResponse, string $responseType): void
    {
        DB::table('remitalogs')->insert([
            'json_data' => $paymentResponse,
            'requesttype' => $responseType
        ]);
    }

    public function saveTransaction(array $applicant, array $feesToPay, array $paystackResponse, string $gateway, string $redirectUrl): array
    {
        $session = AppSession::getAppCurrentSession();
        $now = now();

        $fullnames = trim("{$applicant['surname']} {$applicant['firstname']} {$applicant['othernames']}");

        $transaction = AppTransaction::create([
            'log_id'         => $applicant['id'],
            'fee_id'         => $feesToPay['feeId'],
            'fee_name'       => $feesToPay['feeName'],
            'fee_amount'     => $feesToPay['amount'],
            'trans_no'       => $paystackResponse['data']['reference'],
            'rrr'            => $paystackResponse['data']['authorization_url'],
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

        return [
            'feeName'         => $transaction->fee_name,
            'feeAmount'       => $transaction->fee_amount,
            'transactionID'   => $transaction->trans_no,
            'paymentUrl'      => $transaction->rrr,
            'fullNames'       => $transaction->fullnames,
            'applicationNo'   => $transaction->appno,
            'transactionDate' => $transaction->t_date,
            'appYear'         => $transaction->trans_year,
            'paymentStatus'   => 'Pending',
        ];
    }

    public function fetchTransactionDetails(string $transactionId): array
    {
        $data = AppTransaction::where([
            'trans_no' => $transactionId,
        ])->first(['trans_no', 'trans_custom1', 'paychannel']);

        if (!$data) {
            return [];
        }

        return [
            'transactionID' => $data->trans_no,
            'paymentStatus' => $data->trans_custom1,
            'payChannel' => $data->paychannel,
        ];
    }

    public function updatePayment(string $transactionId): array
    {
        $data = AppTransaction::where([
            'trans_no' => $transactionId,
            'trans_custom1' => 'Pending',
        ])->first(['redirect_url']);

        if (!$data) {
            return [
                'paymentStatus' => "Failed",
                'message' => "Unresolved to process this payment, Kindly update payment from the portal.",
                'data' => ['redirectUrl' => null],

            ];
        }
        $datePaid = date('Y-m-d');
        $dateTimePaid = date('Y-m-d h:i:s');
        AppTransaction::where([
            'trans_no' => $transactionId,
            'trans_custom1'  => "Pending",
        ])->update([
            'trans_custom1'  => "Paid",
            't_date'  => $datePaid,
            'trans_date'  => $dateTimePaid,
        ]);

        return [
            'paymentStatus' => "Paid",
            'message' => "Transaction is Successful, Kindly print your receipt . This transaction will be subject to verification by the Bursary Unit",
            'data' => [
                'transactionID' => $transactionId,
                'redirectUrl' => $data->redirect_url,
            ],
        ];
    }

    public function getAllTransactionsByGateway(string $gateway, int $applicantId): array
    {
        $data = AppTransaction::where([
            'paychannel' => $gateway,
            'log_id' => $applicantId,
            'trans_custom1' => 'Pending',
        ])->groupBy('trans_no')->get(['trans_no'])->toArray();

        return $data;
    }

    public static function getAllPaidTransactions(): array
    {
        $records = AppTransaction::where([
            'log_id' => Auth::user()?->log_id,
            'trans_custom1' => 'Paid',
        ])->get(['trans_no', 'trans_custom1', 'fee_amount', 'trans_year', 't_date', 'fee_name', 'fullnames', 'appno']);

        if ($records->isEmpty()) {
            return [];
        }

        return $records->map(function ($item) {
            return [
                'applicationNo' => $item->appno,
                'fullNames' => $item->fullnames,
                'transactionID' => $item->trans_no,
                'paymentStatus' => $item->trans_custom1,
                'amount' => $item->fee_amount,
                'sessionPaid' => $item->trans_year,
                'datePaid' => $item->t_date->toFormattedDateString(),
                'feeName' => $item->fee_name,
            ];
        })->toArray();
    }
}
