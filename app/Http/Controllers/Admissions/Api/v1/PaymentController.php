<?php

namespace App\Http\Controllers\Admissions\Api\v1;

use Exception;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Payment;
use App\Http\Requests\CheckPayment;
use App\Http\Controllers\Controller;
use App\Services\ApiResponseService;
use Illuminate\Http\RedirectResponse;
use App\Factories\PaymentGatewayFactory;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    protected $paymentfactory;
    protected $apiResponse;

    public function __construct(PaymentGatewayFactory $paymentfactory, ApiResponseService $apiResponse)
    {
        $this->paymentfactory = $paymentfactory;
        $this->apiResponse = $apiResponse;
    }

    public function paynow(Payment $request)
    {
        return $this->apiResponse->respond(
            function () use ($request) {
                $gateway = $this->paymentfactory->create($request['gateway']);
                $redirectUrl = $this->paymentfactory::sanitizeRedirectUrl($request['redirectUrl']);
                return $gateway->processPayment($request['feeType'], $redirectUrl);
            },
            successMessage: 'Payment details retrieved successfully.',
            errorMessage: 'Error generating transaction reference.',
            successStatus: Response::HTTP_OK,
            successCondition: fn($response) => $response && isset($response['generateStatus']),
            transform: function ($response) {
                $message = $response['generateStatus'] === false
                    ? 'Transaction ID already generated.'
                    : 'Payment details retrieved successfully.';
                return array_merge(['message' => $message], $response);
            }
        );
    }

    public function paymentresponse(Request $request)
    {
        // Retrieve query parameters for paystack
        $trxref = $request->input('trxref');

        $referenceMap = [
            'paystack' => $trxref,
            // Add more gateways here if needed
        ];

        $gatewayKey = collect($referenceMap)->filter()->keys()->first();

        $transactionReference = $referenceMap[$gatewayKey] ?? null;

        $errorMessage = "Unable to process payment, update payment from the portal.";

        abort_if(!$gatewayKey || !$transactionReference, Response::HTTP_BAD_REQUEST, $errorMessage);

        $gateway = $this->paymentfactory->create($gatewayKey);

        $transactionDetails = $gateway->retrieveTransactionDetails($transactionReference);

        if (empty($transactionDetails)) {
            return new RedirectResponse(url()->previous());
        }

        $updatetransaction = $gateway->updateTransaction($transactionDetails);

        if ($updatetransaction['paymentStatus'] != 'Paid') {
            return new RedirectResponse(url()->previous());
        }

        // Success, redirection
        return new RedirectResponse($updatetransaction['data']['redirectUrl']);
    }

    public function paystackcancelaction()
    {
        return new RedirectResponse(url()->previous());
    }

    public function checkpayment(CheckPayment $request)
    {
        return $this->apiResponse->respond(
            fn() => $this->paymentfactory->create($request['gateway'])->checkPayment($request['gateway']),
            successMessage: 'Checking of transactions status has been completed.',
            errorMessage: 'Error checking transactions status.',
            successStatus: Response::HTTP_OK,
            successCondition: fn($response) => !empty($response)
        );
    }

    public function getpaymentHistory()
    {
        return $this->apiResponse->respond(
            fn() => $this->paymentfactory::getallPaidTransactions(),
            successMessage: 'Payment history successfully retrieved.',
            errorMessage: 'Error getting transactions history.',
            successStatus: Response::HTTP_OK,
            successCondition: fn($response) => !empty($response)
        );
    }
}
