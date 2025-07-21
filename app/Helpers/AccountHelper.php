<?php

namespace App\Helpers;

use App\Models\Admissions\AppSession;
use App\Models\Programmes;
use App\Models\ProgrammeType;
use App\Models\Admissions\AppLogin;

class AccountHelper
{
    public static function validateCaptcha(int $captchaResult, int $firstNumber, int $secondNumber): bool
    {
        $checkTotal = $firstNumber + $secondNumber;

        if ($captchaResult != $checkTotal) {
            return false;
        }
        return true;
    }

    public static function generateUsername(int $prog, int $progtype): string
    {
        $prefix = Programmes::find($prog)->programme_abbreviation;
        $prefixType = ProgrammeType::find($progtype)->programme_type_abbreviation;
        $appyear = AppSession::getAppCurrentSession();
        $username = AppLogin::getNos($prog, $progtype, $appyear, $prefix, $prefixType);

        return $username;
    }
}
