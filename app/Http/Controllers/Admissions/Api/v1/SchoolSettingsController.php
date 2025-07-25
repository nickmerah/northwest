<?php

namespace App\Http\Controllers\Admissions\Api\v1;


use App\Http\Requests\Lga;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Helpers\SchoolSettingsHelper;
use App\Interfaces\SchoolSettingsRepositoryInterface;
use Symfony\Component\HttpFoundation\Response as ApiJsonResponse;

class SchoolSettingsController extends Controller
{
    protected $schoolSettingsRepository;

    public function __construct(SchoolSettingsRepositoryInterface $schoolSettingsRepository)
    {
        $this->schoolSettingsRepository = $schoolSettingsRepository;
    }

    public function index(): JsonResponse
    {
        $schoolSettings = $this->schoolSettingsRepository->getSchoolInfo();

        if (!$schoolSettings) {
            return ApiResponse::error(
                status: 'error',
                message: 'Data not found.',
                statusCode: ApiJsonResponse::HTTP_NOT_FOUND
            );
        }

        return ApiResponse::success(
            status: 'success',
            message: 'School information retrieved successfully.',
            data: $schoolSettings,
            statusCode: ApiJsonResponse::HTTP_OK
        );
    }

    public function getProgrammes(): JsonResponse
    {
        $programmes = $this->schoolSettingsRepository->getProgramme();

        if (!$programmes) {
            return ApiResponse::error(
                status: 'error',
                message: 'No programmes found.',
                statusCode: ApiJsonResponse::HTTP_NOT_FOUND
            );
        }

        return ApiResponse::success(
            status: 'success',
            message: 'Programmes retrieved successfully.',
            data: $programmes,
            statusCode: ApiJsonResponse::HTTP_OK
        );
    }

    public function getProgrammeTypes(): JsonResponse
    {
        $programmeTypes = $this->schoolSettingsRepository->getProgrammeTypes();

        if (!$programmeTypes) {
            return ApiResponse::error(
                status: 'error',
                message: 'No programme types found.',
                statusCode: ApiJsonResponse::HTTP_NOT_FOUND
            );
        }

        return ApiResponse::success(
            status: 'success',
            message: 'Programme types retrieved successfully.',
            data: $programmeTypes,
            statusCode: ApiJsonResponse::HTTP_OK
        );
    }

    public function getCoursesOfStudy(int $programmeId, int $programmeTypeId): JsonResponse
    {
        $courseofstudy = $this->schoolSettingsRepository->getCoursesOfStudy($programmeId, $programmeTypeId);

        if (!$courseofstudy) {
            return ApiResponse::error(
                status: 'error',
                message: 'No course of study found.',
                statusCode: ApiJsonResponse::HTTP_NOT_FOUND
            );
        }

        return ApiResponse::success(
            status: 'success',
            message: 'Course of study retrieved successfully.',
            data: $courseofstudy,
            statusCode: ApiJsonResponse::HTTP_OK
        );
    }

    public function getStateofOrigin(): JsonResponse
    {
        $stateoforigin = $this->schoolSettingsRepository->getStateofOrigin();

        if (!$stateoforigin) {
            return ApiResponse::error(
                status: 'error',
                message: 'No state of origin found.',
                statusCode: ApiJsonResponse::HTTP_NOT_FOUND
            );
        }

        return ApiResponse::success(
            status: 'success',
            message: 'State of origin retrieved successfully.',
            data: $stateoforigin,
            statusCode: ApiJsonResponse::HTTP_OK
        );
    }

    public function getLga(Lga $request): JsonResponse
    {
        $lga = $this->schoolSettingsRepository->getLGAByStateId($request->stateId);

        if (!$lga) {
            return ApiResponse::error(
                status: 'error',
                message: 'No LGA found.',
                statusCode: ApiJsonResponse::HTTP_NOT_FOUND
            );
        }

        return ApiResponse::success(
            status: 'success',
            message: 'LGA retrieved successfully.',
            data: $lga,
            statusCode: ApiJsonResponse::HTTP_OK
        );
    }

    public function getOlevelSubjects(Lga $request): JsonResponse
    {
        $subjects = $this->schoolSettingsRepository->getOlevelSubjects();

        if (!$subjects) {
            return ApiResponse::error(
                status: 'error',
                message: 'No Subjects found.',
                statusCode: ApiJsonResponse::HTTP_NOT_FOUND
            );
        }

        return ApiResponse::success(
            status: 'success',
            message: 'Olevel Subjects retrieved successfully.',
            data: $subjects,
            statusCode: ApiJsonResponse::HTTP_OK
        );
    }

    public function getOlevelGrades()
    {
        $grades = SchoolSettingsHelper::olevelGrades();

        return ApiResponse::success(
            status: 'success',
            message: 'Olevel Grades retrieved successfully.',
            data: $grades,
            statusCode: ApiJsonResponse::HTTP_OK
        );
    }
}
