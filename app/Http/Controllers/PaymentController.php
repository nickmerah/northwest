<?php

namespace App\Http\Controllers;

use App\Models\CTransaction;
use App\Services\FeeService;
use Illuminate\Http\Request;
use App\Models\ClearanceFees;
use App\Traits\ValidatesUser;

class PaymentController extends Controller
{
    use ValidatesUser;

    public const PAYMENT_METHOD = 'Remita';
    protected $merchantId,
        $serviceTypeId,
        $apiKey,
        $gatewayUrl,
        $gatewayRRRPaymentUrl,
        $checkStatusUrl,
        $path,
        $first_bank_main,
        $first_bank_alumni,
        $zenith_bank_compendium,
        $uba_bank_compendium;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $response = $this->validateUser();
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }
            return $next($request);
        });

        // Remita
        $paymentConfig = app('remitaPayment.config');
        $this->merchantId = $paymentConfig['merchant_id'];
        $this->serviceTypeId = $paymentConfig['service_type_id'];
        $this->apiKey = $paymentConfig['api_key'];
        $this->gatewayUrl = $paymentConfig['gateway_url'];
        $this->gatewayRRRPaymentUrl = $paymentConfig['gateway_rrr_payment_url'];
        $this->checkStatusUrl = $paymentConfig['check_status_url'];
        $this->path = 'https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

        // Bank Accounts
        $accountsConfig = app('accounts.config');
        $this->first_bank_main = $accountsConfig['first_bank_main'];
        $this->first_bank_alumni = $accountsConfig['first_bank_alumni'];
        $this->zenith_bank_compendium = $accountsConfig['zenith_bank_compendium'];
        $this->uba_bank_compendium = $accountsConfig['uba_bank_compendium'];
    }

    public function payPackNow($packId)
    {
        $fees = ClearanceFees::with('feeField')->where(['pack_id' => $packId, 'prog_id' => $this->student->prog_id])->get();
        if ($fees->isEmpty()) {
            return redirect('/clearanceFees')->with('error', 'Fee Pack not found.');
        }


        // check if fees had been generated previuosly

        $oneFeeItem = $fees[0]->item_id;
        $alreadyGenerated = CTransaction::checkIfTransactionAlreadyExists($this->student->csid, $oneFeeItem);

        if (!$alreadyGenerated->isEmpty()) {
            if (empty($response)) {

                $message = "Transaction Already Generated, you will be directed to make payment";
                $redirectUrl = url('/viewfee/' . $alreadyGenerated[0]->rrr);
                self::redirectWithAlert($message, $redirectUrl);
            }
        }

        $feedata = [];


        $totalAmount = $fees->sum('amount');

        $fullNames = $this->student->surname . " " . $this->student->firstname . " " . $this->student->othernames;

        $timesammp = DATE("dmyHis");
        $orderId = session()->get('log_id') . $timesammp;

        $hash_string = $this->merchantId . $this->serviceTypeId . $orderId . $totalAmount . $this->apiKey;
        $apiHash = hash('sha512', $hash_string);

        $postFields = array(
            "serviceTypeId" => $this->serviceTypeId,
            "amount" => $totalAmount,
            "orderId" => $orderId,
            "payerName" => $fullNames,
            "payerEmail" => $this->student->email ?? "clearance@gmail.com",
            "payerPhone" => $this->student->phone ?? "08036858741",
            "description" => "Final Clearance Fees"
        );

        $lineItems = self::getLineItems($packId, $totalAmount);
        if ($lineItems !== null) {
            $postFields["lineItems"] = $lineItems;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->gatewayUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postFields),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: remitaConsumerKey=' . $this->merchantId . ',remitaConsumerToken=' . $apiHash
            ),
        ));

        $json_response = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'cURL error: ' . curl_error($curl);
        } else {
            $jsonData = substr($json_response, 7, -1);

            //log on db
            $feeService = new FeeService($this->student);
            $feeService->insertJsonData(json_encode($postFields), "Request");
            $feeService->insertJsonData(stripslashes(json_encode($jsonData)), "Response");


            $response = json_decode($jsonData, true);
        }
        curl_close($curl);
        //   print_r($response); exit;

        if (empty($response)) {

            $message = "Error generating RRR. Please try again";
            $redirectUrl = url('/clearanceFees');
            self::redirectWithAlert($message, $redirectUrl);
        }

        $statuscode = $response['statuscode'];

        if ($statuscode != '025') {
            $message = "Error generating RRR. Please try again";
            $redirectUrl = url('/clearanceFees');
            self::redirectWithAlert($message, $redirectUrl);
        }

        $rrr = trim($response['RRR']);

        $checkrrr = CTransaction::where(['rrr' => $rrr])->get();

        if (!$checkrrr->isEmpty()) {
            $message = "RRR already generated. Please try again";
            $redirectUrl = url('/clearanceFees');
            self::redirectWithAlert($message, $redirectUrl);
        }
        //end remita


        foreach ($fees as $fee) {
            $feedata[] =
                [
                    'log_id' => $this->student->csid,
                    'fee_id' => $fee->item_id,
                    'fee_name' => $fee->feeField->field_name,
                    'trans_no' => $orderId,
                    'fee_amount' => $fee->amount,
                    't_date' => date('Y-m-d'),
                    'trans_year' => date('Y'),
                    'trans_custom1' => 'Pending',
                    'fullnames' => $fullNames,
                    'matno' => $this->student->matricno,
                    'course' => $this->student->prog_id,
                    'paychannel' => self::PAYMENT_METHOD,
                    'rrr' => $rrr,
                ];
        }

        if (!empty($feedata)) {
            CTransaction::insert($feedata);
            $message = "Transaction ID generated successfully, You will be redirected to make payment";
            $redirectUrl = url('/viewfee/' . $rrr);
            self::redirectWithAlert($message, $redirectUrl);
        } else {
            return redirect('/clearanceFees')->with('error', 'No fees found to process.');
        }
    }

    private function redirectWithAlert($message, $url)
    {
        echo '<script type="text/javascript">
        alert("' . addslashes($message) . '");
        window.location = "' . addslashes($url) . '";
    </script>';
        exit;
    }

    public function processFees($rrr)
    {
        $responseurl = url('/remitaresponse');
        $new_hash_string = $this->merchantId . $rrr . $this->apiKey;
        $new_hash = hash('sha512', $new_hash_string);

        echo '<form action="' . $this->gatewayRRRPaymentUrl . '" name= "apiform" method="POST" id="apiform">
				<input id="merchantId" name="merchantId" value="' . $this->merchantId . '" type="hidden"/>
				<input id="rrr" name="rrr" value="' . $rrr . '" type="hidden"/>
				<input id="responseurl" name="responseurl" value="' . $responseurl . '" type="hidden"/>
				<input id="hash" name="hash" value="' . $new_hash . '" type="hidden"/>
				<script language="JavaScript">document.apiform.submit();</script>
			</form>';
    }

    public function remitaresponse(Request $request)
    {
        // Retrieve query parameters
        $rrr = $request->query('rrr');
        $orderid = $request->query('orderID');


        $response = self::remita_transaction_details($orderid);
        $response_code = $response['status'];


        if ($response_code == '01' || $response_code == '00') {

            $payment_date = date('Y-m-d H:i:s');
            $pt_date = date('Y-m-d', strtotime($payment_date));
            $udata = [
                'trans_custom1'  => "Paid",
                'trans_date' => $payment_date,
                't_date' => $pt_date,
            ];

            CTransaction::where('rrr', $rrr)
                ->where('trans_no', $orderid)
                ->update($udata);

            // Success message and redirection
            $rrr = htmlspecialchars($rrr, ENT_QUOTES);
            $message = "Transaction is Successful, Your RRR is $rrr. Kindly print your receipt. This transaction will be subject to verification by the Bursary Unit";
            $redirectUrl = url('/phistory');
            self::redirectWithAlert($message, $redirectUrl);
        } else {

            $rrr = htmlspecialchars($rrr, ENT_QUOTES);
            $orderid = htmlspecialchars($orderid, ENT_QUOTES);
            $message = "Transaction is Pending, Your RRR is $rrr. Kindly requery your transaction if debited or try again";
            $redirectUrl = url('/paymentslip/' . $orderid);
            self::redirectWithAlert($message, $redirectUrl);
        }
    }

    public function checkpayment()
    {
        $transids = CTransaction::getPendingTransactions($this->student->csid);

        if ($transids->isEmpty()) {
            $message = "No Transaction had been generated\nKindly generate payment.";
            $redirectUrl = url('clearanceFees');
            self::redirectWithAlert($message, $redirectUrl);
        } else {

            foreach ($transids as $transid) {

                $hash_string = $transid->trans_no .  $this->apiKey . $this->merchantId;
                $apiHash = hash('sha512', $hash_string);

                //revalidate payment

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => $this->checkStatusUrl . "/$this->merchantId/$transid->trans_no/$apiHash/orderstatus.reg",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        "Authorization: remitaConsumerKey=2547916,remitaConsumerToken=$apiHash"
                    ),
                ));

                $response = curl_exec($curl);
                curl_close($curl);

                $result = json_decode($response);

                $payment_date = date('Y-m-d H:i:s');
                $pt_date = date('Y-m-d', strtotime($payment_date));

                if ($result->message == "Successful" && $result->status == "00") {
                    $udata = [
                        'trans_custom1'  => 'Paid',
                        'trans_date' => $payment_date,
                        't_date' => $pt_date,
                    ];

                    CTransaction::where('rrr', $result->rrr)
                        ->where('trans_no', $result->orderId)
                        ->update($udata);

                    $message = "Transactions is successful, you will be redirected to payment history";
                    $redirectUrl = url('phistory/');
                    self::redirectWithAlert($message, $redirectUrl);
                }
            }

            $message = "Transactions have been reprocessed, you will be redirected to payment history";
            $redirectUrl = url('phistory/');
            self::redirectWithAlert($message, $redirectUrl);
        }
    }

    protected function remita_transaction_details($orderId)
    {
        $mert =  $this->merchantId;
        $api_key =  $this->apiKey;
        $concatString = $orderId . $api_key . $mert;
        $hash = hash('sha512', $concatString);
        $url     = $this->checkStatusUrl . '/' . $mert  . '/' . $orderId . '/' . $hash . '/' . 'orderstatus.reg';

        //  Initiate curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($result, true);
        return $response;
    }

    protected function createLineItem($lineItemsId, $beneficiaryAccount, $beneficiaryAmount, $deductFeeFrom, $bankCode = "011")
    {
        return array(
            "lineItemsId" => $lineItemsId,
            "beneficiaryName" => "Delta State Polytechnic, Ogwashi-Uku",
            "beneficiaryAccount" => $beneficiaryAccount,
            "bankCode" => $bankCode,
            "beneficiaryAmount" => $beneficiaryAmount,
            "deductFeeFrom" => $deductFeeFrom
        );
    }

    protected function getLineItems($packid, $totalAmount)
    {
        $lineItems = array();

        switch ($packid) {
            case 1:
                $alumni = 1000;
                $balance = $totalAmount - $alumni;
                $lineItems = array(
                    self::createLineItem("itemid1", $this->first_bank_main, $balance, "1"),
                    self::createLineItem("itemid2", $this->first_bank_alumni, $alumni, "0")
                );
                break;

            case 2:
                // No line items for packid 2
                return null;

            case 3:
                $compendium = 5250;
                $balance = $totalAmount - $compendium;
                $lineItems = array(
                    self::createLineItem("itemid1", $this->first_bank_main, $balance, "1"),
                    self::createLineItem("itemid3", $this->uba_bank_compendium, $compendium, "0", "033")
                );
                break;

            default:
                // Handle unexpected packid values
                return null;
        }

        return $lineItems;
    }
}
