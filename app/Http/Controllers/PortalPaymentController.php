<?php

namespace App\Http\Controllers;

use App\Models\OFee;
use App\Models\STransaction;
use App\Services\FeeService;
use Illuminate\Http\Request;
use App\Traits\ValidatesPortalUser;

class PortalPaymentController extends Controller
{
    use ValidatesPortalUser;

    public const PAYMENT_METHOD = 'PayStack';
    protected $ps_sk,
        $subAccount,
        $transactionFee,
        $accomodation,
        $portalFee,
        $callback_url,
        $cancel_action_url;



    public function __construct()
    {

        $this->middleware(function ($request, $next) {
            $response = $this->validatePortalUser();
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }
            return $next($request);
        });

        // PayStack
        $paymentConfig = app('paystackPayment.config');
        $this->ps_sk = $paymentConfig['ps_SK'];
        $this->subAccount = $paymentConfig['subAccount'];
        $this->transactionFee = $paymentConfig['transactionFee'];
        $this->portalFee = $paymentConfig['portalFee'];
        $this->callback_url = $paymentConfig['callback_url'];
        $this->cancel_action_url = $paymentConfig['cancel_action_url'];
    }

    public function savenewfees(Request $request)
    {
        $feeService = new FeeService($this->student);
        $fees = $feeService->getStudentFees('all');

        $feesToPay = $feeService->getStudentCompulsoryAndRemainingFees($fees, $request->policy);

        if (empty($feesToPay)) {
            $message = "Fees already Paid For the selected Semester, Proceed to print your receipt";
            $redirectUrl = url('/pfhistory/');
            self::redirectWithAlert($message, $redirectUrl);
        }

        $fullNames = $this->student->surname . " " . $this->student->firstname . " " . $this->student->othernames;

        $timesammp = DATE("dmyHis");
        $orderId = $this->student->std_logid . $timesammp;

        if (!empty($feesToPay)) {
            //split for compulsory fees

            $totalAmt = array_reduce($feesToPay, fn($carry, $item) => $carry + $item->amount, 0);

            $serviceCharge =  $this->transactionFee;
            $semester = reset($feesToPay)->semester;
            if ($semester == 'First Semester') {
                $serviceCharge =  $this->transactionFee +  $this->portalFee;
            }

            $totalamount = $totalAmt + $serviceCharge;
            $schoolshare = $totalAmt;

            $response = $this->makePayStackApiCall($totalamount, $schoolshare, $orderId);

            $this->handlePayStackResponse($response, $orderId);


            $accessCode = trim($response['data']['access_code']);

            $this->checkDuplicateAccessCode($accessCode);
            $sess = ""; // current session


            foreach ($feesToPay as $fee) {
                $feedata[] = $this->createTransactionData($fee, $orderId, $semester, $sess, $fee->amount, $fullNames, $response, $request->policy, "fees");
            }

            if (empty($feedata)) {
                return redirect('/fees')->with('error', 'Error generating fees.');
            }

            if (STransaction::insert($feedata)) {
                $message = "Transaction ID generated successfully, You will be redirected to make payment";
                $redirectUrl = url('/viewschfee/' . $orderId);
                self::redirectWithAlert($message, $redirectUrl);
            } else {
                return redirect('/fees')->with('error', 'Error generating fees.');
            }
        } else {
            return redirect('/fees')->with('error', 'Error generating fees.');
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

    private function createTransactionData($fee, $orderId, $sem = "", $sess = "", $amount, $fullNames, $response, $policy, $feetype = "ofees")
    {
        $sessSem = STransaction::getCurrentSemesterSession($this->student->stdprogramme_id);
        $psess = !empty($sess) ? $sess : $sessSem['cs_session'];
        $psem = !empty($sem) ? $sem : $sessSem['cs_sem'];

        $policy = ($psem == 'First Semester') ? 1 : 2;
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
            'trans_semester' => $psem,
            'pay_status' => 'Pending',
            'policy' => $policy,
            't_date' => date('Y-m-d'),
            'fullnames' => $fullNames,
            'prog_id' => $this->student->stdprogramme_id,
            'prog_type' => $this->student->stdprogrammetype_id,
            'stdcourse' => $this->student->stdcourse,
            'appno' => $this->student->matric_no,
            'appsor' => $this->student->state_of_origin,
            'channel' => self::PAYMENT_METHOD,
            'fee_type' => $feetype,
            'rrr' => $response['data']['access_code'],
            'payref' => $response['data']['reference'],
        ];
    }

    private function checkDuplicateAccessCode($accessCode)
    {
        $checkAccessCode = STransaction::where(['trans_no' => $accessCode])->get();
        if (!$checkAccessCode->isEmpty()) {
            $message = "Access Code already generated. Please try again";
            $redirectUrl = url('/fees');
            self::redirectWithAlert($message, $redirectUrl);
        }
    }

    private function handlePayStackResponse($response)
    {
        if (empty($response) || $response['status'] != '1') {
            $message = "Error generating Payment details. Please try again";
            $redirectUrl = url('/fees');
            self::redirectWithAlert($message, $redirectUrl);
        }
    }

    private function makePayStackApiCall($totalamount, $schoolshare, $orderId)
    {
        $url = "https://api.paystack.co/transaction/initialize";
        $fields = [
            'email' => $this->student->student_email,
            'amount' => $totalamount * 100,
            'subaccount' => $this->subAccount,
            'reference' => $orderId,
            'transaction_charge' =>  $schoolshare * 100,
            'bearer' => "subaccount",
            'callback_url' => $this->callback_url,
            'metadata' => ["cancel_action" => $this->cancel_action_url]
        ];

        $fields_string = http_build_query($fields);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer $this->ps_sk",
            "Cache-Control: no-cache",
        ));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        $data = json_decode($result, true);

        //log on db
        $feeService = new FeeService($this->student);
        $feeService->insertJsonData(stripslashes(json_encode($data)), "Response");

        return $data;
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

    private function processOtherFees($ofees,  $fullNames, $orderId)
    {
        $orderId = $this->student->std_logid . date("dmyHis");

        $totalAmt = $ofees[0]->of_amount;
        $totalamount = $totalAmt + $this->transactionFee;
        $schoolshare = $totalAmt;
        $fullNames = $this->student->surname . " " . $this->student->firstname . " " . $this->student->othernames;

        $response = $this->makePayStackApiCall($totalamount, $schoolshare, $orderId);

        $this->handlePayStackResponse($response, $orderId);

        $accessCode = trim($response['data']['access_code']);

        $this->checkDuplicateAccessCode($accessCode);
        $sess = ""; // current session
        $sem = ""; // current semester

        foreach ($ofees as $fee) {
            $ofeedata = $this->createTransactionData($fee, $orderId, $sess, $sem, $totalAmt, $fullNames, $response, 0, "ofees");
        }
        if (!empty($ofeedata)) {
            STransaction::insert($ofeedata);
            return $orderId;
        } else {
            return false;
        }
    }

    private function generateOrderId()
    {
        $timesammp = date("dmyHis");
        return $this->student->std_logid . $timesammp;
    }

    private function getFullNames()
    {
        return $this->student->surname . " " . $this->student->firstname . " " . $this->student->othernames;
    }


    public function paystackresponse(Request $request)
    {
        // Retrieve query parameters
        $trxref = $request->trxref;
        $reference = $request->reference;

        $response = self::payStack_transaction_details($trxref);

        $status = $response['data']['status'];
        $reference = $response['data']['reference'];

        if ($status == "success") {
            $udata = [
                'pay_status'  => "Paid"
            ];

            STransaction::where('trans_no', $trxref)
                ->update($udata);

            // Success message and redirection
            $trxref = htmlspecialchars($trxref, ENT_QUOTES);
            $message = "Transaction is Successful, Your Payment Reference is $trxref. Kindly print your receipt. This transaction will be subject to verification by the Bursary Unit";
            $redirectUrl = url('/pfhistory');
            self::redirectWithAlert($message, $redirectUrl);
        } else {

            $trxref = htmlspecialchars($trxref, ENT_QUOTES);
            $message = "Transaction is Pending, Your Payment Reference is $trxref.  Kindly requery your transaction if debited or try again";
            $redirectUrl = url('/viewschfee/' . $reference);
            self::redirectWithAlert($message, $redirectUrl);
        }
    }

    public function paystackcancelaction()
    {
        $message = "Unable to complete payment, Try Again is Pending";
        $redirectUrl = url('/fees');
        self::redirectWithAlert($message, $redirectUrl);
    }

    public function checkpayment()
    {
        $transids = STransaction::getPendingTransactions($this->student->std_logid);

        if ($transids->isEmpty()) {
            return redirect('/pfhistory')->with('error', 'No pending transactions found.');
        } else {

            foreach ($transids as $transid) {

                //revalidate payment

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . $transid->trans_no,
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

                $response = curl_exec($curl);
                curl_close($curl);

                $data = json_decode($response, true);

                $status = $data['data']['status'];
                $reference = $data['data']['reference'];


                if ($status == "success") {
                    $udata = [
                        'pay_status'  => 'Paid'
                    ];

                    STransaction::where('trans_no', $reference)
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

    public function saveofees(Request $request)
    {
        if (!$request->has('ofee')) {
            return back()->withErrors('Select at least one Fee to make payment');
        }

        $selectedFees = $request->input('ofee');


        $fullNames = $this->getFullNames();
        $orderId = $this->generateOrderId();

        if (!empty($selectedFees)) {
            $ofees = OFee::whereIn('of_id', $selectedFees)->get();
            $transId = $this->processOtherFees($ofees,  $fullNames, $orderId);
        }

        if ($transId) {
            $message = "Transaction ID generated successfully, You will be redirected to make payment";
            $redirectUrl = url('/viewofee/' . $transId);
            self::redirectWithAlert($message, $redirectUrl);
        } else {
            return redirect('/ofees')->with('error', 'Error generating fees.');
        }
    }
}
