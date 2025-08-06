<?php

namespace App\Http\Controllers;

use App\Models\OFee;
use App\Models\Hostel;
use App\Models\STransaction;
use App\Services\FeeService;
use Illuminate\Http\Request;
use App\Traits\ValidatesPortalUser;
use App\Services\FeeCalculationService;

class PortalPaymentController extends Controller
{
    use ValidatesPortalUser;

    public const PAYMENT_METHOD = 'Remita';
    protected $merchantId,
        $service_type_id_OTHERS_FEES,
        $service_type_id_SIWES,
        $service_type_id_SUG_NADESSTU_GNS_NIPOGA,
        $service_type_id_SCHOOL_FEES,
        $service_type_id_NEW_SCHOOL_FEES,
        $apiKey,
        $gatewayUrl,
        $gatewayRRRPaymentUrl,
        $checkStatusUrl,
        $path,
        $first_bank_main,
        $union_bank_otherfees,
        $union_SIWES,
        $feeCalculationService,
        $union_bank_NADESSTU,
        $zenith_bank_SUG,
        $union_ANTICULT_SW,
        $premium_trust_bank_NIPOGA,
        $first_bank_GNS,
        $fidelity_MICROSOFT_ACADEMY,
        $ms_academy_split,
        $sterling_bank_main,
        $premium_trust_MEDICALS;


    public function __construct(FeeCalculationService $feeCalculationService)
    {
        $this->feeCalculationService = $feeCalculationService;

        $this->middleware(function ($request, $next) {
            $response = $this->validatePortalUser();
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }
            return $next($request);
        });

        // Remita
        $paymentConfig = app('remitaPayment.config');
        $this->merchantId = $paymentConfig['merchant_id'];
        $this->apiKey = $paymentConfig['api_key'];
        $this->gatewayUrl = $paymentConfig['gateway_url'];
        $this->gatewayRRRPaymentUrl = $paymentConfig['gateway_rrr_payment_url'];
        $this->checkStatusUrl = $paymentConfig['check_status_url'];
        $this->path = 'https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

        // Bank Accounts
        $accountsConfig = app('accounts.config');
        $this->first_bank_main = $accountsConfig['first_bank_main'];
        $this->union_bank_otherfees = $accountsConfig['union_bank_otherfees'];
        $this->union_SIWES = $accountsConfig['union_SIWES'];
        $this->union_bank_NADESSTU = $accountsConfig['union_bank_NADESSTU'];
        $this->zenith_bank_SUG = $accountsConfig['zenith_bank_SUG'];
        $this->union_ANTICULT_SW = $accountsConfig['union_ANTICULT_SW'];
        $this->premium_trust_bank_NIPOGA = $accountsConfig['premium_trust_bank_NIPOGA'];
        $this->first_bank_GNS = $accountsConfig['first_bank_GNS'];
        $this->fidelity_MICROSOFT_ACADEMY = $accountsConfig['fidelity_MICROSOFT_ACADEMY'];
        $this->sterling_bank_main = $accountsConfig['sterling_bank_main'];
        $this->premium_trust_MEDICALS = $accountsConfig['premium_trust_MEDICALS'];

        //Service Type
        $this->service_type_id_OTHERS_FEES = $paymentConfig['service_type_id_OTHERS_FEES'];
        $this->service_type_id_SIWES = $paymentConfig['service_type_id_SIWES'];

        //School Fees Complusory Service Types
        $this->service_type_id_SUG_NADESSTU_GNS_NIPOGA = $paymentConfig['service_type_id_SUG_NADESSTU_GNS_NIPOGA'];
        $this->service_type_id_SCHOOL_FEES = $paymentConfig['service_type_id_SCHOOL_FEES'];
        $this->service_type_id_NEW_SCHOOL_FEES = $paymentConfig['service_type_id_NEW_SCHOOL_FEES'];

