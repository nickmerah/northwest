<?php

namespace App\Http\Controllers;

use App\Models\RTransaction;
use App\Services\FeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ValidatesRemedialUser;
use App\Http\Controllers\RemedialController;

class RemedialPaymentController extends Controller
{
    use ValidatesRemedialUser;

    public const PAYMENT_METHOD = 'Remita';
    protected $merchantId,
        $serviceTypeId,
        $apiKey,
        $gatewayUrl,
        $gatewayRRRPaymentUrl,
        $checkStatusUrl,
        $path,
        $first_bank_main;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $response = $this->ValidateRemedialUser();
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
    }

    public function payNow(Request $request)
    {
        $validatedData = $request->validate([
            'noCourses' => 'required|integer',
        ]);

        $noCourses = $validatedData['noCourses'];

        if ($noCourses <= 0) {
            return redirect('/makepayment')->with('error', 'No of Courses to pay for has not been selected.');
        }

        $feedata = [];

        $CourseRegFees = RemedialController::getCourseRegFees($noCourses);

        //attempt to retrieve student email and phone number
        $student = DB::table('stdaccess')
            ->select('email', 'gsm')
            ->where('matno', $this->remedialstudent->matno)
            ->first();

        if ($student) {
            $email = $student->email;
            $gsm = $student->gsm;
        } else {
            $email = "remedialpayment@gmail.com";
            $gsm = "08036858741";
        }

        $totalAmount = $CourseRegFees['total'];

        $fullNames = $this->remedialstudent->surname . " " . $this->remedialstudent->firstname . " " . $this->remedialstudent->othernames;

        $timesammp = DATE("dmyHis");
        $orderId = $this->remedialstudent->id . $timesammp;


        $hash_string = $this->merchantId . $this->serviceTypeId . $orderId . $totalAmount . $this->apiKey;
        $apiHash = hash('sha512', $hash_string);

        $postFields = array(
            "serviceTypeId" => $this->serviceTypeId,
            "amount" => $totalAmount,
            "orderId" => $orderId,
            "payerName" => $fullNames,
            "payerEmail" => $email,
            "payerPhone" => $gsm,
            "description" => "Remedial Payment"
        );

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
            $feeService = new FeeService($this->remedialstudent);
            $feeService->insertJsonData(json_encode($postFields), "Request");
            $feeService->insertJsonData(stripslashes(json_encode($jsonData)), "Response");


            $response = json_decode($jsonData, true);
        }
        curl_close($curl);

        if (empty($response)) {

            $message = "Error generating RRR. Please try again";
            $redirectUrl = url('/makepayment');
            self::redirectWithAlert($message, $redirectUrl);
        }

        $statuscode = $response['statuscode'];

        if ($statuscode != '025') {
            $message = "Error generating RRR. Please try again";
            $redirectUrl = url('/makepayment');
            self::redirectWithAlert($message, $redirectUrl);
        }

        $rrr = trim($response['RRR']);

        $checkrrr = RTransaction::where(['rrr' => $rrr])->get();

        if (!$checkrrr->isEmpty()) {
            $message = "RRR already generated. Please try again";
            $redirectUrl = url('/makepayment');
            self::redirectWithAlert($message, $redirectUrl);
        }
        //end remita
        $sess = DB::table('rcurrent_session')
            ->select('cs_session')
            ->where('status', 'current')
            ->first();


        foreach ($CourseRegFees['feeDetails'] as $feeName => $feeData) {

            if ($feeData['amount'] > 0) {
                $feedata[] = [
                    'log_id' => $this->remedialstudent->id,
                    'fee_id' => $feeData['id'],
                    'fee_name' => $feeName,
                    'trans_no' => $orderId,
                    'fee_amount' => $feeData['amount'],
                    't_date' => date('Y-m-d'),
                    'trans_year' => $sess->cs_session,
                    'trans_custom1' => 'Pending',
                    'fullnames' => $fullNames,
                    'matno' => $this->remedialstudent->matno,
                    'course' => $noCourses,
                    'paychannel' => self::PAYMENT_METHOD,
                    'rrr' => $rrr,
                ];
            }
        }

        // print_r($feedata); exit;

        if (!empty($feedata)) {
            RTransaction::insert($feedata);
            $message = "Transaction ID generated successfully, You will be redirected to make payment";
            $redirectUrl = url('/viewremedialfee/' . $rrr);
            self::redirectWithAlert($message, $redirectUrl);
        } else {
            return redirect('/makepayment')->with('error', 'No fees found to process.');
        }
    }

    public function payNowAgain(Request $request)
    {
        $validatedData = $request->validate([
            'noCourses' => 'required|integer',
        ]);

        $noCourses = $validatedData['noCourses'];

        if ($noCourses <= 0) {
            return redirect('/makepayment')->with('error', 'No of Courses to pay for has not been selected.');
        }

        $feedata = [];

        $fees = DB::table('rfield')->whereNotIn('field_id', [1])->get();

        $additionalFee = 0;
        $serviceCharge = 0;
        $additionalFeePerCourse = 0;

        foreach ($fees as $fee) {
            if ($fee->field_name === 'Additional Fee') {
                $additionalFee = $fee->amount;
                $additionalFeeId = $fee->field_id;
            } elseif ($fee->field_name === 'Service Charge') {
                $serviceCharge = $fee->amount;
                $serviceChargeId = $fee->field_id;
            }
        }

        $additionalFeePerCourse = $noCourses * $additionalFee;

        $feeDetails = [
            'Additional Fee' => [
                'id' => $additionalFeeId,
                'amount' => $additionalFeePerCourse,
            ],
            'Service Charge' => [
                'id' => $serviceChargeId,
                'amount' => $serviceCharge,
            ],
        ];



        $totalAmount =  $additionalFeePerCourse + $serviceCharge;



        //attempt to retrieve student email and phone number
        $student = DB::table('stdaccess')
            ->select('email', 'gsm')
            ->where('matno', $this->remedialstudent->matno)
            ->first();

        if ($student) {
            $email = $student->email;
            $gsm = $student->gsm;
        } else {
            $email = "remedialpayment@gmail.com";
            $gsm = "08036858741";
        }


        $fullNames = $this->remedialstudent->surname . " " . $this->remedialstudent->firstname . " " . $this->remedialstudent->othernames;

        $timesammp = DATE("dmyHis");
        $orderId = $this->remedialstudent->id . $timesammp;


        $hash_string = $this->merchantId . $this->serviceTypeId . $orderId . $totalAmount . $this->apiKey;
        $apiHash = hash('sha512', $hash_string);

        $postFields = array(
            "serviceTypeId" => $this->serviceTypeId,
            "amount" => $totalAmount,
            "orderId" => $orderId,
            "payerName" => $fullNames,
            "payerEmail" => $email,
            "payerPhone" => $gsm,
            "description" => "Remedial Payment"
        );

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
            $feeService = new FeeService($this->remedialstudent);
            $feeService->insertJsonData(json_encode($postFields), "Request");
            $feeService->insertJsonData(stripslashes(json_encode($jsonData)), "Response");


            $response = json_decode($jsonData, true);
        }
        curl_close($curl);

        if (empty($response)) {

            $message = "Error generating RRR. Please try again";
            $redirectUrl = url('/makepayment');
            self::redirectWithAlert($message, $redirectUrl);
        }

        $statuscode = $response['statuscode'];

        if ($statuscode != '025') {
            $message = "Error generating RRR. Please try again";
            $redirectUrl = url('/makepayment');
            self::redirectWithAlert($message, $redirectUrl);
        }

        $rrr = trim($response['RRR']);

        $checkrrr = RTransaction::where(['rrr' => $rrr])->get();

        if (!$checkrrr->isEmpty()) {
            $message = "RRR already generated. Please try again";
            $redirectUrl = url('/makepayment');
            self::redirectWithAlert($message, $redirectUrl);
        }
        //end remita
        $sess = DB::table('rcurrent_session')
            ->select('cs_session')
            ->where('status', 'current')
            ->first();

        foreach ($feeDetails as $feeName => $feeData) {

            if ($feeData['amount'] > 0) {
                $feedata[] = [
                    'log_id' => $this->remedialstudent->id,
                    'fee_id' => $feeData['id'],
                    'fee_name' => $feeName,
                    'trans_no' => $orderId,
                    'fee_amount' => $feeData['amount'],
                    't_date' => date('Y-m-d'),
                    'trans_year' => $sess->cs_session,
                    'trans_custom1' => 'Pending',
                    'fullnames' => $fullNames,
                    'matno' => $this->remedialstudent->matno,
                    'course' => $noCourses,
                    'paychannel' => self::PAYMENT_METHOD,
                    'rrr' => $rrr,
                ];
            }
        }

        if (!empty($feedata)) {
            RTransaction::insert($feedata);
            $message = "Transaction ID generated successfully, You will be redirected to make payment";
            $redirectUrl = url('/viewremedialfee/' . $rrr);
            self::redirectWithAlert($message, $redirectUrl);
        } else {
            return redirect('/makepayment')->with('error', 'No fees found to process.');
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

            RTransaction::where('rrr', $rrr)
                ->where('trans_no', $orderid)
                ->update($udata);

            // Success message and redirection
            $rrr = htmlspecialchars($rrr, ENT_QUOTES);
            $message = "Transaction is Successful, Your RRR is $rrr. Kindly print your receipt. This transaction will be subject to verification by the Bursary Unit";
            $redirectUrl = url('/rphistory');
            self::redirectWithAlert($message, $redirectUrl);
        } else {

            $rrr = htmlspecialchars($rrr, ENT_QUOTES);
            $orderid = htmlspecialchars($orderid, ENT_QUOTES);
            $message = "Transaction is Pending, Your RRR is $rrr. Kindly requery your transaction if debited or try again";
            $redirectUrl = url('/rphistory');
            self::redirectWithAlert($message, $redirectUrl);
        }
    }

    public function checkpayment()
    {
        $transids = RTransaction::getPendingTransactions($this->remedialstudent->id);

        if ($transids->isEmpty()) {
            $message = "No Transaction had been generated. Kindly generate payment.";
            $redirectUrl = url('/makepayment');
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

                    RTransaction::where('rrr', $result->rrr)
                        ->where('trans_no', $result->orderId)
                        ->update($udata);

                    $message = "Transactions is successful, you will be redirected to payment history";
                    $redirectUrl = url('rphistory/');
                    self::redirectWithAlert($message, $redirectUrl);
                }
            }

            $message = "Transactions have been reprocessed, you will be redirected to payment history";
            $redirectUrl = url('rphistory/');
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
}
