<?php

namespace App\Http\Controllers\Admissions;

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

    public function applicationhome()
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

    public function biodata()
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

    public function olevel()
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

    public function jamb()
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

    public function school()
    {
        $jambResults =  $this->applicationService->getSchool()['data'];
        $olevelSubjects =  $this->applicationService->getOlevelSubjects()['data'];

        return view(
            'admissions.applicants.jamb',
            compact('jambResults', 'olevelSubjects')
        );
    }

    public function saveschool(Request $request)
    {
        $response = $this->applicationService->savejamb($request);

        if ($response['success']) {

            $this->applicationService->refreshApplicantCache(session('user')['id'], session('access_token'));

            return redirect()->route('admissions.jamb')->with('success', $response['message']);
        }
        return redirect()->route('admissions.jamb')->with('error', 'Unable to add  jamb results, try Again!');
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
}
