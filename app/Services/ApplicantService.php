<?php

namespace App\Services;

use App\Helpers\AccountHelper;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use App\Interfaces\ApplicantRepositoryInterface;


class ApplicantService
{
    protected $applicantRepository;

    public function __construct(ApplicantRepositoryInterface $applicantRepository)
    {
        $this->applicantRepository = $applicantRepository;
    }

    public function getdashBoardData($request): ?array
    {
        $response = $this->applicantRepository->getdashBoardData($request);
        return $response;
    }

    public function getDeclaration(): ?array
    {
        return AccountHelper::getDeclarationText();
    }

    public function saveDeclaration(): ?array
    {
        $applicantId = AccountHelper::getUserId();
        $applicant = Cache::get("applicant:{$applicantId}")
            ?? abort(Response::HTTP_BAD_REQUEST, 'Cached applicant not found.');

        //check if all requirement have been met
        if ($applicant['biodata'] === 0 || $applicant['olevels'] === 0 || ($applicant['jambResult'] === 0 || $applicant['schoolAttended'] === 0)) {
            abort(Response::HTTP_BAD_REQUEST, 'You can only save declaration and submit your application after filling all forms.');
        }

        $response = $this->applicantRepository->saveDeclaration();
        return $response;
    }
}