        //Service charge
        $this->ms_academy_split = 7000;
    }

    public function saveofees(Request $request)
    {
        if (!$request->has('ofee')) {
            return back()->withErrors('Select at least one Fee to make payment');
        }

        $selectedFees = $request->input('ofee');

        $libraryBindingCopies = $request->input('copies', 0);
        $calculation = $this->feeCalculationService->calculateFees($selectedFees, $libraryBindingCopies);

        $fullNames = $this->getFullNames();
        $orderId = $this->generateOrderId();
        $siwesFeesId = 9;

        // EED1,EED2,EED Extra, Olevel Verification, Sports Clearane, Hostel Maintenance, ND Result Verification
        $ofeeIds = [2, 3, 4, 8, 10, 12, 13];

        // Process SIWES fees if applicable
        if (in_array($siwesFeesId, $selectedFees)) {
            $rrr = $this->processSiwesFee($siwesFeesId, $calculation, $fullNames, $orderId);
        }


        // Process EED1,EED2,EED Extra, Olevel Verification, Sports Clearane, Hostel Maintenance
        if (in_array($selectedFees[0], $ofeeIds)) {
            $rrr = $this->processOtherFeesTwo($selectedFees[0], $calculation, $fullNames, $orderId);
        }

        // Process other selected fees
        $otherselectedFees = array_filter($selectedFees, function ($value) use ($siwesFeesId, $ofeeIds) {
            return $value != $siwesFeesId && !in_array($value, $ofeeIds);
        });

        if (!empty($otherselectedFees)) {
            $ofees = OFee::whereIn('of_id', $otherselectedFees)->get();
            $rrr = $this->processOtherFees($ofees, $calculation, $fullNames, $orderId, $libraryBindingCopies);
        }

        if ($rrr) {
            $message = "Transaction ID generated successfully, You will be redirected to make payment";
            $redirectUrl = url('/viewofee/' . $rrr);
            self::redirectWithAlert($message, $redirectUrl);
        } else {
            return redirect('/ofees')->with('error', 'Error generating fees.');
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

    private function createTransactionData($fee, $orderId, $sess = "", $amount, $fullNames, $rrr, $feetype = "ofees", $hostelid = 0)
    {
        $sessSem = STransaction::getCurrentSemesterSession($this->student->stdprogramme_id);

        $psess = !empty($sess) ? $sess : $sessSem['cs_session'];

        return [
            'log_id' => $this->student->std_logid,
            'fee_id' => isset($fee->of_id) ? $fee->of_id : $fee->field_id,
            'trans_name' => isset($fee->ofield_name) ? $fee->ofield_name : $fee->field_name,
            'trans_no' => $orderId,
            'user_faculty' => $this->student->stdfaculty_id,
            'user_dept' => $this->student->stddepartment_id,
            'levelid' => $this->student->stdlevel,
            'trans_amount' => $amount,
            'trans_year' => $psess,
            'trans_semester' => $sessSem['cs_sem'],
            'pay_status' => 'Pending',
            'policy' => $hostelid,
            't_date' => date('Y-m-d'),
            'fullnames' => $fullNames,
            'prog_id' => $this->student->stdprogramme_id,
            'prog_type' => $this->student->stdprogrammetype_id,
            'stdcourse' => $this->student->stdcourse,
            'appno' => $this->student->matric_no,
            'appsor' => $this->student->state_of_origin,
            'channel' => self::PAYMENT_METHOD,
            'fee_type' => $feetype,
            'rrr' => $rrr,
        ];
    }

    private function checkDuplicateRRR($rrr)
    {
        $checkrrr = STransaction::where(['rrr' => $rrr])->get();
        if (!$checkrrr->isEmpty()) {
            $message = "RRR already generated. Please try again";
            $redirectUrl = url('/ofees');
            self::redirectWithAlert($message, $redirectUrl);
        }
    }

    private function handleRemitaResponse($response, $orderId)
    {
        if (empty($response) || $response['statuscode'] != '025') {
            $message = "Error generating RRR. Please try again";
            $redirectUrl = url('/ofees');
            self::redirectWithAlert($message, $redirectUrl);
        }
    }

    private function makeRemitaApiCall($postFields)
    {
        $hash_string = $this->merchantId . $postFields['serviceTypeId'] . $postFields['orderId'] . $postFields['amount'] . $this->apiKey;
        $apiHash = hash('sha512', $hash_string);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->gatewayUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postFields),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: remitaConsumerKey=' . $this->merchantId . ',remitaConsumerToken=' . $apiHash
            ],
        ]);

        $json_response = curl_exec($curl);
        curl_close($curl);

        if (curl_errno($curl)) {
            throw new \Exception('cURL error: ' . curl_error($curl));
        }

        $jsonData = substr($json_response, 7, -1);

        //log on db
        $feeService = new FeeService($this->student);
        $feeService->insertJsonData(json_encode($postFields), "Request");
        $feeService->insertJsonData(stripslashes(json_encode($jsonData)), "Response");


        return json_decode($jsonData, true);
    }

    private function createPostFields($serviceTypeId, $amount, $orderId, $payerName, $description)
    {
        return [
            "serviceTypeId" => $serviceTypeId,
            "amount" => $amount,
            "orderId" => $orderId,
            "payerName" => $payerName,
            "payerEmail" => $this->student->student_email,
            "payerPhone" => $this->student->student_mobiletel,
            "description" => $description
        ];
    }

    private function processOtherFees($ofees, $calculation, $fullNames, $orderId, $libraryBindingCopies, $hostelid = 0)
    {
        $totalAmount = $calculation['grandTotal'];
        $orderId = session()->get('log_id') . date("dmyHis");

        $postFields = $this->createPostFields($this->service_type_id_OTHERS_FEES, $totalAmount, $orderId, $fullNames, "Portal Other Fees");
        $response = $this->makeRemitaApiCall($postFields);

        $this->handleRemitaResponse($response, $orderId);

        $rrr = trim($response['RRR']);

        // Check if RRR is already generated
        $this->checkDuplicateRRR($rrr);

        $sess = ""; // current session

        $ofeedata = [];
        foreach ($ofees as $fee) {
            $amt = ($fee->of_id == 11) ? ($calculation['totalBindingFee'] + $calculation['serviceCharge']) : $fee->of_amount;
            $damt = $amt;
            $ofeedata[] = $this->createTransactionData($fee, $orderId, $sess, $damt, $fullNames, $rrr, 'ofees', $hostelid);
        }

        if (!empty($ofeedata)) {
            STransaction::insert($ofeedata);
            return $rrr;
        } else {
            return false;
        }
    }


    private function processSiwesFee($siwesFeesId, $calculation, $fullNames, $orderId)
    {
        $siwesFees = OFee::find($siwesFeesId);
        $siwesFeeAmount = $siwesFees->of_amount;
        $siwesTotal = $siwesFeeAmount - $calculation['serviceCharge'];

        $postFields = $this->createPostFields($this->service_type_id_SIWES, $siwesFeeAmount, $orderId, $fullNames, "SIWES Fee");

        //add split

        $postFields["lineItems"] = [
            [
                "lineItemsId" => 'SERVICE_CHARGE',
                "beneficiaryName" => "STATE POLYTECHNIC",
                "beneficiaryAccount" => $this->sterling_bank_main,
                "bankCode" => "232",
                "beneficiaryAmount" => $calculation['serviceCharge'],
                "deductFeeFrom" => 0
            ],
            [
                "lineItemsId" => 'SIWES',
                "beneficiaryName" => "STUDENT INDUSTRIAL WORK EXPERIENCE",
                "beneficiaryAccount" => $this->union_SIWES,
                "bankCode" => "032",
                "beneficiaryAmount" => $siwesTotal,
                "deductFeeFrom" => 1
            ]
        ];


        $response = $this->makeRemitaApiCall($postFields);

        $this->handleRemitaResponse($response, $orderId);

        $rrr = trim($response['RRR']);

        // Check if RRR is already generated
        $this->checkDuplicateRRR($rrr);

        $sess = ""; // current session

        // Insert SIWES fee data
        $feedata = $this->createTransactionData($siwesFees, $orderId, $sess, $siwesFeeAmount, $fullNames, $rrr);
        if (STransaction::insert($feedata)) {
            return $rrr;
        } else {
            return false;
        }
    }

    private function processOtherFeesTwo($feesId, $calculation, $fullNames, $orderId)
    {
        $oFees = OFee::find($feesId);
        $feeAmount = $oFees->of_amount;

        $postFields = $this->createPostFields($this->service_type_id_NEW_SCHOOL_FEES, $feeAmount, $orderId, $fullNames, "Portal Other Fees");
        $response = $this->makeRemitaApiCall($postFields);

        $this->handleRemitaResponse($response, $orderId);

        $rrr = trim($response['RRR']);

        // Check if RRR is already generated
        $this->checkDuplicateRRR($rrr);

        $sess = ""; // current session

        // Insert fee data
        $feedata = $this->createTransactionData($oFees, $orderId, $sess, $feeAmount, $fullNames, $rrr);
        if (STransaction::insert($feedata)) {
            return $rrr;
        } else {
            return false;
        }
    }

    private function generateOrderId()
    {
        $timesammp = date("dmyHis");
        return session()->get('log_id') . $timesammp;
    }

    private function getFullNames()
    {
        return $this->student->surname . " " . $this->student->firstname . " " . $this->student->othernames;
    }

    public function processFees($rrr)
    {
        $responseurl = url('/remitaresponses');
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
                'pay_status'  => "Paid",
                'trans_date' => $payment_date,
                't_date' => $pt_date,
            ];

            STransaction::where('rrr', $rrr)
                ->where('trans_no', $orderid)
                ->update($udata);

            // Success message and redirection
            $rrr = htmlspecialchars($rrr, ENT_QUOTES);
            $message = "Transaction is Successful, Your RRR is $rrr. Kindly print your receipt. This transaction will be subject to verification by the Bursary Unit";
            $redirectUrl = url('/pfhistory');
            self::redirectWithAlert($message, $redirectUrl);
        } else {

            $rrr = htmlspecialchars($rrr, ENT_QUOTES);
            $orderid = htmlspecialchars($orderid, ENT_QUOTES);
            $message = "Transaction is Pending, Your RRR is $rrr. Kindly requery your transaction if debited or try again";
            $redirectUrl = url('/paymentslip/' . $orderid);
            self::redirectWithAlert($message, $redirectUrl);
        }
    }

    public function testrrr()
    {
        $rrr = "251256441106";
        $hash_string = $rrr .  $this->apiKey . $this->merchantId;
        $apiHash = hash('sha512', $hash_string);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->checkStatusUrl . "/$this->merchantId/$rrr/$apiHash/status.reg",
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

        print_r($result);
        exit;
    }

    public function checkpayment()
    {
        $transids = STransaction::getPendingTransactions($this->student->std_logid);

        if ($transids->isEmpty()) {
            return redirect('/pfhistory')->with('error', 'No pending transactions found.');
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


                if ($result->message == "Successful" && $result->status == "00") {
                    $payment_date = date('Y-m-d H:i:s');
                    $pt_date = date('Y-m-d', strtotime($payment_date));

                    $udata = [
                        'pay_status'  => 'Paid',
                        'trans_date' => $payment_date,
                        't_date' => $pt_date,
                    ];

                    STransaction::where('rrr', $result->rrr)
                        ->where('trans_no', $result->orderId)
                        ->update($udata);

                    $message = "Transactions is successful, you will be redirected to payment history";
                    $redirectUrl = url('pfhistory/');
                    self::redirectWithAlert($message, $redirectUrl);
                }
            }

            $message = "Transactions have been reprocessed, you will be redirected to payment history";
            $redirectUrl = url('pfhistory/');
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

    public function savefees()
    {
        $feeService = new FeeService($this->student);
        $fees = $feeService->getStudentFees()->toArray();

        $feesToPay = $feeService->getStudentCompulsoryAndRemainingFees($fees);

        if (empty($feesToPay)) {
            $message = "Fees already Paid, Proceed to print your receipt";
            $redirectUrl = url('/pfhistory/');
            self::redirectWithAlert($message, $redirectUrl);
        }

        $compulsoryFees = array_filter($feesToPay, fn($item) => $item->group == 1);

        $SUG = array_filter($compulsoryFees, fn($item) => $item->field_id === 2);
        if (!empty($SUG)) {
            $sugAmount = reset($SUG);
            //   echo "The amount for SUG is: " . $sugAmount->amount;
        } else {
            $sugAmount = 0;
        }

        $NADESSU = array_filter($compulsoryFees, fn($item) => $item->field_id === 5);
        if (!empty($NADESSU)) {
            $nadessuAmount = reset($NADESSU);
            //  echo "The amount for NADESSU is: " . $nadessuAmount->amount;
        } else {
            $nadessuAmount = 0;
        }

        $NIPOGA = array_filter($compulsoryFees, fn($item) => $item->field_id === 7);
        if (!empty($NIPOGA)) {
            $nipogaAmount = reset($NIPOGA);
            //     echo "The amount for NIPOGA is: " . $nipogaAmount->amount;
        } else {
            $nipogaAmount = 0;
        }

        $SUG_NIPOGA_ServiceCharge = array_filter($compulsoryFees, fn($item) => $item->field_id === 8);
        if (!empty($SUG_NIPOGA_ServiceCharge)) {
            $serviceChargeAmount = reset($SUG_NIPOGA_ServiceCharge);
            //     echo "The amount for SUG_NIPOGA_ServiceCharge is: " . $serviceChargeAmount->amount;
        } else {
            $serviceChargeAmount = 0;
        }



        $fullNames = $this->student->surname . " " . $this->student->firstname . " " . $this->student->othernames;

        $timesammp = DATE("dmyHis");
        $orderId = session()->get('log_id') . $timesammp;

        if (!empty($compulsoryFees)) {
            //split for compulsory fees

            $totalAmt = array_reduce($compulsoryFees, fn($carry, $item) => $carry + $item->amount, 0);

            $postFields = array(
                "serviceTypeId" => $this->service_type_id_SUG_NADESSTU_GNS_NIPOGA,
                "amount" => $totalAmt,
                "orderId" => $orderId,
                "payerName" => $fullNames,
                "payerEmail" => $this->student->student_email,
                "payerPhone" => $this->student->student_mobiletel,
                "description" => "Compulsory Fee",
                "lineItems" => [
                    [
                        "lineItemsId" => 'SUG',
                        "beneficiaryName" => "STUDENTS UNION GOVERNMENT",
                        "beneficiaryAccount" => $this->zenith_bank_SUG,
                        "bankCode" => "057",
                        "beneficiaryAmount" => $sugAmount->amount,
                        "deductFeeFrom" => 0
                    ],
                    [
                        "lineItemsId" => 'NADESSU',
                        "beneficiaryName" => "NATIONAL ASSOC OF STATE STUDENTS",
                        "beneficiaryAccount" => $this->union_bank_NADESSTU,
                        "bankCode" => "032",
                        "beneficiaryAmount" => $nadessuAmount->amount,
                        "deductFeeFrom" => 0
                    ],
                    [
                        "lineItemsId" => 'NIPOGA',
                        "beneficiaryName" => "NIPOGA 2025",
                        "beneficiaryAccount" => $this->premium_trust_bank_NIPOGA,
                        "bankCode" => "105",
                        "beneficiaryAmount" => $nipogaAmount->amount,
                        "deductFeeFrom" => 1
                    ],
                    [
                        "lineItemsId" => 'PORTAL_CHARGES',
                        "beneficiaryName" => "STATE POLYTECHNIC",
                        "beneficiaryAccount" => $this->first_bank_main,
                        "bankCode" => "011",
                        "beneficiaryAmount" => $serviceChargeAmount->amount,
                        "deductFeeFrom" => 0
                    ],
                ]
            );

            $response = $this->makeRemitaApiCall($postFields);

            $this->handleRemitaResponse($response, $orderId);

            $rrr = trim($response['RRR']);

            $this->checkDuplicateRRR($rrr);
            $sess = ""; // current session

            foreach ($compulsoryFees as $fee) {
                $feedata[] = $this->createTransactionData($fee, $orderId, $sess, $fee->amount, $fullNames, $rrr, "fees");
            }


            if (empty($feedata)) {
                return redirect('/fees')->with('error', 'Error generating fees.');
            }

            if (STransaction::insert($feedata)) {
                $message = "Transaction ID generated successfully, You will be redirected to make payment";
                $redirectUrl = url('/viewschfee/' . $rrr);
                self::redirectWithAlert($message, $redirectUrl);
            } else {
                return redirect('/fees')->with('error', 'Error generating fees.');
            }
        } else {
            //school fees
            $schoolFees = array_filter($feesToPay, fn($item) => $item->group == 0);
            if (empty($schoolFees)) {
                return redirect('/fees')->with('error', 'Error generating fees.');
            }
            $feeAmount = $schoolFees[0]->amount;

            $postFields = array(
                "serviceTypeId" => $this->service_type_id_SCHOOL_FEES,
                "amount" => $feeAmount,
                "orderId" => $orderId,
                "payerName" => $fullNames,
                "payerEmail" => $this->student->student_email,
                "payerPhone" => $this->student->student_mobiletel,
                "description" => "School Fee",
            );

            if ($this->student->stdlevel == 1 or $this->student->stdlevel == 3) {
                $school_share = $feeAmount - $this->ms_academy_split;
                $postFields["lineItems"] = [
                    [
                        "lineItemsId" => 'SCHOOL_FEES',
                        "beneficiaryName" => "STATE POLYTECHNIC",
                        "beneficiaryAccount" => $this->first_bank_main,
                        "bankCode" => "011",
                        "beneficiaryAmount" => $school_share,
                        "deductFeeFrom" => 0
                    ],
                    [
                        "lineItemsId" => 'MS_ACADEMY',
                        "beneficiaryName" => "INTERKEL TECHNOLOGIES LTD",
                        "beneficiaryAccount" => $this->fidelity_MICROSOFT_ACADEMY,
                        "bankCode" => "070",
                        "beneficiaryAmount" => $this->ms_academy_split,
                        "deductFeeFrom" => 1
                    ]
                ];
            }


            $response = $this->makeRemitaApiCall($postFields);

            $this->handleRemitaResponse($response, $orderId);

            $rrr = trim($response['RRR']);

            $sess = ""; // current session

            foreach ($schoolFees as $fee) {
                $feedata[] = $this->createTransactionData($fee, $orderId, $sess, $fee->amount, $fullNames, $rrr, "fees");
            }
            if (empty($feedata)) {
                return redirect('/fees')->with('error', 'Error generating fees.');
            }

            if (STransaction::insert($feedata)) {
                $message = "Transaction ID generated successfully, You will be redirected to make payment";
                $redirectUrl = url('/viewschfee/' . $rrr);
                self::redirectWithAlert($message, $redirectUrl);
            } else {
                return redirect('/fees')->with('error', 'Error generating fees.');
            }
        }
    }

    public function savesfees()
    {
        $feeService = new FeeService($this->student);
        $allfees = $feeService->getStudentPreviousFees()->toArray();

        $feeAmount = $feeService->getStudentFeeExclusion();

        if ($feeAmount != 0 || $feeAmount == -1) {
            $message = "You have not been enabled to pay this fee";
            $redirectUrl = url('/sfees');
            self::redirectWithAlert($message, $redirectUrl);
            exit;
        }

        $fullNames = $this->student->surname . " " . $this->student->firstname . " " . $this->student->othernames;

        $timesammp = DATE("dmyHis");
        $orderId = session()->get('log_id') . $timesammp;

        //school fees
        $schoolFees = array_filter($allfees, fn($item) => $item->group == 0);
        if (empty($schoolFees)) {
            return redirect('/fees')->with('error', 'Error generating fees.');
        }
        $feeAmount = $schoolFees[0]->amount;

        $postFields = array(
            "serviceTypeId" => $this->service_type_id_SCHOOL_FEES,
            "amount" => $feeAmount,
            "orderId" => $orderId,
            "payerName" => $fullNames,
            "payerEmail" => $this->student->student_email,
            "payerPhone" => $this->student->student_mobiletel,
            "description" => "School Fee",
        );

        if ($this->student->stdlevel == 1 or $this->student->stdlevel == 3) {
            $school_share = $feeAmount - $this->ms_academy_split;
            $postFields["lineItems"] = [
                [
                    "lineItemsId" => 'SCHOOL_FEES',
                    "beneficiaryName" => "STATE POLYTECHNIC",
                    "beneficiaryAccount" => $this->first_bank_main,
                    "bankCode" => "011",
                    "beneficiaryAmount" => $school_share,
                    "deductFeeFrom" => 0
                ],
                [
                    "lineItemsId" => 'MS_ACADEMY',
                    "beneficiaryName" => "INTERKEL TECHNOLOGIES LTD",
                    "beneficiaryAccount" => $this->fidelity_MICROSOFT_ACADEMY,
                    "bankCode" => "070",
                    "beneficiaryAmount" => $this->ms_academy_split,
                    "deductFeeFrom" => 1
                ]
            ];
        }


        $response = $this->makeRemitaApiCall($postFields);

        $this->handleRemitaResponse($response, $orderId);

        $rrr = trim($response['RRR']);

        $sess = "2023"; // current session

        foreach ($schoolFees as $fee) {
            $feedata[] = $this->createTransactionData($fee, $orderId, $sess, $fee->amount, $fullNames, $rrr, "fees");
        }
        if (empty($feedata)) {
            return redirect('/fees')->with('error', 'Error generating fees.');
        }

        if (STransaction::insert($feedata)) {
            $message = "Transaction ID generated successfully, You will be redirected to make payment";
            $redirectUrl = url('/viewschfee/' . $rrr);
            self::redirectWithAlert($message, $redirectUrl);
        } else {
            return redirect('/fees')->with('error', 'Error generating fees.');
        }
    }

    public function savebfees()
    {
        $feeService = new FeeService($this->student);
        $allfees = $feeService->getStudentFees()->toArray();

        $fullNames = $this->student->surname . " " . $this->student->firstname . " " . $this->student->othernames;

        $timesammp = DATE("dmyHis");
        $orderId = session()->get('log_id') . $timesammp;

        //school fees
        $schoolFees = array_filter($allfees, fn($item) => $item->group == 0);
        if (empty($schoolFees)) {
            return redirect('/fees')->with('error', 'Error generating fees.');
        }

        $feeAmount = $feeService->getStudentFeeExclusion();

        if ($feeAmount == 0) {
            $message = "You have not been enabled to pay this fee";
            $redirectUrl = url('/bfees');
            self::redirectWithAlert($message, $redirectUrl);
            exit;
        }


        $postFields = array(
            "serviceTypeId" => $this->service_type_id_SCHOOL_FEES,
            "amount" => $feeAmount,
            "orderId" => $orderId,
            "payerName" => $fullNames,
            "payerEmail" => $this->student->student_email,
            "payerPhone" => $this->student->student_mobiletel,
            "description" => "School Fee",
        );

        $response = $this->makeRemitaApiCall($postFields);

        $this->handleRemitaResponse($response, $orderId);

        $rrr = trim($response['RRR']);
        $sess = "2023"; // current session

        foreach ($schoolFees as $fee) {
            $feedata[] = $this->createTransactionData($fee, $orderId, $sess, $feeAmount, $fullNames, $rrr, "fees");
        }
        if (empty($feedata)) {
            return redirect('/fees')->with('error', 'Error generating fees.');
        }

        if (STransaction::insert($feedata)) {
            $message = "Transaction ID generated successfully, You will be redirected to make payment";
            $redirectUrl = url('/viewschfee/' . $rrr);
            self::redirectWithAlert($message, $redirectUrl);
        } else {
            return redirect('/fees')->with('error', 'Error generating fees.');
        }
    }

    function payHostelFee(Request $request, $hostel_id, $of_id)
    {
        $request->merge([
            'hostel_id' => $hostel_id,
            'of_id' => $of_id,
        ]);

        $request->validate([
            'hostel_id' => 'required|integer',
            'of_id' => 'required|integer',
        ]);

        $hostelHasAvailableRooms = Hostel::where('hid', $hostel_id)
            ->whereHas('rooms', function ($query) {
                $query->where('room_status', 1)
                    ->whereDoesntHave('allocations', function ($subquery) {
                        $subquery->select('room_id')
                            ->from('hostelroom_allocations')
                            ->groupBy('room_id')
                            ->havingRaw('COUNT(*) < MAX(capacity)');
                    });
            })
            ->with('ofee')
            ->get();

        if ($hostelHasAvailableRooms->isEmpty()) {
            return redirect()->back()->with('error', 'Hostel Rooms are fully occupied');
        }

        $selectedFees = [$of_id];
        $libraryBindingCopies = $request->input('copies', 0);
        $calculation = $this->feeCalculationService->calculateFees($selectedFees, $libraryBindingCopies);

        $fullNames = $this->getFullNames();
        $orderId = $this->generateOrderId();
        $siwesFeesId = 9;

        // Process other selected fees
        $otherselectedFees = array_filter($selectedFees, function ($value) use ($siwesFeesId) {
            return $value != $siwesFeesId;
        });

        if (!empty($otherselectedFees)) {
            $ofees = OFee::whereIn('of_id', $otherselectedFees)->get();
            $rrr = $this->processOtherFees($ofees, $calculation, $fullNames, $orderId, $libraryBindingCopies, $hostel_id);
        }

        if ($rrr) {
            $message = "Transaction ID generated successfully, You will be redirected to make payment";
            $redirectUrl = url('/viewofee/' . $rrr);
            self::redirectWithAlert($message, $redirectUrl);
        } else {
            return redirect('/hostels')->with('error', 'Error generating fees.');
        }
    }

    public function savepfees(Request $request)
    {
        if (!$request->has('psess')) {
            return back()->withErrors('Select any least a Session to make payment');
        }
        $psess = $request->psess;

        $feeService = new FeeService($this->student);

        $fees = $feeService->getStudentPreviousFees()->toArray();
        $feesToPay  = $feeService->getStudentPreviousFeesToPay($fees, $psess);

        if (empty($feesToPay)) {
            $message = "Fees already Paid, Proceed to print your receipt";
            $redirectUrl = url('/pfhistory/');
            self::redirectWithAlert($message, $redirectUrl);
        }

        $compulsoryFees = array_filter($feesToPay, fn($item) => $item->group == 1);

        $SUG = array_filter($compulsoryFees, fn($item) => $item->field_id === 2);
        if (!empty($SUG)) {
            $sugAmount = reset($SUG);
        } else {
            $sugAmount = 0;
        }

        $NADESSU = array_filter($compulsoryFees, fn($item) => $item->field_id === 5);
        if (!empty($NADESSU)) {
            $nadessuAmount = reset($NADESSU);
        } else {
            $nadessuAmount = 0;
        }

        $NIPOGA = array_filter($compulsoryFees, fn($item) => $item->field_id === 7);
        if (!empty($NIPOGA)) {
            $nipogaAmount = reset($NIPOGA);
        } else {
            $nipogaAmount = 0;
        }

        $SUG_NIPOGA_ServiceCharge = array_filter($compulsoryFees, fn($item) => $item->field_id === 8);
        if (!empty($SUG_NIPOGA_ServiceCharge)) {
            $serviceChargeAmount = reset($SUG_NIPOGA_ServiceCharge);
        } else {
            $serviceChargeAmount = 0;
        }

        $fullNames = $this->student->surname . " " . $this->student->firstname . " " . $this->student->othernames;

        $timesammp = DATE("dmyHis");
        $orderId = session()->get('log_id') . $timesammp;

        if (!empty($compulsoryFees)) {
            //split for compulsory fees

            $totalAmt = array_reduce($compulsoryFees, fn($carry, $item) => $carry + $item->amount, 0);

            $postFields = array(
                "serviceTypeId" => $this->service_type_id_SUG_NADESSTU_GNS_NIPOGA,
                "amount" => $totalAmt,
                "orderId" => $orderId,
                "payerName" => $fullNames,
                "payerEmail" => $this->student->student_email,
                "payerPhone" => $this->student->student_mobiletel,
                "description" => "Compulsory Fee",
                "lineItems" => [
                    [
                        "lineItemsId" => 'SUG',
                        "beneficiaryName" => "STUDENTS UNION GOVERNMENT",
                        "beneficiaryAccount" => $this->zenith_bank_SUG,
                        "bankCode" => "057",
                        "beneficiaryAmount" => $sugAmount->amount,
                        "deductFeeFrom" => 0
                    ],
                    [
                        "lineItemsId" => 'NADESSU',
                        "beneficiaryName" => "NATIONAL ASSOC OF STATE STUDENTS",
                        "beneficiaryAccount" => $this->union_bank_NADESSTU,
                        "bankCode" => "032",
                        "beneficiaryAmount" => $nadessuAmount->amount,
                        "deductFeeFrom" => 0
                    ],
                    [
                        "lineItemsId" => 'NIPOGA',
                        "beneficiaryName" => "NIPOGA 2025",
                        "beneficiaryAccount" => $this->premium_trust_bank_NIPOGA,
                        "bankCode" => "105",
                        "beneficiaryAmount" => $nipogaAmount->amount,
                        "deductFeeFrom" => 1
                    ],
                    [
                        "lineItemsId" => 'PORTAL_CHARGES',
                        "beneficiaryName" => "STATE POLYTECHNIC",
                        "beneficiaryAccount" => $this->first_bank_main,
                        "bankCode" => "011",
                        "beneficiaryAmount" => $serviceChargeAmount->amount,
                        "deductFeeFrom" => 0
                    ],
                ]
            );

            $response = $this->makeRemitaApiCall($postFields);

            $this->handleRemitaResponse($response, $orderId);

            $rrr = trim($response['RRR']);

            $this->checkDuplicateRRR($rrr);

            foreach ($compulsoryFees as $fee) {
                $feedata[] = $this->createTransactionData($fee, $orderId, $psess,  $fee->amount, $fullNames, $rrr, "fees");
            }


            if (empty($feedata)) {
                return redirect('/fees')->with('error', 'Error generating fees.');
            }

            if (STransaction::insert($feedata)) {
                $message = "Transaction ID generated successfully, You will be redirected to make payment";
                $redirectUrl = url('/viewschfee/' . $rrr);
                self::redirectWithAlert($message, $redirectUrl);
            } else {
                return redirect('/fees')->with('error', 'Error generating fees.');
            }
        } else {
            //school fees
            $schoolFees = array_filter($feesToPay, fn($item) => $item->group == 0);
            if (empty($schoolFees)) {
                return redirect('/fees')->with('error', 'Error generating fees.');
            }
            $feeAmount = $schoolFees[0]->amount;

            $postFields = array(
                "serviceTypeId" => $this->service_type_id_SCHOOL_FEES,
                "amount" => $feeAmount,
                "orderId" => $orderId,
                "payerName" => $fullNames,
                "payerEmail" => $this->student->student_email,
                "payerPhone" => $this->student->student_mobiletel,
                "description" => "School Fee",
            );

            if ($this->student->stdlevel == 1 or $this->student->stdlevel == 3) {
                $school_share = $feeAmount - $this->ms_academy_split;
                $postFields["lineItems"] = [
                    [
                        "lineItemsId" => 'SCHOOL_FEES',
                        "beneficiaryName" => "STATE POLYTECHNIC",
                        "beneficiaryAccount" => $this->first_bank_main,
                        "bankCode" => "011",
                        "beneficiaryAmount" => $school_share,
                        "deductFeeFrom" => 0
                    ],
                    [
                        "lineItemsId" => 'MS_ACADEMY',
                        "beneficiaryName" => "INTERKEL TECHNOLOGIES LTD",
                        "beneficiaryAccount" => $this->fidelity_MICROSOFT_ACADEMY,
                        "bankCode" => "070",
                        "beneficiaryAmount" => $this->ms_academy_split,
                        "deductFeeFrom" => 1
                    ]
                ];
            }


            $response = $this->makeRemitaApiCall($postFields);

            $this->handleRemitaResponse($response, $orderId);

            $rrr = trim($response['RRR']);

            foreach ($schoolFees as $fee) {
                $feedata[] = $this->createTransactionData($fee, $orderId, $psess, $fee->amount, $fullNames, $rrr, "fees");
            }
            if (empty($feedata)) {
                return redirect('/bpfee')->with('error', 'Error generating fees.');
            }

            if (STransaction::insert($feedata)) {
                $message = "Transaction ID generated successfully, You will be redirected to make payment";
                $redirectUrl = url('/viewschfee/' . $rrr);
                self::redirectWithAlert($message, $redirectUrl);
            } else {
                return redirect('/bpfee')->with('error', 'Error generating fees.');
            }
        }
    }

    public function savenewfees(Request $request)
    {
        $feeService = new FeeService($this->student);
        $fees = $feeService->getStudentFees()->toArray();

        $feesToPay = $feeService->getStudentCompulsoryAndRemainingFees($fees);

        if (empty($feesToPay)) {
            $checkSchoolFeesCompleted = $feeService->checkSchoolFeesCompletePaid();
            if ($checkSchoolFeesCompleted) {
                $message = "Fees already Paid, Proceed to print your receiptddd";
                $redirectUrl = url('/pfhistory/');
                self::redirectWithAlert($message, $redirectUrl);
            }
            $feesToPay = $feeService->getStudentBalanceFees($fees);
        }

        $compulsoryFees = array_filter($feesToPay, fn($item) => $item->group == 1);

        $SUG = array_filter($compulsoryFees, fn($item) => $item->field_id === 2);
        if (!empty($SUG)) {
            $sugAmount = reset($SUG);
            //   echo "The amount for SUG is: " . $sugAmount->amount;
        } else {
            $sugAmount = 0;
        }

        $NADESSU = array_filter($compulsoryFees, fn($item) => $item->field_id === 5);
        if (!empty($NADESSU)) {
            $nadessuAmount = reset($NADESSU);
            //  echo "The amount for NADESSU is: " . $nadessuAmount->amount;
        } else {
            $nadessuAmount = 0;
        }

        $NIPOGA = array_filter($compulsoryFees, fn($item) => $item->field_id === 7);
        if (!empty($NIPOGA)) {
            $nipogaAmount = reset($NIPOGA);
            //     echo "The amount for NIPOGA is: " . $nipogaAmount->amount;
        } else {
            $nipogaAmount = 0;
        }

        $SUG_NIPOGA_ServiceCharge = array_filter($compulsoryFees, fn($item) => $item->field_id === 8);
        if (!empty($SUG_NIPOGA_ServiceCharge)) {
            $serviceChargeAmount = reset($SUG_NIPOGA_ServiceCharge);
            //     echo "The amount for SUG_NIPOGA_ServiceCharge is: " . $serviceChargeAmount->amount;
        } else {
            $serviceChargeAmount = 0;
        }



        $fullNames = $this->student->surname . " " . $this->student->firstname . " " . $this->student->othernames;

        $timesammp = DATE("dmyHis");
        $orderId = session()->get('log_id') . $timesammp;

        if (!empty($compulsoryFees)) {
            //split for compulsory fees

            $totalAmt = array_reduce($compulsoryFees, fn($carry, $item) => $carry + $item->amount, 0);

            $postFields = array(
                "serviceTypeId" => $this->service_type_id_SUG_NADESSTU_GNS_NIPOGA,
                "amount" => $totalAmt,
                "orderId" => $orderId,
                "payerName" => $fullNames,
                "payerEmail" => $this->student->student_email,
                "payerPhone" => $this->student->student_mobiletel,
                "description" => "Compulsory Fee",
                "lineItems" => [
                    [
                        "lineItemsId" => 'SUG',
                        "beneficiaryName" => "STUDENTS UNION GOVERNMENT",
                        "beneficiaryAccount" => $this->zenith_bank_SUG,
                        "bankCode" => "057",
                        "beneficiaryAmount" => $sugAmount->amount,
                        "deductFeeFrom" => 0
                    ],
                    [
                        "lineItemsId" => 'NADESSU',
                        "beneficiaryName" => "NATIONAL ASSOC OF STATE STUDENTS",
                        "beneficiaryAccount" => $this->union_bank_NADESSTU,
                        "bankCode" => "032",
                        "beneficiaryAmount" => $nadessuAmount->amount,
                        "deductFeeFrom" => 0
                    ],
                    [
                        "lineItemsId" => 'SPORTS DEVELOPMENT',
                        "beneficiaryName" => "NIPOGA 2025",
                        "beneficiaryAccount" => $this->premium_trust_bank_NIPOGA,
                        "bankCode" => "105",
                        "beneficiaryAmount" => $nipogaAmount->amount,
                        "deductFeeFrom" => 1
                    ],
                    [
                        "lineItemsId" => 'PORTAL_CHARGES',
                        "beneficiaryName" => "POLYTECHNIC",
                        "beneficiaryAccount" => $this->sterling_bank_main,
                        "bankCode" => "232",
                        "beneficiaryAmount" => $serviceChargeAmount->amount,
                        "deductFeeFrom" => 0
                    ],
                ]
            );

            $response = $this->makeRemitaApiCall($postFields);

            $this->handleRemitaResponse($response, $orderId);

            $rrr = trim($response['RRR']);

            $this->checkDuplicateRRR($rrr);
            $sess = ""; // current session

            foreach ($compulsoryFees as $fee) {
                $feedata[] = $this->createTransactionData($fee, $orderId, $sess, $fee->amount, $fullNames, $rrr, "fees");
            }


            if (empty($feedata)) {
                return redirect('/fees')->with('error', 'Error generating fees.');
            }

            if (STransaction::insert($feedata)) {
                $message = "Transaction ID generated successfully, You will be redirected to make payment";
                $redirectUrl = url('/viewschfee/' . $rrr);
                self::redirectWithAlert($message, $redirectUrl);
            } else {
                return redirect('/fees')->with('error', 'Error generating fees.');
            }
        } else {
            //school fees
            $schoolFees = array_filter($feesToPay, fn($item) => $item->group == 0);
            if (empty($schoolFees)) {
                return redirect('/fees')->with('error', 'Error generating fees.');
            }
            $feeAmount = $schoolFees[0]->amount;
            $policy = $request->input('policy', 0);
            $totalAmount = 0;
            $postFields = array(
                "serviceTypeId" => $this->service_type_id_NEW_SCHOOL_FEES,
                "amount" => $feeAmount,
                "orderId" => $orderId,
                "payerName" => $fullNames,
                "payerEmail" => $this->student->student_email,
                "payerPhone" => $this->student->student_mobiletel,
                "description" => "School Fee",
            );

            // get splits
            $sessSem = STransaction::getCurrentSemesterSession($this->student->stdprogramme_id);
            $residentStatus = $this->student->state_of_origin == 10 ? 'resident' : 'non-resident';
            $feesSplit = $feeService->getFeesSplit(
                $this->student->stdlevel,
                $this->student->stdprogrammetype_id,
                $residentStatus,
                $sessSem['cs_session']
            );

            $medicalsAmount = $feesSplit->first(function ($item) {
                return $item->field_name === 'Medicals';
            })->amount;


            $othersAmount = $feesSplit->first(function ($item) {
                return $item->field_name === 'Others';
            })->amount;
            $postFields["lineItems"] = [];

            if ($policy != 0.6) {
                $postFields["lineItems"][] = [
                    "lineItemsId" => 'OTHERS',
                    "beneficiaryName" => "STATE POLYTECHNIC",
                    "beneficiaryAccount" => $this->first_bank_main,
                    "bankCode" => "011",
                    "beneficiaryAmount" => $othersAmount,
                    "deductFeeFrom" => ($policy == 0.4 || $policy == 0) ? 1 : 0,
                ];
            }

            $mSAmount = 0;


            if (($this->student->stdlevel == 1 or $this->student->stdlevel == 3) and $policy != 0.4) {
                $postFields["lineItems"][] = [
                    "lineItemsId" => 'MS_ACADEMY',
                    "beneficiaryName" => "INTERKEL TECHNOLOGIES LTD",
                    "beneficiaryAccount" => $this->fidelity_MICROSOFT_ACADEMY,
                    "bankCode" => "070",
                    "beneficiaryAmount" => $this->ms_academy_split,
                    "deductFeeFrom" => 0
                ];
                $mSAmount = $this->ms_academy_split;
            }

            $school_share = $feeAmount - $mSAmount - $medicalsAmount - $othersAmount;
            if ($policy != 0.4) {
                $postFields["lineItems"] = array_merge($postFields["lineItems"], [
                    [
                        "lineItemsId" => 'SCHOOL_FEES',
                        "beneficiaryName" => "STATE POLYTECHNIC",
                        "beneficiaryAccount" => $this->sterling_bank_main,
                        "bankCode" => "232",
                        "beneficiaryAmount" => $school_share,
                        "deductFeeFrom" =>  $policy == 0.6 ? 1 : 0
                    ],
                    [
                        "lineItemsId" => 'MEDICALS',
                        "beneficiaryName" => "STATE POLYTECHNIC MEDICAL CENTRE",
                        "beneficiaryAccount" => $this->premium_trust_MEDICALS,
                        "bankCode" => "105",
                        "beneficiaryAmount" => $medicalsAmount,
                        "deductFeeFrom" => 0
                    ],

                ]);
            }

            $totalAmount = array_sum(array_column($postFields['lineItems'], 'beneficiaryAmount'));

            // Replace the amount
            $postFields['amount'] = $totalAmount;
            //  print_r($postFields);
            //  exit;
            $response = $this->makeRemitaApiCall($postFields);

            $this->handleRemitaResponse($response, $orderId);

            $rrr = trim($response['RRR']);

            $sess = ""; // current session

            foreach ($schoolFees as $fee) {
                $feedata[] = $this->createTransactionData($fee, $orderId, $sess, $totalAmount, $fullNames, $rrr, "fees", $policy);
            }
            if (empty($feedata)) {
                return redirect('/fees')->with('error', 'Error generating fees.');
            }

            if (STransaction::insert($feedata)) {
                $message = "Transaction ID generated successfully, You will be redirected to make payment";
                $redirectUrl = url('/viewschfee/' . $rrr);
                self::redirectWithAlert($message, $redirectUrl);
            } else {
                return redirect('/fees')->with('error', 'Error generating fees.');
            }
        }
    }
}
