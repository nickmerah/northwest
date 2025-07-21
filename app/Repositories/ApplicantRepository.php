<?php

namespace App\Repositories;

use App\Models\Admissions\AppFee;
use Illuminate\Support\Facades\Auth;
use App\Models\Admissions\AppProfile;
use App\Models\Admissions\AppTransaction;


class ApplicantRepository implements ApplicantRepositoryInterface
{

    public function getdashBoardData(): ?array
    {
        $user = Auth::user();
        $userData = AppProfile::getUserData($user->log_id);
        $userStat = AppProfile::getDashboardData($user->log_id);
        $applicantFee = AppFee::getApplicantFees($userData['programmeId'], $userData['programmeTypeId']);
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
