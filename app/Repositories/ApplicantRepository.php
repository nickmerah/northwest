<?php

namespace App\Repositories;

use App\Models\Admissions\AppFee;
use Illuminate\Support\Facades\Auth;
use App\Models\Admissions\AppProfile;
use App\Models\Admissions\AppTransaction;
use App\Interfaces\ApplicantRepositoryInterface;

class ApplicantRepository implements ApplicantRepositoryInterface
{

    public function getdashBoardData($request): ?array
    {
        $user = Auth::user();
        $userData = AppProfile::getUserData($user->log_id, $request);
        $userStat = AppProfile::getDashboardData($user->log_id);
        $applicantFee = AppFee::getApplicantFees($userData);
        $applicationPaymentStatus = AppTransaction::getApplicantPaymentStatus($user->log_id);


        return [
            'success' => true,
            'user' => $userData,
            'applicationFee' => $applicantFee,
            'applicationPaymentStatus' => $applicationPaymentStatus,
            'stats' => $userStat,

        ];
    }
}
