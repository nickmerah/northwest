<?php

namespace App\Http\Controllers\Admissions\Api\v1;

use Exception;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Payment;
use App\Http\Requests\CheckPayment;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Factories\PaymentGatewayFactory;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    protected $paymentfactory;

    public function __construct(PaymentGatewayFactory $paymentfactory)
    {
        $this->paymentfactory = $paymentfactory;
    }

    public function paynow(Payment $request)
    {
        $gateway = $this->paymentfactory->create($request['gateway']);
        $redirectUrl =  $this->paymentfactory::sanitizeRedirectUrl($request['redirectUrl']);

        try {
            $response = $gateway->processPayment($request['feeType'], $redirectUrl);

            if (!$response) {
                return ApiResponse::error(
                    status: 'error',
                    message: 'Error generating transaction reference.',
                    statusCode: Response::HTTP_UNAUTHORIZED
                );
            }

            $message = $response['generateStatus'] === false
                ? 'Transaction ID already generated.'
                : 'Payment details retrieved successfully.';

            return ApiResponse::success(
                status: 'success',
                message: $message,
                data: $response,
                statusCode: Response::HTTP_OK
            );
        } catch (Exception $e) {
            return ApiResponse::error(
                status: 'error',
                message: $e->getMessage(),
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }
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
        $gateway = $this->paymentfactory->create($request['gateway']);

        try {
            $response = $gateway->checkPayment($request['gateway']);

            if (!$response) {
                return ApiResponse::error(
                    status: 'error',
                    message: 'Error checking transactions status.',
                    statusCode: Response::HTTP_NOT_FOUND
                );
            }

            return ApiResponse::success(
                status: 'success',
                message: "Checking of transactions status has been completed.",
                data: $response,
                statusCode: Response::HTTP_OK
            );
        } catch (Exception $e) {
            return ApiResponse::error(
                status: 'error',
                message: $e->getMessage(),
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }
    }

    public function getpaymentHistory()
    {
        $response = $this->paymentfactory::getallPaidTransactions();

        if (!$response) {
            return ApiResponse::error(
                status: 'error',
                message: 'Error getting transactions history.',
                statusCode: Response::HTTP_NOT_FOUND
            );
        }

        return ApiResponse::success(
            status: 'success',
            message: "Payment history successfully retrieved.",
            data: $response,
            statusCode: Response::HTTP_OK
        );
    }
}
