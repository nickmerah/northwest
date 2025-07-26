<?php

namespace App\Interfaces;

use App\Http\Requests\Profile;

interface ProfileRepositoryInterface
{
    public function getBiodataDetails(int $applicantId): array;

    public function saveBiodata(array $applicant, Profile $request): array;
}
