<?php

namespace App\Http\Controllers\Admissions;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Helpers\ApplicationHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Services\Admissions\ApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;


class ApplicantController extends BaseController
{
    public function __construct(protected ApplicationService $applicationService) {}

    public function applicationhome(): View
    {
        $data = ApplicationHelper::getApplicanData();

        return view('admissions.applicants.applicationhome', [
            'applicantPayments' => $data->applicationPaymentStatus,
            'applicantStatus'   => $data->stats,
            'applicantProgramme' => $data->user['programme']['programme_id'],
            'applicantProgrammeType' => $data->user['programmeType'],
        ]);
    }

    public function biodata(): View
    {
        $data = ApplicationHelper::getApplicanData();

        return view('admissions.applicants.profile', [
            'biodetail'         => (object) $data->user,
            'applicantPayments' => $data->applicationPaymentStatus,
            'applicantStatus'   => $data->stats,
            'states'            => (object) $this->applicationService->states()['data']['data'],
        ]);
    }

    public function savebiodata(Request $request): RedirectResponse
    {
        return $this->handleSave(
            fn() => $this->applicationService->saveprofile($request),
            'admissions.biodata',
            'Unable to update profile, try Again!'
        );
    }

    public function olevel(): View
    {
        $data = ApplicationHelper::getApplicanData();

        return view('admissions.applicants.olevel', [
            'applicantStatus' => $data->stats,
            'olevelResults'   => (object) $this->applicationService->getOlevelResults()['data'],
            'olevelSubjects'  => $this->applicationService->getOlevelSubjects()['data'],
            'olevelGrades'    => $this->applicationService->getOlevelGrades(),
        ]);
    }

    public function saveolevel(Request $request): RedirectResponse
    {
        return $this->handleSave(
            fn() => $this->applicationService->saveolevel($request),
            'admissions.olevel',
            'Unable to add olevel results, try Again!'
        );
    }

    public function jamb(): View
    {
        $data = ApplicationHelper::getApplicanData();

        return view('admissions.applicants.jamb', [
            'applicantStatus' => $data->stats,
            'jambResults'     => $this->applicationService->getJambResults()['data'],
            'olevelSubjects'  => $this->applicationService->getOlevelSubjects()['data'],
        ]);
    }

    public function savejamb(Request $request): RedirectResponse
    {
        return $this->handleSave(
            fn() => $this->applicationService->savejamb($request),
            'admissions.jamb',
            'Unable to add jamb results, try Again!'
        );
    }

    public function school(): View
    {
        $data = ApplicationHelper::getApplicanData();

        return view('admissions.applicants.schoolattended', [
            'schoolDetails'   => $this->applicationService->getSchool()['data'],
            'applicantStatus' => $data->stats,
        ]);
    }

    public function saveschool(Request $request): RedirectResponse
    {
        return $this->handleSave(
            fn() => $this->applicationService->saveSchool($request),
            'admissions.school',
            'Unable to add school details, try Again!'
        );
    }

    public function certupload(): View
    {
        $data = ApplicationHelper::getApplicanData();

        return view('admissions.applicants.uploadcertificate', [
            'certificates'    => (object) $this->applicationService->getCertificates()['data'],
            'applicantStatus' => $data->stats,
        ]);
    }

    public function savecertupload(Request $request): RedirectResponse
    {
        return $this->handleSave(
            fn() => $this->applicationService->uploadCertificates($request),
            'admissions.certupload',
            'Unable to upload certificates, try Again!'
        );
    }

    public function deletecertupload(): RedirectResponse
    {
        $response = $this->applicationService->deleteCertificates();

        if ($response['success']) {
            $this->refreshCache();
        }

        return redirect()->route('admissions.certupload');
    }

    public function declares(): View|RedirectResponse
    {
        $data = $this->getApplicantFormData();

        if (!($data['applicantStatus']['biodata']
            && $data['applicantStatus']['olevels']
            && $data['applicantStatus']['jambResult']
            && $data['applicantStatus']['schoolattended'])) {
            return redirect()->route('admissions.myapplication');
        }

        return view('admissions.applicants.declaration', $data);
    }

    public function savedeclares(): RedirectResponse
    {
        return $this->handleSave(
            fn() => $this->applicationService->saveDecalaration(),
            'admissions.myapplication',
            'Unable to save declaration, try Again!'
        );
    }

    public function applicationforms(): View
    {
        $data = ApplicationHelper::getApplicanData();

        return view('admissions.applicants.applicationforms', [
            'applicantStatus' => $data->stats
        ]);
    }

    public function applicationform(): View
    {
        $data = $this->getApplicantFormData();
        $data['passportUrl'] = ApplicationHelper::getApplicantPassport();

        return view('admissions.applicants.applicationform', $data);
    }

    public function applicationcard(): View
    {
        $data = $this->getApplicantFormData();
        $data['passportUrl'] = ApplicationHelper::getApplicantPassport();

        return view('admissions.applicants.applicationcard', $data);
    }

    public function logout(): RedirectResponse
    {
        if (session()->has('user')) {
            Cache::forget("dashboard:" . session('user')['id']);
        }

        Session::flush();
        Auth::logout();

        return redirect()->route('admissions.starting')->with('error', 'You have been logged out');
    }

    private function getApplicantFormData(): array
    {
        $data = ApplicationHelper::getApplicanData();

        return [
            'biodetail'       => (object) $data->user,
            'olevelResults'   => (object) $this->applicationService->getOlevelResults()['data'],
            'jambResults'     => $this->applicationService->getJambResults()['data'],
            'schoolDetails'   => $this->applicationService->getSchool()['data'],
            'certificates'    => (object) $this->applicationService->getCertificates()['data'],
            'declaration'     => $this->applicationService->getDecalaration()['data'],
            'applicantStatus' => $data->stats,
        ];
    }

    private function handleSave(callable $action, string $route, string $failMessage): RedirectResponse
    {
        $response = $action();

        if ($response['success']) {
            $this->refreshCache();
            return redirect()->route($route)->with('success', $response['message']);
        }

        return redirect()->route($route)->with('error', $failMessage);
    }

    private function refreshCache(): void
    {
        $this->applicationService->refreshApplicantCache(session('user')['id'], session('access_token'));
    }
}
