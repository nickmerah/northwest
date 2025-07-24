<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // paystack Credentials
        $this->app->singleton('paystackPayment.config', function () {
            return [
                'ps_SK' => env('PAYSTACK_SECRET_KEY'),
                'subAccount' => env('PAYSTACK_SUBACCOUNT'),
                'transactionFee' => 500,
                'accomodation' => 500,
                'portalFee' => 2000,
                'callback_url' => "https://portal.bawocons.edu.ng/paystackpaymentresponse",
                'cancel_action_url' => "https://portal.bawocons.edu.ng/paystackcancelaction",
            ];
        });
    }


    public function boot(): void
    {
        //
    }
}
