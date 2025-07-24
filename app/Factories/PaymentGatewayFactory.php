<?php

namespace App\Factories;

use Illuminate\Support\Str;
use App\Services\PayStackGateway;
use App\Interfaces\PaymentGateway;
use Symfony\Component\HttpFoundation\Response;

class PaymentGatewayFactory
{
    public function create(string $gateway): PaymentGateway
    {
        return match (strtolower($gateway)) {
            'paystack' => app(PayStackGateway::class),
            default => abort(Response::HTTP_BAD_REQUEST, "Unsupported gateway: $gateway")
        };
    }


    public static function sanitizeRedirectUrl(string $redirectUrl)
    {
        $redirectUrl = trim($redirectUrl);
        $redirectUrl = Str::finish(Str::lower($redirectUrl), '/');
        $parsed = parse_url($redirectUrl);

        if (!isset($parsed['scheme']) || $parsed['scheme'] !== 'https') {
            abort(Response::HTTP_BAD_REQUEST, 'Only HTTPS Redirect URLs are allowed.');
        }

        return $redirectUrl;
    }
}
