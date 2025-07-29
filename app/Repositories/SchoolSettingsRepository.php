<?php

namespace App\Repositories;

use App\Models\Lga;
use App\Models\Programmes;
use App\Models\SchoolInfo;
use App\Models\ProgrammeType;
use App\Models\StateOfOrigin;
use App\Models\DepartmentOptions;
use Illuminate\Support\Facades\DB;
use App\Interfaces\SchoolSettingsRepositoryInterface;


class SchoolSettingsRepository implements SchoolSettingsRepositoryInterface
{
    public function getSchoolInfo()
    {
        return SchoolInfo::get(['appmarkuee', 'appenddate']);
    }

    public function getProgramme()
    {
        return Programmes::where('p_status', 1)
            ->get(['programme_id', 'programme_name', 'aprogramme_name'])->toArray();
    }

    public function getProgrammeTypes()
    {
        return ProgrammeType::where('pt_status', 1)
            ->get(['programmet_id', 'programmet_name'])->toArray();
    }


    public function getCoursesOfStudy(int $programmeId, int $programmeTypeId)
    {
        $query = DepartmentOptions::select('do_id', 'programme_option')
            ->where('prog_id', $programmeId)
            ->orderBy('programme_option', 'ASC');

        if ($programmeTypeId != 2) {
            $query->where('d_status', 1);
        }

        if ($programmeTypeId == 2) {
            $query->where('prog_option', 0)
                ->where('d_status_pt', 1);
        }

        return $query->get();
    }

    public function getStateofOrigin(): array
    {
        return StateOfOrigin::select('state_id', 'state_name')->get()->toArray();
    }

    public function getLGAByStateId($stateId): array
    {
        return Lga::select('lga_id', 'lga_name')
            ->where('state_id', $stateId)
            ->get()
            ->toArray();
    }

    public function getOlevelSubjects(): array
    {
        return DB::table('subjects')->select('subjectname')->get()->toArray();
    }
}
