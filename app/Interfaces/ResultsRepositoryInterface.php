<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ResultsRepositoryInterface
{
    public function getOlevelDetails(int $applicantId): array;

    public function saveOlevel(int $applicantId, Request $request): array;

    public function getJambDetails(int $applicantId): array;

    public function saveJamb(int $applicantId, Request $request): array;

    public function getSchoolAttended(int $applicantId): array;

    public function saveSchoolAttended(int $applicantId, Request $request): array;

    public function getUploadedResults(int $applicantId): array;

    public function saveCertificates(array $certificates, int $applicantId): array;

    public function deleteDocumentRecords(int $applicantId): bool;
}
