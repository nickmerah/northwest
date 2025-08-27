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
                'portalFee' => 5000,
                'callback_url' => "https://eportal.nowecons.edu.ng/paystackresponse",
                'cancel_action_url' => "https://eportal.nowecons.edu.ng/paystackcancelaction",
            ];
        });
    }


    public function boot(): void
    {
        //
    }
}
