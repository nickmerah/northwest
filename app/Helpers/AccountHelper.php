<?php

namespace App\Helpers;

use App\Models\Programmes;
use App\Models\ProgrammeType;
use Illuminate\Support\Carbon;
use App\Models\Admissions\AppLogin;
use Illuminate\Support\Facades\Http;
use App\Models\Admissions\AppSession;
use Illuminate\Support\Facades\Cache;
use App\Interfaces\AccountRepositoryInterface;
use App\Models\Admissions\AppTransaction;
use Symfony\Component\HttpFoundation\Response;

class AccountHelper
{
    public function __construct(protected AccountRepositoryInterface $accountRepository) {}

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

    public function generateOAuthToken(array $credentials): array
    {
        $response = Http::asForm()->post(env('APP_URL') . '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => env('PERSONAL_ACCESS_CLIENT_ID'),
            'client_secret' => env('PERSONAL_ACCESS_CLIENT_SECRET'),
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'scope' => '*',
        ]);

        if ($response->failed()) {
            abort(Response::HTTP_BAD_REQUEST, 'Token request failed');
        }

        $data = $response->json();
        $data['expires_at'] = Carbon::now()->addSeconds($data['expires_in']);

        return $data;
    }

    public function cacheApplicantData(int $userId): array
    {
        $applicant = $this->accountRepository->getApplicantDetails($userId);

        $data = $this->getMinimalApplicant($applicant);

        $data['paymentDetails'] = self::getApplicationFees($userId);

        Cache::put("applicant:{$userId}", $data, now()->addHour());

        return $data;
    }

    public function getMinimalApplicant($applicant): array
    {
        return [
            'id' => $applicant->std_logid,
            'surname' => $applicant->surname,
            'firstname' => $applicant->firstname,
            'othernames' => $applicant->othernames,
            'applicationNumber' => $applicant->app_no,
            'email' => $applicant->student_email,
            'phoneNumber' => $applicant->student_mobiletel,
            'programme' => $applicant->stdprogramme_id,
            'firstchoice' => $applicant->stdcourse,
            'secondchoice' => $applicant->std_course,
            'programmeType' => $applicant->std_programmetype,
            'portalStatus' => $this->accountRepository->isPortalClosed($applicant->stdprogramme_id),
            'biodata' => $applicant->biodata,
            'schoolAttended' => $applicant->std_custome5,
            'olevels' => $applicant->std_custome6,
            'jambResult' => $applicant->std_custome7,
            'declaration' => $applicant->std_custome8,
            'appSubmit' => $applicant->std_custome9,
        ];
    }

    private static function getApplicationFees(int $userId): array
    {
        $feesPaid = AppTransaction::getApplicantPaymentStatus($userId);
        $applicationFees = array_map(function ($fee) {
            return [
                'feeId' => $fee['feeId'],
            ];
        }, $feesPaid);

        return $applicationFees;
    }
}
