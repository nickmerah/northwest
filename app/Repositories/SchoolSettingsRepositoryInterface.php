<?php

namespace App\Repositories;

interface SchoolSettingsRepositoryInterface
{
    public function getSchoolInfo();

    public function getProgramme();

    public function getProgrammeTypes();

    public function getCoursesOfStudy(int $programmeId, int $programmeTypeId);
}
