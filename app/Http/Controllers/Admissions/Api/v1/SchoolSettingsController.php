<?php

namespace App\Http\Controllers\Admissions\Api\v1;


use App\Http\Requests\Lga;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\ApiResponseService;
use App\Helpers\SchoolSettingsHelper;
use App\Interfaces\SchoolSettingsRepositoryInterface;
use Symfony\Component\HttpFoundation\Response as ApiJsonResponse;

class SchoolSettingsController extends Controller
{
    protected $schoolSettingsRepository;
    protected $apiResponse;

    public function __construct(SchoolSettingsRepositoryInterface $schoolSettingsRepository, ApiResponseService $apiResponse)
    {
        $this->schoolSettingsRepository = $schoolSettingsRepository;
        $this->apiResponse = $apiResponse;
    }

    public function index(): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->schoolSettingsRepository->getSchoolInfo(),
            'School information retrieved successfully.',
            'Data not found.',
            ApiJsonResponse::HTTP_OK,
            fn($response) => !empty($response)
        );
    }

    public function getProgrammes(): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->schoolSettingsRepository->getProgramme(),
            'Programmes retrieved successfully.',
            'No programmes found.',
            ApiJsonResponse::HTTP_OK,
            fn($response) => !empty($response)
        );
    }

    public function getProgrammeTypes(): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->schoolSettingsRepository->getProgrammeTypes(),
            'Programme types retrieved successfully.',
            'No programme types found.',
            ApiJsonResponse::HTTP_OK,
            fn($response) => !empty($response)
        );
    }

    public function getCoursesOfStudy(int $programmeId, int $programmeTypeId): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->schoolSettingsRepository->getCoursesOfStudy($programmeId, $programmeTypeId),
            'Course of study retrieved successfully.',
            'No course of study found.',
            ApiJsonResponse::HTTP_OK,
            fn($response) => !empty($response)
        );
    }

    public function getStateofOrigin(): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->schoolSettingsRepository->getStateofOrigin(),
            'State of origin retrieved successfully.',
            'No state of origin found.',
            ApiJsonResponse::HTTP_OK,
            fn($response) => !empty($response)
        );
    }

    public function getLga(Lga $request): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->schoolSettingsRepository->getLGAByStateId($request->stateId),
            'LGA retrieved successfully.',
            'No LGA found.',
            ApiJsonResponse::HTTP_OK,
            fn($response) => !empty($response)
        );
    }

    public function getOlevelSubjects(): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => $this->schoolSettingsRepository->getOlevelSubjects(),
            'Olevel Subjects retrieved successfully.',
            'No Subjects found.',
            ApiJsonResponse::HTTP_OK,
            fn($response) => !empty($response)
        );
    }

    public function getOlevelGrades(): JsonResponse
    {
        return $this->apiResponse->respond(
            fn() => SchoolSettingsHelper::olevelGrades(),
            'Olevel Grades retrieved successfully.',
            'No grades found.',
            ApiJsonResponse::HTTP_OK,
            fn($response) => !empty($response)
        );
    }
}
