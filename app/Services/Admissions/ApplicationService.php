<?php

namespace App\Services\Admissions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\PendingRequest;

class ApplicationService
{
    protected function handleRequest(callable $callback, string $errorMessage): array
    {
        try {
            $response = $callback();
            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => $response['message'] ?? 'Request successful.',
                    'data'    => $response->json(),
                ];
            }
            return [
                'success' => false,
                'message' => $errorMessage,
                'data'    => $response->json(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'data'    => [],
            ];
        }
    }

    protected function apiUrl(string $path): string
    {
        return config('app.url') . "/api/v1/{$path}";
    }

    protected function withAuth(): PendingRequest
    {
        return Http::withToken(session('access_token'))->withHeaders(['Accept' => 'application/json']);
    }

    public function logout(): array
    {
        return $this->handleRequest(fn() => $this->withAuth()->get($this->apiUrl('logout')), 'unable to logout applicant.');
    }

    public function states(): array
    {
        return $this->handleRequest(fn() => Http::get($this->apiUrl('getstateoforigin')), 'unable to retrieve states of origin.');
    }

    public function saveprofile(Request $request): array
    {
        $client = $this->withAuth()->asMultipart();

        if ($request->boolean('updatePassport') && $request->hasFile('file')) {
            $client->attach(
                'profilePicture',
                file_get_contents($request->file('file')->getRealPath()),
                $request->file('file')->getClientOriginalName()
            );
        }

        $payload = [
            'othernames'            => $request->input('othernames'),
            'gender'                => $request->input('gender'),
            'maritalStatus'         => $request->input('marital_status'),
            'stateofOrigin'         => $request->input('state'),
            'lga'                   => $request->input('lga'),
            'birthDate'             => sprintf('%s-%s-%s', $request->input('yob'), $request->input('mob'), $request->input('dob')),
            'contactAddress'        => $request->input('contact_address'),
            'studentHomeAddress'    => $request->input('student_homeaddress'),
            'homeTown'              => $request->input('hometown'),
            'nextofKin'             => $request->input('nok'),
            'nextofKinAddress'      => $request->input('nok_address'),
            'nextofKinEmail'        => $request->input('nok_email'),
            'nextofKinPhoneNo'      => $request->input('nok_tel'),
            'nextofKinRelationship' => $request->input('nok_rel'),
            'updateWithPassport'    => $request->input('updatePassport', 0),
        ];

        return $this->handleRequest(fn() => $client->post($this->apiUrl('biodata'), $payload), 'Saving of profile failed.');
    }

    public function refreshApplicantCache(int $userId, string $token): object
    {
        $response = Http::withToken($token)->get(
            $this->apiUrl('dashboard') . '?include=firstChoiceCourse,secondChoiceCourse,programme,programmeType,stateofOrigin,lga'
        );

        $data = (object) $response->json()['data'];
        Cache::put("dashboard:{$userId}", $data, now()->addHour());

        return $data;
    }

    public function getOlevelResults(): array
    {
        return $this->handleRequest(fn() => $this->withAuth()->get($this->apiUrl('olevels')), "Unable to retrieve applicant's O'level results.");
    }

    public function getOlevelSubjects(): array
    {
        return $this->handleRequest(fn() => Http::get($this->apiUrl('getolevelsubjects')), "Unable to retrieve applicant's O'level subjects.");
    }

    public function getOlevelGrades(): array
    {
        return $this->handleRequest(fn() => Http::get($this->apiUrl('getolevelgrades')), "Unable to retrieve applicant's O'level grades.");
    }

    public function saveolevel(Request $request): array
    {
        $formatOlevel = fn($data, $sitting) => [
            'examName'    => $data['examName'] ?? '',
            'centerNo'    => $data['centerNo'] ?? '',
            'examNo'      => $data['examNo'] ?? '',
            'examMonth'   => $data['examMonth'] ?? '',
            'examYear'    => $data['examYear'] ?? '',
            'subjectName' => array_values(array_filter($data['subjectName'] ?? [])),
            'grade'       => array_values(array_filter($data['grade'] ?? [])),
            'sitting'     => $sitting,
        ];

        $payload = ['first' => $formatOlevel($request->input('first'), 'First')];

        if (
            !empty(array_filter($request->input('second.subjectName', []))) &&
            !empty(array_filter($request->input('second.grade', [])))
        ) {
            $payload['second'] = $formatOlevel($request->input('second'), 'Second');
        }

        return $this->handleRequest(
            fn() => $this->withAuth()->post($this->apiUrl('olevels'), $payload),
            'Saving of Olevel failed.'
        );
    }

    public function getJambResults(): array
    {
        return $this->handleRequest(fn() => $this->withAuth()->get($this->apiUrl('jamb')), "Unable to retrieve applicant's Jamb results.");
    }

    public function savejamb(Request $request): array
    {
        $payload = [
            'jambNo'      => $request->input('jambNo'),
            'subjectName' => array_filter($request->input('subjectName', [])),
            'jambScore'   => array_filter($request->input('jambScore', [])),
        ];

        return $this->handleRequest(
            fn() => $this->withAuth()->post($this->apiUrl('jamb'), $payload),
            'Saving of Jamb results failed.'
        );
    }

    public function getSchool(): array
    {
        return $this->handleRequest(fn() => $this->withAuth()->get($this->apiUrl('schoolattended')), "Unable to retrieve applicant's school details.");
    }

    public function saveSchool(Request $request): array
    {
        $payload = $request->only(['schoolName', 'ndMatno', 'courseofstudy', 'grade', 'fromDate', 'toDate']);

        return $this->handleRequest(
            fn() => $this->withAuth()->post($this->apiUrl('schoolattended'), $payload),
            'Saving of school details failed.'
        );
    }

    public function getCertificates(): array
    {
        return $this->handleRequest(fn() => $this->withAuth()->get($this->apiUrl('resultupload')), "Unable to retrieve applicant's Certificates.");
    }

    public function uploadCertificates(Request $request): array
    {
        $client = $this->withAuth()->asMultipart();

        foreach (['jamb_result', 'o_level_result', 'birth_certificate'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $client->attach($field, file_get_contents($file->getRealPath()), $file->getClientOriginalName());
            }
        }

        return $this->handleRequest(fn() => $client->post($this->apiUrl('resultupload')), 'Certificates Upload failed.');
    }

    public function deleteCertificates(): array
    {
        return $this->handleRequest(fn() => $this->withAuth()->delete($this->apiUrl('removeresult')), "Unable to delete applicant's Certificates.");
    }

    public function getDecalaration(): array
    {
        return $this->handleRequest(fn() => $this->withAuth()->get($this->apiUrl('declaration')), "Unable to retrieve applicant's declaration.");
    }

    public function saveDecalaration(): array
    {
        return $this->handleRequest(fn() => $this->withAuth()->post($this->apiUrl('declaration')), 'Saving declaration failed.');
    }
}
