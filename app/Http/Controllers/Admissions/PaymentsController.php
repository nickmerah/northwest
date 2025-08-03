<?php

namespace App\Http\Controllers\Admissions;

use App\Helpers\ApplicationHelper;
use App\Services\Admissions\PaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Routing\Controller as BaseController;


class PaymentsController extends BaseController
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(): View
    {
        $defaultFeeId = 1;
        $data = $this->getApplicantData($defaultFeeId);
        $feeId = Crypt::encryptString($defaultFeeId);

        return view('admissions.applicants.einvoice', compact('data', 'feeId'));
    }

    public function paynow(Request $request): View
    {
        $feeId = Crypt::decryptString($request->input('feeId'));
        $response = $this->paymentService->makePayment($feeId);
        $paymentData = (object) $response['data']['data'];

        return view('admissions.applicants.epaymentslip', compact('paymentData'));
    }

    public function verifypayment()
    {
        $this->paymentService->verifyPayment();

        return $this->transactionhistory();
    }

    public function transactionhistory(?int $transactionId = null): View|array
    {
        $response = $this->paymentService->getPaymentHistory();
        $paymentDatas = [];
        if ($response['success']) {
            $paymentDatas = $response['data']['data'];
        }

        if ($transactionId) {
            $paymentDatas = array_values(array_filter($paymentDatas, function ($transaction) use ($transactionId) {
                return $transaction['transactionID'] == $transactionId;
            }));
            return $paymentDatas;
        }

        return view('admissions.applicants.epaymenthistory', compact('paymentDatas'));
    }

    public function paymentreceipt(int $transactionId)
    {

        $passportUrl = ApplicationHelper::getApplicantPassport();

        $paymentData =  $this->transactionhistory($transactionId);

        return view('admissions.applicants.receipt', compact('paymentData', 'passportUrl'));
    }


    private function getApplicantData(int $feeId): ?object
    {
        $data = Cache::get("dashboard:{session('user')['id']}");

        if ($data && isset($data->applicationFee)) {
            $data->applicationFee = array_values(array_filter($data->applicationFee, function ($fee) use ($feeId) {
                return isset($fee['feeId']) && $fee['feeId'] == $feeId;
            }));
        }

        return $data;
    }
}
