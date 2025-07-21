<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Applicant;
use App\Models\CTransaction;
use App\Models\RTransaction;
use App\Models\AppTransaction;
use App\Models\CurrentSession;
use App\Models\StdTransaction;
use App\Models\RemedialSession;
use App\Models\StdCurrentSession;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public const SCHOOLNAME = 'Delta State Polytechnic, Ogwashi-Uku';

    public function dashboard()
    {
        $app_year = CurrentSession::where('status', 'current')->get();
        $appyear = $app_year[0]->cs_session;
        $rem_year = RemedialSession::where('status', 'current')->get();
        $remyear = $rem_year[0]->cs_session;
        $std_year = StdCurrentSession::where('status', 'current')->get();
        $stdyear = $std_year[0]->cs_session;

        $app_count = Applicant::where(['appyear' => $appyear])->count();
        $apptotalpaid = AppTransaction::where(['trans_custom1' => 'Paid', 'trans_year' => $appyear])->get();
        $app_completed = Applicant::where(['std_custome9' => 1, 'appyear' => $appyear])->count();
        $schoolfeespaid = StdTransaction::where(['pay_status' => 'Paid', 'trans_year' => $stdyear, 'fee_type' => 'fees'])->get();
        $otherfeespaid = StdTransaction::where(['pay_status' => 'Paid', 'trans_year' => $stdyear, 'fee_type' => 'ofees'])->get();
        $clearancepaid = CTransaction::where(['trans_custom1' => 'Paid', 'trans_year' => $appyear])->get();
        $remedialpaid = RTransaction::where(['trans_custom1' => 'Paid', 'trans_year' => $remyear])->get();
        $appresultverificationpaid = AppTransaction::where(['trans_custom1' => 'Paid', 'trans_year' => $appyear, 'fee_id' => 4])->get();
        $appapplicationfeepaid = AppTransaction::where(['trans_custom1' => 'Paid', 'trans_year' => $appyear, 'fee_id' => 1])->get();
        $appacceptancefeepaid = AppTransaction::where(['trans_custom1' => 'Paid', 'trans_year' => $appyear, 'fee_id' => 2])->get();
        $servicecharge = AppTransaction::where(['trans_custom1' => 'Paid', 'trans_year' => $appyear, 'fee_id' => 5])->get();
         $feestotalpaid = $schoolfeespaid->merge($otherfeespaid);
        $students_verified = Student::count();

        $feestodaypaid = StdTransaction::where(['pay_status' => 'Paid', 'trans_year' => $stdyear, 't_date' => date('Y-m-d')])->get();
        $clearancetodaypaid = CTransaction::where(['trans_custom1' => 'Paid', 'trans_year' => $appyear, 't_date' => date('Y-m-d')])->get();
        $remedialtodaypaid = RTransaction::where(['trans_custom1' => 'Paid', 'trans_year' => $remyear, 't_date' => date('Y-m-d')])->get();

        return view('dashboard', compact(
            'app_count',
            'appyear',
            'apptotalpaid',
            'app_completed',
            'clearancepaid',
            'remedialpaid',
            'appresultverificationpaid',
            'appapplicationfeepaid',
            'appacceptancefeepaid',
            'schoolfeespaid',
            'otherfeespaid',
            'stdyear',
            'servicecharge',
            'feestotalpaid',
            'feestodaypaid',
            'students_verified',
            'clearancetodaypaid',
            'remedialtodaypaid'
        ));
    }
}
