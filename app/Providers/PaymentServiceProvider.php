<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Remita Credentials
        $this->app->singleton('remitaPayment.config', function () {
            return [
                'merchant_id' => '14266220685',
                'service_type_id' => '14402166455',
                'api_key' => 'E47KFQPW',
                'gateway_url' => 'https://login.remita.net/remita/exapp/api/v1/send/api/echannelsvc/merchant/api/paymentinit',
                'gateway_rrr_payment_url' => 'https://login.remita.net/remita/ecomm/finalize.reg',
                'check_status_url' => 'https://login.remita.net/remita/exapp/api/v1/send/api/echannelsvc',

                //other service types
                //otherfees
                'service_type_id_OTHERS_FEES' => '14545633900',
                'service_type_id_SIWES' => '14545559518',

                //schoolfees
                'service_type_id_SCHOOL_FEES' => '14545208411',
                'service_type_id_SUG_NADESSTU_GNS_NIPOGA' => '14545598990',
                'service_type_id_NEW_SCHOOL_FEES' => '15533345649',

            ];
        });

        // Bank Accounts
        $this->app->singleton('accounts.config', function () {
            return [
                'first_bank_main' => '2004402644',
                'first_bank_alumni' => '2011810346',
                'zenith_bank_compendium' => '1229110808',
                'union_bank_otherfees' => '0080527351',
                'union_SIWES' => '0080527351',
                'union_bank_NADESSTU' => '0036375968',
                // 'union_bank_SUG' => '0038079930',
                'zenith_bank_SUG' => '1229628710',
                'union_ANTICULT_SW' => '0037892949',
                'premium_trust_bank_NIPOGA' => '0040145965',
                'first_bank_GNS' => '2017447652',
                'fidelity_MICROSOFT_ACADEMY' => '4011256518',
                'sterling_bank_main' => '0072223654',
                'premium_trust_MEDICALS' => '0040115414',
                'uba_bank_compendium' => '1027077950',
            ];
        });
    }


    public function boot(): void
    {
        //
    }
}
