<?php

namespace App\Services;

use App\Repositories\ApplicantRepositoryInterface;

class ApplicantService
{
    protected $applicantRepository;

    public function __construct(ApplicantRepositoryInterface $applicantRepository)
    {
        $this->applicantRepository = $applicantRepository;
    }

    public function getdashBoardData(): ?array
    {
        $response = $this->applicantRepository->getdashBoardData();
        return $response;
    }
}
