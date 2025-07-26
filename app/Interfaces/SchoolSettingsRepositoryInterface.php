<?php

namespace App\Interfaces;

interface SchoolSettingsRepositoryInterface
{
    public function getSchoolInfo();

    public function getProgramme();

    public function getProgrammeTypes();

    public function getCoursesOfStudy(int $programmeId, int $programmeTypeId);

    public function getStateofOrigin(): array;

    public function getLGAByStateId($stateId): array;

    public function getOlevelSubjects(): array;
}
