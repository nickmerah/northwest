<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Models\Admissions\Jamb;
use App\Models\Admissions\OLevel;
use App\Models\Admissions\AppProfile;
use App\Models\Admissions\Certificates;
use App\Models\Admissions\SchoolAttended;
use App\Interfaces\ResultsRepositoryInterface;

class ResultRepository implements ResultsRepositoryInterface
{

    public function getOlevelDetails(int $applicantId): array
    {
        $olevelDetails = OLevel::where('std_id', $applicantId)->get()->toArray();

        if (empty($olevelDetails)) {

            return [];
        }
        $results = array_map(function ($result) {
            return [
                'subjectName' => $result['subname'],
                'examName' => $result['certname'],
                'grade' => $result['grade'],
                'examYear' => $result['eyear'],
                'examMonth' => $result['emonth'],
                'centerNo' => $result['centerno'],
                'examNo' => $result['examno'],
                'sitting' => $result['sitting'],
            ];
        }, $olevelDetails);

        return $results;
    }

    public function saveOlevel(int $applicantId, Request $request): array
    {
        foreach (['first', 'second'] as $key) {
            $subjects = array_values(array_filter($request->input("{$key}.subjectName", [])));
            $grades = array_values(array_filter($request->input("{$key}.grade", [])));
            $nosubject = count($subjects);

            OLevel::where(['std_id' => $applicantId, 'sitting' => $key,])
                ->whereNotIn('subname', $subjects)
                ->delete();

            for ($i = 0; $i < $nosubject; $i++) {
                OLevel::updateOrCreate(
                    [
                        'std_id' => $applicantId,
                        'subname' => $subjects[$i],
                        'sitting' => $key,
                    ],
                    [
                        'certname' => $request->input("{$key}.examName"),
                        'grade' => $grades[$i],
                        'emonth' => $request->input("{$key}.examMonth"),
                        'examno' => $request->input("{$key}.examNo"),
                        'centerno' => $request->input("{$key}.centerNo"),
                        'eyear' => $request->input("{$key}.examYear"),
                    ]
                );
            }
        }

        $this->updateAppProfile($applicantId, 'std_custome6');

        return self::getOlevelDetails($applicantId);
    }

    public function getJambDetails(int $applicantId): array
    {
        $jambDetails = Jamb::where('std_id', $applicantId)->get()->toArray();

        if (empty($jambDetails)) {

            return [];
        }

        $results = array_map(function ($result) {
            return [
                'jambNo' => $result['jambno'],
                'subjectName' => $result['subjectname'],
                'jambScore' => $result['jscore'],
            ];
        }, $jambDetails);

        return $results;
    }

    public function saveJamb(int $applicantId, Request $request): array
    {
        $subjects = $request['subjectName'];
        $scores = $request['jambScore'];
        $jambNo = $request['jambNo'];

        Jamb::where('std_id', $applicantId)
            ->whereNotIn('subjectname', $subjects)
            ->delete();

        for ($i = 0; $i < count($subjects); $i++) {
            Jamb::updateOrCreate(
                [
                    'std_id' => $applicantId,
                    'subjectname' => $subjects[$i],
                ],
                [
                    'jambno' => $jambNo,
                    'jscore' => $scores[$i],
                ]
            );
        }

        $this->updateAppProfile($applicantId, 'std_custome7');

        return self::getJambDetails($applicantId);
    }

    public function getSchoolAttended(int $applicantId): array
    {
        $schoolAttendedDetails = SchoolAttended::where('std_id', $applicantId)->get()->toArray();

        if (empty($schoolAttendedDetails)) {

            return [];
        }

        $results = array_map(function ($result) {
            return [
                'schoolName' => $result['schoolname'],
                'ndMatno' => $result['ndmatno'],
                'courseofStudy' => $result['cos'],
                'grade' => $result['grade'],
                'fromDate' => $result['fromdate'],
                'toDate' => $result['todate'],
            ];
        }, $schoolAttendedDetails);

        return $results;
    }

    public function saveSchoolAttended(int $applicantId, Request $request): array
    {
        SchoolAttended::updateOrCreate(
            [
                'std_id' => $applicantId,
            ],
            [
                'schoolname' => $request['schoolName'],
                'ndmatno' => $request['ndMatno'],
                'cos' => $request['courseofstudy'],
                'grade' => $request['grade'],
                'fromdate' => $request['fromDate'],
                'todate' => $request['toDate'],
            ]
        );

        $this->updateAppProfile($applicantId, 'std_custome5');

        return self::getSchoolAttended($applicantId);
    }

    protected function updateAppProfile(string $applicantId, string $column): void
    {
        $appProfile = AppProfile::where('std_logid', $applicantId)->first();

        if ($appProfile && in_array($column, ['std_custome5', 'std_custome6', 'std_custome7'])) {
            $appProfile->update([$column => 1]);
        }
    }

    public function getUploadedResults(int $applicantId): array
    {
        $uploadedResults = Certificates::where('stdid', $applicantId)->get()->toArray();

        if (empty($uploadedResults)) {

            return [];
        }

        $results = array_map(function ($result) {
            return [
                'documentName' => $result['docname'],
                'uploadName' => $result['uploadname'],
            ];
        }, $uploadedResults);

        return $results;
    }

    public function saveCertificates(array $certificates, int $applicantId): array
    {
        foreach ($certificates as $data) {
            Certificates::create($data);
        }

        return self::getUploadedResults($applicantId);
    }

    public function deleteDocumentRecords(int $applicantId): bool
    {
        return Certificates::where('stdid', $applicantId)->delete() > 0;
    }
}
