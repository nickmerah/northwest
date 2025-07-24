<?php

namespace App\Http\Controllers\Admissions\Api\v1;

use Exception;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Payment;
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

    public function paystackpaymentresponse(Request $request)
    {
        // Retrieve query parameters
        $trxref = $request->input('trxref');
        $reference = $request->input('reference');

        $response = $gateway->processPayment($request['feeType'], $redirectUrl);

        $response = self::payStack_transaction_details($trxref);

        $status = $response['data']['status'];
        $reference = $response['data']['reference'];


        if ($status == "success") {

            $udata = [
                'pay_status'  => "Paid"
            ];

            STransaction::where('trans_no', $trxref)
                ->update($udata);

            // Success, redirection
            return new RedirectResponse('http://localhost/northwest/admissions');
        } else {

            return ApiResponse::error(
                status: 'error',
                message: "Transaction is Pending, Your Payment Reference is $trxref.  Kindly requery your transaction if debited or try again",
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}
