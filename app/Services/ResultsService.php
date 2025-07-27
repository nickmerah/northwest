<?php

namespace App\Services;

use App\Helpers\AccountHelper;
use App\Http\Requests\Olevel;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use App\Interfaces\ResultsRepositoryInterface;
use App\Http\Requests\Jamb;
use App\Http\Requests\SchoolAttended;

class ResultsService
{
    protected $resultsRepository;

    public function __construct(ResultsRepositoryInterface $resultsRepository)
    {
        $this->resultsRepository = $resultsRepository;
    }

    public function getOlevels()
    {
        return $this->resultsRepository->getOlevelDetails(AccountHelper::getUserId());
    }

    public function saveOlevels(Olevel $request)
    {
        $applicantId = AccountHelper::getUserId();
        $applicant = Cache::get("applicant:{$applicantId}")
            ?? abort(Response::HTTP_BAD_REQUEST, 'Cached applicant not found.');

        //check if biodata has been saved
        if ($applicant['biodata'] === 0) {
            abort(Response::HTTP_BAD_REQUEST, 'You can only save olevel details after updating your profile');
        }

        return $this->resultsRepository->saveOlevel($applicantId, $request);
    }

    public function getJamb()
    {
        return $this->resultsRepository->getJambDetails(AccountHelper::getUserId());
    }

    public function saveJamb(Jamb $request)
    {
        $applicantId = AccountHelper::getUserId();
        $applicant = Cache::get("applicant:{$applicantId}")
            ?? abort(Response::HTTP_BAD_REQUEST, 'Cached applicant not found.');

        //check if olevel has been saved
        if ($applicant['olevels'] === 0) {
            abort(Response::HTTP_BAD_REQUEST, 'You can only save jamb details after saving your olevel results');
        }

        return $this->resultsRepository->saveJamb($applicantId, $request);
    }

    public function getSchoolAttended()
    {
        return $this->resultsRepository->getSchoolAttended(AccountHelper::getUserId());
    }

    public function saveSchoolAttended(SchoolAttended $request)
    {
        $applicantId = AccountHelper::getUserId();
        $applicant = Cache::get("applicant:{$applicantId}")
            ?? abort(Response::HTTP_BAD_REQUEST, 'Cached applicant not found.');

        //check if olevel has been saved
        if ($applicant['olevels'] === 0) {
            abort(Response::HTTP_BAD_REQUEST, 'You can only save jamb details after saving your olevel results');
        }

        return $this->resultsRepository->saveSchoolAttended($applicantId, $request);
    }
}
