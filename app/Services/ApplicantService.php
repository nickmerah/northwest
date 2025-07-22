<?php

namespace App\Services;

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
}
