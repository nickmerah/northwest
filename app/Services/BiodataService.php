<?php

namespace App\Services;

use App\Helpers\AccountHelper;
use App\Http\Requests\Profile;
use Illuminate\Support\Facades\Cache;
use App\Interfaces\ProfileRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;



class BiodataService
{
    protected $profileRepository;

    public function __construct(ProfileRepositoryInterface $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function getBiodata()
    {
        $applicantId = AccountHelper::getUserId();

        return $this->profileRepository->getBiodataDetails($applicantId);
    }

    public function saveBiodata(Profile $request)
    {
        $applicantId = AccountHelper::getUserId();
        $applicant = Cache::get("applicant:{$applicantId}")
            ?? abort(Response::HTTP_BAD_REQUEST, 'Cached applicant not found.');

        //check if application fee has been paid
        $applicationFee = array_values(array_filter($applicant['paymentDetails'], fn($p) => $p['feeId'] == 1))[0] ?? null;
        if (empty($applicationFee)) {
            abort(Response::HTTP_BAD_REQUEST, 'You can only save biodata after payment of application fees');
        }

        return $this->profileRepository->saveBiodata($applicant, $request);
    }
}
