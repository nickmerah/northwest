<?php

namespace App\Http\Controllers\Admissions;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Helpers\ApplicationHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Services\Admissions\ApplicationService;
use Illuminate\Routing\Controller as BaseController;


class ApplicantController extends BaseController
{
    protected $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    public function applicationhome(): View
    {
        $data = ApplicationHelper::getApplicanData();
        $applicantPayments = $data->applicationPaymentStatus;
        $applicantStatus = $data->stats;
        $applicantProgramme = $data->user['programme']['programme_id'];
        $applicantProgrammeType = $data->user['programmeType'];

        return view(
            'admissions.applicants.applicationhome',
            compact('applicantPayments', 'applicantStatus', 'applicantProgramme', 'applicantProgrammeType')
        );
    }

    public function biodata(): View
    {
        $data = ApplicationHelper::getApplicanData();
        $applicantPayments = $data->applicationPaymentStatus;
        $applicantStatus = $data->stats;
        $biodetail = (object) $data->user;
        $states = (object) $this->applicationService->states()['data']['data'];

        return view(
            'admissions.applicants.profile',
            compact('biodetail', 'applicantPayments', 'states', 'applicantStatus')
        );
    }

    public function savebiodata(Request $request)
    {
        $response = $this->applicationService->saveprofile($request);

        if ($response['success']) {

            $this->applicationService->refreshApplicantCache(session('user')['id'], session('access_token'));

            return redirect()->route('admissions.biodata')->with('success', $response['message']);
        }
        return redirect()->route('admissions.biodata')->with('error', 'Unable to update profile, try Again!');
    }

    public function olevel(): View
    {
        $olevelResults = (object) $this->applicationService->getOlevelResults()['data'];
        $olevelSubjects =   $this->applicationService->getOlevelSubjects()['data'];
        $olevelGrades =   $this->applicationService->getOlevelGrades();

        return view(
            'admissions.applicants.olevel',
            compact('olevelResults', 'olevelSubjects', 'olevelGrades')
        );
    }

    public function saveolevel(Request $request)
    {
        $response = $this->applicationService->saveolevel($request);

        if ($response['success']) {

            $this->applicationService->refreshApplicantCache(session('user')['id'], session('access_token'));

            return redirect()->route('admissions.olevel')->with('success', $response['message']);
        }
        return redirect()->route('admissions.olevel')->with('error', 'Unable to add  olevel results, try Again!');
    }

    public function jamb(): View
    {
        $jambResults =  $this->applicationService->getJambResults()['data'];
        $olevelSubjects =  $this->applicationService->getOlevelSubjects()['data'];

        return view(
            'admissions.applicants.jamb',
            compact('jambResults', 'olevelSubjects')
        );
    }

    public function savejamb(Request $request)
    {
        $response = $this->applicationService->savejamb($request);

        if ($response['success']) {

            $this->applicationService->refreshApplicantCache(session('user')['id'], session('access_token'));

            return redirect()->route('admissions.jamb')->with('success', $response['message']);
        }
        return redirect()->route('admissions.jamb')->with('error', 'Unable to add  jamb results, try Again!');
    }

    public function school(): View
    {
        $schoolDetails =  $this->applicationService->getSchool()['data'];

        return view('admissions.applicants.schoolattended', compact('schoolDetails'));
    }

    public function saveschool(Request $request)
    {
        $response = $this->applicationService->saveSchool($request);

        if ($response['success']) {

            $this->applicationService->refreshApplicantCache(session('user')['id'], session('access_token'));

            return redirect()->route('admissions.school')->with('success', $response['message']);
        }
        return redirect()->route('admissions.school')->with('error', 'Unable to add  school details, try Again!');
    }

    public function certupload(): View
    {
        $certificates =  (object) $this->applicationService->getCertificates()['data'];

        return view('admissions.applicants.uploadcertificate', compact('certificates'));
    }

    public function savecertupload(Request $request)
    {
        $response = $this->applicationService->uploadCertificates($request);

        if ($response['success']) {

            $this->applicationService->refreshApplicantCache(session('user')['id'], session('access_token'));

            return redirect()->route('admissions.certupload')->with('success', $response['message']);
        }
        return redirect()->route('admissions.certupload')->with('error', 'Unable to upload certificates, try Again!');
    }

    public function deletecertupload()
    {
        $response = $this->applicationService->deleteCertificates();

        if ($response['success']) {

            $this->applicationService->refreshApplicantCache(session('user')['id'], session('access_token'));
        }
        return redirect()->route('admissions.certupload');
    }

    public function declares(): View
    {
        $data = $this->getApplicantFormData();
        return view('admissions.applicants.declaration', $data);
    }

    public function savedeclares()
    {
        $response = $this->applicationService->saveDecalaration();

        if ($response['success']) {

            $this->applicationService->refreshApplicantCache(session('user')['id'], session('access_token'));

            return redirect()->route('admissions.myapplication')->with('success', $response['message']);
        }
        return redirect()->route('admissions.myapplication')->with('error', 'Unable to save declaration, try Again!');
    }

    public function applicationforms(): View
    {
        $data = ApplicationHelper::getApplicanData();
        $applicantStatus = $data->stats;

        return view('admissions.applicants.applicationforms', compact('applicantStatus'));
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

    public function logout()
    {
        if (session()->has('user')) {
            $userId = session('user')['id'];
            Cache::forget("dashboard:$userId");
        }

        Session::flush();
        Auth::logout();

        return redirect()->route('admissions.starting')->with('error', 'You have been logged out');
    }

    private function getApplicantFormData(): array
    {
        $data = ApplicationHelper::getApplicanData();

        return [
            'biodetail'     => (object) $data->user,
            'olevelResults' => (object) $this->applicationService->getOlevelResults()['data'],
            'jambResults'   => $this->applicationService->getJambResults()['data'],
            'schoolDetails' => $this->applicationService->getSchool()['data'],
            'certificates'  => (object) $this->applicationService->getCertificates()['data'],
            'declaration'   => $this->applicationService->getDecalaration()['data'],
            'applicantStatus' => $data->stats,
        ];
    }
}
