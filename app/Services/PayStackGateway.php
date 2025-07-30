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
            return ['generateStatus' => false, 'paymentDetails' => $transactionDetails];
        }

        //CONFIGURE paystack setup and generate ref
        $transactionDetails = $this->getPayStackPaymentURL($applicant, $feeType, $redirectUrl);

        return ['generateStatus' => true, 'paymentDetails' => $transactionDetails];
    }

    protected function getPayStackPaymentURL(array $applicant, int $feeType, string $redirectUrl): array
    {
        $timesammp = DATE("dmyHis");
        $orderId = $applicant['id'] . $timesammp;
        $config = app('paystackPayment.config');
        $totalamount = 0;

        $feesToPay = $this->getApplicantFeeDetails($applicant, $feeType);


        $totalamount = $feesToPay['amount'] + $config['transactionFee'];
        $schoolshare = $feesToPay['amount'];

        // we only charge portal fees for application form fee 
        if ($feeType == 1) {
            $schoolshare = $feesToPay['amount'] - $config['portalFee'];
        }

        $paystackResponse = $this->makePayStackApiCall($applicant, $totalamount, $schoolshare, $orderId, $config);

        if ($paystackResponse['status'] != 1) {
            abort(Response::HTTP_BAD_REQUEST, $paystackResponse['message']);
        }

        //log response on db
        $this->paymentRepository->logTransaction(stripslashes(json_encode($paystackResponse)), "Response");

        //save response to db
        $transactionDetails = $this->savePayment($applicant, $feesToPay, $paystackResponse, self::PAYMENT_METHOD_PAYSTACK, $redirectUrl);

        return $transactionDetails;
    }

    private function makePayStackApiCall(array $applicant, int $totalamount, int $schoolshare, int $orderId, array $config): ?array
    {
        $data = [];

        try {
            $url = "https://api.paystack.co/transaction/initialize";
            $fields = [
                'email' => $applicant['email'],
                'first_name' => $applicant['firstname'],
                'last_name' => $applicant['surname'],
                'phone' => $applicant['phoneNumber'],
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
            $response = json_decode($result, true);
            if (curl_errno($ch)) {
                abort(Response::HTTP_BAD_REQUEST, 'Paystack API error: ' . curl_error($ch));
            }

            curl_close($ch);

            abort_if(!$response['status'], Response::HTTP_BAD_REQUEST, $response['message']);

            $data =  $response;
        } catch (\Exception $e) {
            $data = [
                'status' => 0,
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }

        return $data;
    }

    public function savePayment(array $applicant, array $feesToPay, array $paystackResponse, string $gateway, string $redirectUrl): array
    {
        return $this->paymentRepository->saveTransaction($applicant, $feesToPay, $paystackResponse,  $gateway, $redirectUrl);
    }

    public function retrieveTransactionDetails(string $transactionId): array
    {
        return $this->paymentRepository->fetchTransactionDetails($transactionId);
    }

    public function updateTransaction(array $transactionDetails): array
    {
        $data = [];

        $trxref = $transactionDetails['transactionID'];
        $response = self::payStackTransactionDetails($trxref);
        $status = $response['data']['status'];
        $reference = $response['data']['reference'];
        if ($status != "success") {
            $data = [
                'paymentStatus' => $status,
                'message' => $response['data']['gateway_response'],
                'data' => ['redirectUrl' => null],
            ];
        } else {
            $data =  $this->paymentRepository->updatePayment($reference);
        }

        return $data;
    }

    public static function payStackTransactionDetails($trxref)
    {
        $config = app('paystackPayment.config');
        $sk = $config['ps_SK'];
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
                "Authorization: Bearer $sk",
                "Cache-Control: no-cache",
            ),
        ));

        $result = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($result, true);

        return $response;
    }

    public function checkPayment(string $gateway): array
    {
        $applicant = $this->getApplicant();

        $allTransactions = $this->paymentRepository->getAllTransactionsByGateway($gateway, $applicant['id']);

        if (empty($allTransactions)) {
            abort(Response::HTTP_NOT_FOUND, "No pending transactions found.");
        }

        foreach ($allTransactions as $allTransaction) {
            $response = self::payStackTransactionDetails($allTransaction['trans_no']);
            if ($response['status'] == true) {
                $status = $response['data']['status'];
                $reference = $response['data']['reference'];
                if ($status === "success") {
                    $this->paymentRepository->updatePayment($reference);
                }
            }
        }

        return ['status' => true];
    }
}
