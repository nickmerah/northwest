<?php

namespace App\Services;

use App\Interfaces\AccountRepositoryInterface;
use App\Interfaces\PaymentRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

class PayStackGateway extends AbstractPaymentGateway
{
    public const PAYMENT_METHOD_PAYSTACK = 'PayStack';

    public function __construct(AccountRepositoryInterface $accountRepository, PaymentRepositoryInterface $paymentRepository)
    {
        parent::__construct($accountRepository, $paymentRepository);
    }


    //save in database

    public function processPayment(int $feeType, string $redirectUrl): array
    {
        $applicant = $this->getApplicant();

        // need to check if portal is closed
        $this->ensurePortalIsOpen($applicant);

        //check if transaction ID has already been generated for application Fee
        $transactionDetails = $this->getTransactionDetails($applicant, $feeType);

        if ($transactionDetails) {
            return ['transactionDetails' => $transactionDetails, 'generateStatus' => false];
        }

        //CONFIGURE paystack setup and generate ref
        $paystackDetails = $this->getPayStackPaymentURL($applicant, $feeType, $redirectUrl);

        return ['generateStatus' => true, 'paymentDetails' => $paystackDetails];
    }

    public function checkPayment(string $transactionId): array
    {
        return ['transactionId' => $transactionId, 'status' => 'success'];
    }

    protected function getPayStackPaymentURL(array $applicant, int $feeType, string $redirectUrl): array
    {

        $timesammp = DATE("dmyHis");
        $orderId = $applicant['id'] . $timesammp;
        $config = app('paystackPayment.config');
        $totalamount = 0;

        $feesToPay = $this->getApplicantFeeDetails($applicant, $feeType);
        $totalamount = $feesToPay['amount'] + $config['transactionFee'];
        $schoolshare = $feesToPay['amount'] - $config['portalFee'];
        $paystackResponse = $this->makePayStackApiCall($applicant, $totalamount, $schoolshare, $orderId, $config);

        //log response on db
        $this->paymentRepository->logTransaction(stripslashes(json_encode($paystackResponse)), "Response");

        //save response to db
        $this->savePayment($applicant, $feesToPay, $paystackResponse, self::PAYMENT_METHOD_PAYSTACK, $redirectUrl);

        return $paystackResponse;
    }

    private function makePayStackApiCall(array $applicant, int $totalamount, int $schoolshare, int $orderId, array $config): ?array
    {
        $data = [];

        try {
            $url = "https://api.paystack.co/transaction/initialize";
            $fields = [
                'email' => $applicant['email'],
                'amount' => $totalamount * 100,
                'subaccount' => $config['subAccount'],
                'reference' => $orderId,
                'transaction_charge' => $schoolshare * 100,
                'bearer' => "subaccount",
                'callback_url' => $config['callback_url'],
                'metadata' => ['cancel_action' => $config['cancel_action_url']]
            ];

            $fields_string = http_build_query($fields);
            $sk = $config['ps_SK'];

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $fields_string,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer $sk",
                    "Cache-Control: no-cache",
                ],
                CURLOPT_RETURNTRANSFER => true,
            ]);

            $result = curl_exec($ch);

            if (curl_errno($ch)) {
                abort(Response::HTTP_BAD_REQUEST, 'Paystack API error: ' . curl_error($ch));
            }

            curl_close($ch);

            $data = json_decode($result, true);
        } catch (\Exception $e) {
            $data = [
                'status' => 0,
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }

        return $data;
    }

    public function savePayment(array $applicant, array $feesToPay, array $paystackResponse, string $gateway, string $redirectUrl): void
    {
        $this->paymentRepository->saveTransaction($applicant, $feesToPay, $paystackResponse,  $gateway, $redirectUrl);
    }

    public function retrieveTransactionDetails(string $transactionId): array
    {
        return $this->paymentRepository->fetchTransactionDetails($transactionId);
    }

    protected function payStack_transaction_details($trxref)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . $trxref,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $this->ps_sk",
                "Cache-Control: no-cache",
            ),
        ));

        $result = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($result, true);

        return $response;
    }
}
