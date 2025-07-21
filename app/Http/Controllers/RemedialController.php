<?php

namespace App\Http\Controllers;

use App\Models\SchoolInfo;
use App\Models\RTransaction;
use Illuminate\Http\Request;
use App\Models\RemedialCourseReg;
use Illuminate\Support\Facades\DB;
use App\Traits\ValidatesRemedialUser;

class RemedialController extends Controller
{
    use ValidatesRemedialUser;

    protected $schoolInfo;

    protected $remedialstudent;

    public function __construct()
    {
        $this->schoolInfo = SchoolInfo::first();
        $this->middleware(function ($request, $next) {
            $response = $this->ValidateRemedialUser();
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }
            return $next($request);
        });
    }

    public function home()
    {
        $feesPaid = RTransaction::getPaidTransactions($this->remedialstudent->id);
        $courses = RemedialCourseReg::where("std_id", $this->remedialstudent->id)->get()->toArray();

        return view('remedial.dashboard', [
            'schoolName' => $this->schoolInfo->schoolname,
            'student' => $this->remedialstudent,
            'feesPaid' => $feesPaid,
            'noCourse' => count($courses),
        ]);
    }

    public function makepayment()
    {
        return view('remedial.selectcourse', [
            'schoolName' => $this->schoolInfo->schoolname,
            'student' => $this->remedialstudent,
        ]);
    }

    public function previewfee(Request $request)
    {
        $validatedData = $request->validate([
            'noCourse' => 'required|integer',
        ]);

        $noCourse = $validatedData['noCourse'];

        if ($noCourse < 1) {
            return back()->withErrors([
                'noCourse' => 'You must select a valid course',
            ]);
        }
        // let check if payment was made already
        $feesPaid = RTransaction::getPaidTransactions($this->remedialstudent->id);

        $CourseRegFees = $this->getCourseRegFees($noCourse);

        return view('remedial.fee', [
            'schoolName' => $this->schoolInfo->schoolname,
            'student' => $this->remedialstudent,
            'CourseRegFees' => $CourseRegFees,
            'feesPaid' => $feesPaid,
        ]);
    }

    public function phistory()
    {
        $data = $this->prepareTransactionData(function ($sid) {
            return RTransaction::getAllTransactions($sid);
        });

        return view('remedial.paymenthistory', $data);
    }

    private function prepareTransactionData($transactionFetcher)
    {
        $transactions = $transactionFetcher($this->remedialstudent->id)->toArray();

        $trans = json_decode(json_encode($transactions));


        return [
            'student' => $this->remedialstudent,
            'trans' => $trans,
            'schoolName' => $this->schoolInfo->schoolname,
        ];
    }

    public function printReceipt(int $transno)
    {
        $trans = RTransaction::getPaidTransaction($transno)->toArray();

        return view('remedial.paymentreceipt', [
            'student' => $this->remedialstudent,
            'trans' => $trans,
            'schoolName' => $this->schoolInfo->schoolname,
        ]);
    }

    public function viewfees(int $rrr)
    {
        $trans = RTransaction::where(['rrr' => $rrr, 'trans_custom1' => 'Pending'])->get();
        if ($trans->isEmpty()) {
            return redirect('/makepayment')->with('error', 'Transaction not found.');
        }

        return view('remedial.viewfee', [
            'student' => $this->remedialstudent,
            'schoolName' => $this->schoolInfo->schoolname,
            'trans' => $trans
        ]);
    }

    public function viewsfees(int $rrr)
    {
        $trans = RTransaction::where(['rrr' => $rrr, 'pay_status' => 'Pending'])->get();
        if ($trans->isEmpty()) {
            return redirect('/ofees')->with('error', 'Transaction not found.');
        }

        return view('remedial.viewsfee', [
            'student' => $this->remedialstudent,
            'schoolName' => $this->schoolInfo->schoolname,
            'trans' => $trans
        ]);
    }

    public static function getCourseRegFees(int $noCourses)
    {
        $fees = DB::table('rfield')->get();

        $courseFee = 0;
        $additionalFee = 0;
        $serviceCharge = 0;

        foreach ($fees as $fee) {
            if ($fee->field_name === 'Course Fee') {
                $courseFee = $fee->amount;
                $courseFeeId = $fee->field_id;
            } elseif ($fee->field_name === 'Additional Fee') {
                $additionalFee = $fee->amount;
                $additionalFeeId = $fee->field_id;
            } elseif ($fee->field_name === 'Service Charge') {
                $serviceCharge = $fee->amount;
                $serviceChargeId = $fee->field_id;
            }
        }

        $additionalFeePerCourse = 0;
        if ($noCourses > 3) {
            $additionalFeePerCourse = ($noCourses - 3) * $additionalFee;
        }
        $total = $courseFee + $additionalFeePerCourse + $serviceCharge;

        $feeDetails = [
            'Course Fee' => [
                'id' => $courseFeeId,
                'amount' => $courseFee,
            ],
            'Additional Fee' => [
                'id' => $additionalFeeId,
                'amount' => $additionalFeePerCourse, // Total additional amount (for courses beyond 3)
            ],
            'Service Charge' => [
                'id' => $serviceChargeId,
                'amount' => $serviceCharge,
            ],
        ];

        return [
            'noCourses' => $noCourses,
            'total' => $total,
            'feeDetails' => $feeDetails,
        ];
    }

    public function coursereg()
    {
        // let check if payment is made
        $feesPaid = RTransaction::getPaidTransactions($this->remedialstudent->id);
        $noCourse = 0;

        if ($feesPaid->isEmpty()) {
            return redirect('/makepayment')->with('error', 'You have to pay the fees before course registraion.');
        }

        $noCourse = array_sum(array_column($feesPaid->toArray(), 'course'));


        $regcourse =  RemedialCourseReg::where([
            'std_id' => $this->remedialstudent->id,
            'clevel_id' => $this->remedialstudent->level,
            'cyearsession' => $this->remedialstudent->sess,
        ])->get();

        return view('remedial.coursereg', [
            'student' => $this->remedialstudent,
            'schoolName' => $this->schoolInfo->schoolname,
            'noCourse' => $noCourse,
            'regcourse' => $regcourse
        ]);
    }

    public function regcourse(Request $request)
    {
        // let check if payment is made
        $feesPaid = RTransaction::getPaidTransactions($this->remedialstudent->id);

        if ($feesPaid->isEmpty()) {
            return redirect('/makepayment')->with('error', 'You have to pay the fees before course registraion.');
        }

        $noCourses = array_sum(array_column($feesPaid->toArray(), 'course'));


        $request->validate([
            'coursecode' => 'required|array|min:1',
            'coursecode.*' => 'required|string',
        ]);

        $courseCodes = $request->input('coursecode');

        $courseCodes = array_map(function ($code) {
            return str_replace(' ', '', trim($code));
        }, $courseCodes);

        $duplicates = array_filter(array_count_values($courseCodes), function ($count) {
            return $count > 1;
        });

        if (!empty($duplicates)) {
            $duplicateCodes = array_keys($duplicates);

            return redirect()->back()->withErrors(['error' => 'Duplicate course codes found: ' . implode(', ', $duplicateCodes)]);
        }

        $courseCodes = array_map(function ($code) {
            return str_replace(' ', '', trim($code));
        }, $courseCodes);


        if ($noCourses !== count($courseCodes)) {
            return redirect('/rcourses')->with('error', 'Number of Courses paid for is different from number to register.');
        }

        RemedialCourseReg::where([
            'std_id' => $this->remedialstudent->id,
            'clevel_id' => $this->remedialstudent->level,
            'cyearsession' => $this->remedialstudent->sess,
        ])->delete();

        foreach ($courseCodes as $courseCode) {
            $upperCaseCode = strtoupper($courseCode);
            $upperCaseCode = preg_replace('/([a-zA-Z]+)(\d+)/', '$1 $2', $upperCaseCode);
            $exists = RemedialCourseReg::where([
                'std_id' => $this->remedialstudent->id,
                'clevel_id' => $this->remedialstudent->level,
                'cyearsession' => $this->remedialstudent->sess,
                'c_code' => $upperCaseCode,
            ])->exists();

            if (!$exists) {
                RemedialCourseReg::create([
                    'std_id' => $this->remedialstudent->id,
                    'clevel_id' => $this->remedialstudent->level,
                    'cyearsession' => $this->remedialstudent->sess,
                    'c_code' => $upperCaseCode,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Course codes registered or updated successfully.');
    }

    public function printCourse()
    {
        $courses = RemedialCourseReg::where("std_id", $this->remedialstudent->id)->get()->toArray();

        return view('remedial.courses', [
            'student' => $this->remedialstudent,
            'courses' => $courses,
            'schoolName' => $this->schoolInfo->schoolname,
        ]);
    }
}
