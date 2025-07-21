<?php

namespace App\Http\Controllers;

use App\Models\CourseReg;
use App\Models\Courses;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Models\HostelRoomAllocation;
use App\Models\OFee;
use App\Models\SchoolInfo;
use App\Models\StdSession;
use App\Models\STransaction;
use App\Models\StudentLogin;
use App\Models\StudentProfile;
use App\Services\FeeCalculationService;
use App\Services\FeeService;
use App\Traits\ValidatesPortalUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PortalController extends Controller
{
    use ValidatesPortalUser;

    private const SERVICE_CHARGE = 300;
    private const MAX_UNIT_TO_REGISTER = 40;
    protected $schoolInfo;
    protected $student;
    protected $currentSessionSem;
    protected $currentSession;
    protected $currentSemester;
    protected $feeCalculationService;

    public function __construct(FeeCalculationService $feeCalculationService)
    {
        $this->schoolInfo = SchoolInfo::first();
        $this->feeCalculationService = $feeCalculationService;

        $this->middleware(function ($request, $next) {
            if ($response = $this->validatePortalUser()) {
                return $response;
            }

            if ($response = $this->ensureStudentIsProfiled()) {
                return $response;
            }

            $this->currentSession = $this->currentSessionSem['cs_session'];
            $this->currentSemester = $this->currentSessionSem['cs_sem'];

            view()->share([
                'student' => $this->student,
                'currentSession' => $this->currentSession,
                'schoolName' => $this->schoolInfo->schoolname,
            ]);

            return $next($request);
        });
    }

    private function ensureStudentIsProfiled()
    {
        $excludedPaths = ['updateprofile', 'passport', 'updatepassport', 'profile'];

        if (in_array(request()->path(), $excludedPaths)) {
            return null;
        }

        $photoPath = storage_path('app/public/passport/' . $this->student->std_photo);

        if (!file_exists($photoPath)) {
            return redirect('/passport')->with('error', 'Upload Passport to Continue');
        }

        if ($this->student->hometown == "") {
            return redirect('/profile')->with('error', 'Your Home Town is missing in your profile, Kindly Update it.');
        }

        return null;
    }

    public function home()
    {
        $schoolfeespaid = STransaction::where(['log_id' => $this->student->std_logid, 'pay_status' => 'Paid', 'trans_year' => $this->currentSession, 'fee_type' => 'fees'])->get();
        $otherfeespaid = STransaction::where(['log_id' => $this->student->std_logid, 'pay_status' => 'Paid', 'trans_year' => $this->currentSession, 'fee_type' => 'ofees'])->get();

        return view('portal.dashboard', compact(
            'schoolfeespaid',
            'otherfeespaid',
        ));
    }

    public function biodata()
    {
        return view('portal.profile');
    }

    public function passport()
    {
        return view('portal.passport');
    }

    public function biodataupdate(Request $request)
    {
        $validatedData = $request->validate([
            'marital_status' => 'required|string',
            'contact_address' => 'required|string',
            'student_homeaddress' => 'required|string',
            'std_genotype' => 'required|string',
            'std_bloodgrp' => 'required|string',
            'hometown' => 'required|string',
            'student_email' => 'required|email',
            'student_mobiletel' => 'required|string',
            'next_of_kin' => 'required|string',
            'nok_tel' => 'required|numeric',
            'nok_address' => 'required|string',
        ]);


        $sanitizedData = [
            'marital_status' => strip_tags($validatedData['marital_status']),
            'contact_address' => strtoupper(strip_tags($validatedData['contact_address'])),
            'student_homeaddress' => strtoupper(strip_tags($validatedData['student_homeaddress'])),
            'std_genotype' => $validatedData['std_genotype'],
            'std_bloodgrp' => $validatedData['std_bloodgrp'],
            'nationality' => 'NIGERIA',
            'hometown' => strtoupper(strip_tags($validatedData['hometown'])),
            'student_email' => strtolower(strip_tags($validatedData['student_email'])),
            'student_mobiletel' => strip_tags($validatedData['student_mobiletel']),
            'next_of_kin' => strtoupper(strip_tags($validatedData['next_of_kin'])),
            'nok_tel' => strip_tags($validatedData['nok_tel']),
            'nok_address' => strtoupper(strip_tags($validatedData['nok_address'])),
        ];

        $studentProfile = StudentProfile::findOrFail($this->student->std_id);

        $studentProfile->update($sanitizedData);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function passportupdate(Request $request)
    {
        // Validate the request
        $request->validate([
            'passport' => 'required|image|mimes:jpeg,jpg|max:100', // Max size is 100 KB
        ]);

        // Check and process the uploaded file
        if ($request->hasFile('passport')) {
            $file = $request->file('passport');
            $fileName = $this->student->std_logid . '.' . $file->getClientOriginalExtension();
            $storagePath = storage_path('app/public/passport/');

            // Move the file to the target location
            $file->move($storagePath, $fileName);

            // Update the student's photo path in the database
            $this->student->std_photo = $fileName;
            $this->student->save();

            return back()->with('success', 'Passport updated successfully.');
        }

        return back()->with('error', 'Failed to upload passport.');
    }

    public function ofee()
    {
        $ofees = OFee::where('of_status', 1)
            ->where(function ($query) {
                $query->where('of_prog', $this->student->stdprogramme_id)
                    ->orWhere('of_prog', 0);
            })
            ->get();
        return view('portal.ofee', [
            'ofees' => $ofees,
        ]);
    }

    public function previewofee(Request $request)
    {
        if (!$request->has('ofee')) {
            return back()->withErrors('Select any least on Fee to make payment');
        }

        $selectedFees = $request->input('ofee');

        if (!is_array($selectedFees)) {
            $selectedFees = [$selectedFees];
        }

        $ofees = OFee::whereIn('of_id', $selectedFees)->get();

        $libraryBindingCopies = $request->input('copies', 0);

        $calculation = $this->feeCalculationService->calculateFees($selectedFees, $libraryBindingCopies);

        // Final total including service charge
        $grandTotal = $calculation['grandTotal'];

        return view('portal.ofeep', [
            'schoolName' => $this->schoolInfo->schoolname,
            'student' => $this->student,
            'ofees' => $ofees,
            'bindingamount' => $calculation['totalBindingFee'] ?? 0,
            'libraryBindingCopies' => $libraryBindingCopies ?? 0,
            'totalAmount' => $calculation['totalAmount'],
            'grandTotal' => $grandTotal
        ]);
    }

    public function viewOfees(int $rrr)
    {
        $trans = STransaction::where(['rrr' => $rrr, 'pay_status' => 'Pending'])->get();
        if ($trans->isEmpty()) {
            return redirect('/ofees')->with('error', 'Transaction not found.');
        }

        return view('portal.viewofee', [
            'trans' => $trans,
        ]);
    }

    public function fee()
    {
        if ($this->student->state_of_origin == 0) {
            return redirect('/profile')->with('error', 'Your State of Origin is missing in your profile, contact Admin to Update it.');
        }

        $feeService = new FeeService($this->student);
        $fees = $feeService->getStudentFees();
        return view('portal.fee', [
            'fees' => $fees,
        ]);
    }

    public function phistory()
    {
        $data = $this->prepareTransactionData(function ($sid) {
            return STransaction::getAllTransactions($sid);
        });

        return view('portal.paymenthistory', $data);
    }

    private function prepareTransactionData($transactionFetcher)
    {
        $transactions = $transactionFetcher($this->student->std_logid)->toArray();

        $trans = json_decode(json_encode($transactions));


        return [
            'student' => $this->student,
            'trans' => $trans,
            'schoolName' => $this->schoolInfo->schoolname,
        ];
    }

    public function printReceipt(int $transno)
    {

        $trans = STransaction::getPaidTransaction($transno)->toArray();


        if ($this->isNewStudent($trans)) {
            $matNo = $this->generateMatricNo();

            if ($matNo) {
                $login = StudentLogin::findOrFail($this->student->std_logid);

                $this->student->update([
                    'matset' => $this->student->matric_no,
                    'matric_no' => $matNo,
                ]);

                $login->update([
                    'log_username' => $matNo,
                ]);
            } else {
                return redirect('/pfhistory')->with('error', 'Error Generating Matric No');
            }
        }

        // attempt to get student ID from past record
        $studentId = self::getStudentId();

        return view('portal.paymentreceipt', [
            'trans' => $trans,
            'studentId' => $studentId,
        ]);
    }

    private function isNewStudent(array $trans): bool
    {
        $checkNew = DB::table('jprofile')
            ->where("app_no", $this->student->matric_no)
            ->exists();

        return $trans[0]['fee_id'] == 1
            && $trans[0]['fee_type'] == 'fees'
            && $checkNew
            && ($this->student->matset == "" || $this->student->matset == "0");
    }

    private function generateMatricNo(): ?string
    {
        $matNo = DB::table('matcode')
            ->where([
                "doid" => $this->student->stdcourse,
                "progtype" => $this->student->stdprogrammetype_id
            ])
            ->pluck('matno')
            ->first();

        //$matNo = "WED/ND/23/00131";

        if (!$matNo) {
            //return false;
            // lets try to add the first entry in the matcode table

            $matNo = $this->addStartingMatricNumber();
        }

        // Check if matric number already exists
        $checkMat = StudentProfile::where("matric_no", $matNo)->exists();

        if ($checkMat) {
            return $this->incrementMatricNo($matNo);
        }

        return $matNo;
    }

    private function addStartingMatricNumber(): string|bool
    {
        $matNo = DB::table('dept_options')
            ->where([
                "do_id" => $this->student->stdcourse,
                "prog_id" => $this->student->stdprogramme_id
            ])
            ->pluck('deptcode')
            ->first();

        $prefix = self::getValueAfterND($matNo);
        $progAbbrev = $this->student->programme->aprogramme_name;
        $sess = substr(StdSession::getStdCurrentSession()['cs_session'], -2);
        $counter = str_pad(($this->student->stdprogrammetype_id == 1) ? 1 : 10001, 5, '0', STR_PAD_LEFT);

        $formedMatNo = "{$prefix}/{$progAbbrev}/{$sess}/{$counter}";

        $exists = DB::table('matcode')
            ->where('matno', $formedMatNo)
            ->exists();

        if (!$exists) {
            DB::table('matcode')->insert([
                'coscode' => "{$progAbbrev}{$prefix}",
                'matno' => $formedMatNo,
                'doid' => $this->student->stdcourse,
                'progtype' => $this->student->stdprogrammetype_id,
                'status' => 0,

            ]);

            return $formedMatNo;
        }

        return false;
    }

    function getValueAfterND($input)
    {
        return preg_replace('/^(HND|ND)/', '', $input);
    }

    private function incrementMatricNo(string $matNo): string
    {
        // Validate the format
        if (!preg_match('/^[A-Z]{3}\/[A-Z]{2,3}\/\d{2}\/\d+$/', $matNo)) {
            return '';
        }

        // Extract the prefix and increment the number
        //  echo $qprefix = implode('/', array_slice(explode('/', $matNo), 0, 3)) . '/';
        $prefix = implode('/', array_slice(explode('/', $matNo), 0, 3)) . '/';

        $parts = explode('/', $matNo);
        $parts[2] = (int)$parts[2] - 1; // Convert to integer, subtract 1
        $qprefix = implode('/', array_slice($parts, 0, 3)) . '/';

        // Get the last record in the db where matric_no starts with the prefix
        $lastPrefixMatNo = StudentProfile::where('matric_no', 'like', $prefix . '%')
            ->where('stdprogramme_id', $this->student->stdprogramme_id)
            ->where('stdprogrammetype_id', $this->student->stdprogrammetype_id)
            ->orderByRaw('CAST(SUBSTRING_INDEX(matric_no, "/", -1) AS UNSIGNED) DESC')
            ->first();

        // Get the last record in the db where matric_no starts with the qprefix
        $lastQPrefixMatNo = StudentProfile::where('matric_no', 'like', $qprefix . '%')
            ->where('stdprogramme_id', $this->student->stdprogramme_id)
            ->where('stdprogrammetype_id', $this->student->stdprogrammetype_id)
            ->where('is_repeating', 1)
            ->orderByRaw('CAST(SUBSTRING_INDEX(matric_no, "/", -1) AS UNSIGNED) DESC')
            ->first();


        $lastPDigits = explode('/', $lastPrefixMatNo->matric_no)[3];
        $lastQDigits = $lastQPrefixMatNo?->matric_no ? explode('/', $lastQPrefixMatNo->matric_no)[3] ?? 0 : 0;

        $nextDigit = max($lastPDigits, $lastQDigits);

        $nextDigits = str_pad((int)$nextDigit + 1, strlen($nextDigit), '0', STR_PAD_LEFT);

        return $prefix . $nextDigits;
    }

    private function getStudentId(): ?string
    {

        if (empty($this->student)) {
            return null;
        }

        $studentId = $this->student->cs_status;

        if (empty($studentId)) {
            $studentId = DB::table('stdaccess')
                ->where("matno", $this->student->matric_no)
                ->value('stdno');
        }

        // sometimes the studentID isn't updated for new students, so we check again
        if (empty($studentId) and $this->student->matset != 0) {
            $studentId = DB::table('jprofile')
                ->where("app_no", $this->student->matset)
                ->value('student_id');
        }

        if ($this->student->cs_status == 0) {
            // attempt to update it on the student table

            $this->student->cs_status = substr($studentId, 0, 10);
            $this->student->save();
        }


        return $studentId;
    }

    public function previewfee()
    {
        $feeService = new FeeService($this->student);
        $fees = $feeService->getStudentFees()->toArray();
        $paybalance = false;
        $feesToPay = $feeService->getStudentCompulsoryAndRemainingFees($fees);

        if (empty($feesToPay)) {
            $checkSchoolFeesCompleted = $feeService->checkSchoolFeesCompletePaid();
            if ($checkSchoolFeesCompleted) {
                return redirect('/fees')->with('error', 'Fees already Paid, Proceed to print your receipt.');
            }
            $paybalance = true;
            $feesToPay = $feeService->getStudentBalanceFees($fees);
        }

        return view('portal.pfee', [
            'fees' => $feesToPay,
            'paybalance' => $paybalance,
        ]);
    }

    public function viewfees(int $rrr)
    {
        $trans = STransaction::where(['rrr' => $rrr, 'pay_status' => 'Pending'])->get();
        if ($trans->isEmpty()) {
            return redirect('/fees')->with('error', 'Transaction not found.');
        }

        return view('portal.viewfee', [
            'trans' => $trans,
            'serviceCharge' => self::SERVICE_CHARGE
        ]);
    }

    public function viewsfees(int $rrr)
    {
        $trans = STransaction::where(['rrr' => $rrr, 'pay_status' => 'Pending'])->get();
        if ($trans->isEmpty()) {
            return redirect('/ofees')->with('error', 'Transaction not found.');
        }

        return view('portal.viewsfee', [
            'trans' => $trans
        ]);
    }

    public function sfee()
    {
        $feeService = new FeeService($this->student);
        $allfees = $feeService->getStudentPreviousFees();

        $feeAmount = $feeService->getStudentFeeExclusion();

        if ($feeAmount != 0 || $feeAmount == -1) {
            return redirect('/fees')->with('error', 'You have not been enabled to pay this fee.');
        }


        $fees = $allfees->filter(function ($item) {
            return $item->group === 0;
        });

        return view('portal.sfee', [
            'fees' => $fees,
        ]);
    }

    public function bfee()
    {
        $feeService = new FeeService($this->student);
        $allfees = $feeService->getStudentPreviousFees();

        $fees = $allfees->filter(function ($item) {
            return $item->group === 0;
        });

        $feeAmount = $feeService->getStudentFeeExclusion();

        if ($feeAmount == 0 || $feeAmount == -1) {
            return redirect('/fees')->with('error', 'You have not been enabled to pay this fee.');
        }

        return view('portal.bfee', [
            'fees' => $fees,
            'balance' => $feeAmount,
        ]);
    }

    public function hostel()
    {
        $paidHostel = $this->getPaidHostel();

        $hostels = Hostel::where(['gender' => $this->student->gender, 'hid' => $paidHostel[0]->policy])
            ->with(['rooms' => function ($query) {
                $query->whereDoesntHave('allocations', function ($subquery) {
                    $subquery->select('room_id')
                        ->from('hostelroom_allocations')
                        ->groupBy('room_id')
                        ->havingRaw('COUNT(*) >= MAX(capacity)');
                });
            }])->get();

        return view('portal.availability', [
            'hostels' => $hostels,

        ]);
    }

    public function getPaidHostel()
    {
        return STransaction::where([
            'log_id' => $this->student->std_logid,
            'pay_status' => 'Paid',
            'fee_type' => 'ofees'
        ])
            ->whereIn('fee_id', [6, 7])
            ->get();
    }

    public function roomAvailability()
    {
        //check if hostel is paid

        $paidHostel = $this->getPaidHostel();

        if ($paidHostel->isEmpty()) {
            return redirect()->route('hostelpayment')->with('error', 'You must pay for accomodation before you can reserve a room');
        }

        $hostels = Hostel::where(['gender' => $this->student->gender, 'hid' => $paidHostel[0]->policy])
            ->with(['rooms' => function ($query) {
                $query->where('room_status', 1)->whereDoesntHave('allocations', function ($subquery) {
                    $subquery->select('room_id')
                        ->from('hostelroom_allocations')
                        ->groupBy('room_id')
                        ->havingRaw('COUNT(*) >= MAX(capacity)');
                });
            }])->get();

        $allBookedIdsWithRoomIds = $this->getAllBookedIdsWithRoomIds();
        $stdLogid = $this->student->std_logid;

        $matchingRoom = $allBookedIdsWithRoomIds->first(function ($entry) use ($stdLogid) {
            return in_array($stdLogid, $entry['booked']);
        });

        if ($matchingRoom) {
            $roomid = $matchingRoom['roomid'];

            $hostels = Hostel::where(['gender' => $this->student->gender, 'hid' => $paidHostel[0]->policy])
                ->with(['rooms' => function ($query) use ($roomid) {
                    $query->where('roomid', $roomid)->whereDoesntHave('allocations', function ($subquery) {
                        $subquery->select('room_id')
                            ->from('hostelroom_allocations')
                            ->groupBy('room_id')
                            ->havingRaw('COUNT(*) >= MAX(capacity)');
                    });
                }])->get();
        }


        return view('portal.availability', [
            'hostels' => $hostels,

        ]);
    }

    public function getAllBookedIdsWithRoomIds()
    {
        return HostelRoom::pluck('booked', 'roomid')
            ->filter(function ($value) {
                return !empty($value); // Ensure the value isn't empty
            })
            ->map(function ($value, $key) {
                $value = trim($value, '"'); // Trim any unnecessary quotes
                $decoded = json_decode($value);

                // Fetch the hostelid for the given roomid
                $hostelid = HostelRoom::where('roomid', $key)->value('hostelid');

                return [
                    'roomid' => $key,
                    'hostelid' => $hostelid,
                    'booked' => $decoded,
                ];
            })
            ->filter(function ($value) {
                return !empty($value['booked']); // Ensure booked values are not empty
            })
            ->values(); // Re-index the array to ensure proper collection indexing
    }

    public function allocateRoom($roomId)
    {
        $room = HostelRoom::findOrFail($roomId);

        // Check if there is space in the room
        if (!$room->hasSpace()) {
            return redirect()->back()->with('error', 'Room is fully occupied');
        }

        // Check if std_logid is already allocated to this room
        $existingAllocation = $room->allocations()->where('std_logid', $this->student->std_logid)->first();
        if ($existingAllocation) {
            return redirect()->back()->with('error', 'Student is already allocated to this room');
        }

        // Check if std_logid is already allocated to any room
        $existingRoomAllocation = HostelRoomAllocation::where('std_logid', $this->student->std_logid)->first();
        if ($existingRoomAllocation) {
            return redirect()->back()->with('error', 'Student is already allocated to another room');
        }

        // Allocate the room to the student
        $room->allocations()->create(['std_logid' => $this->student->std_logid]);

        return redirect()->route('reserveRoom')->with('success', 'Room allocated successfully!');
    }

    public function reservedRoom()
    {
        $myreservation = HostelRoomAllocation::with(['room.hostel'])
            ->where('std_logid', $this->student->std_logid)
            ->get();

        return view('portal.myroom', [
            'myreservation' => $myreservation,

        ]);
    }

    public function makeHostelPayment()
    {
        //check if hostel is paid

        $paidHostel = $this->getPaidHostel();

        if ($paidHostel->isNotEmpty()) {
            return redirect()->route('reserveRoom')->with('error', 'You already made payment for hostel.');
        }

        $hostels = Hostel::where(['gender' => $this->student->gender, 'hostel_status' => 1])
            ->with(['rooms' => function ($query) {
                $query->where('room_status', 1)->whereDoesntHave('allocations', function ($subquery) {
                    $subquery->select('room_id')
                        ->from('hostelroom_allocations')
                        ->groupBy('room_id')
                        ->havingRaw('COUNT(*) >= MAX(capacity)');
                });
            }, 'ofee'])->get();


        $allBookedIdsWithRoomIds = $this->getAllBookedIdsWithRoomIds();
        $stdLogid = $this->student->std_logid;

        $matchingRoom = $allBookedIdsWithRoomIds->first(function ($entry) use ($stdLogid) {
            return in_array($stdLogid, $entry['booked']);
        });

        if ($matchingRoom) {
            $roomid = $matchingRoom['roomid'];
            $hostelid = $matchingRoom['hostelid'];

            $hostels = Hostel::where(['gender' => $this->student->gender, 'hid' => $hostelid])
                ->with(['rooms' => function ($query) use ($roomid) {
                    $query->where('roomid', $roomid)
                        ->whereDoesntHave('allocations', function ($subquery) {
                            $subquery->select('room_id')
                                ->from('hostelroom_allocations')
                                ->groupBy('room_id')
                                ->havingRaw('COUNT(*) >= MAX(capacity)');
                        });
                }, 'ofee'])->get();
        }


        return view('portal.payhostelfee', [
            'hostels' => $hostels,

        ]);
    }

    public function printHostelReservation()
    {
        $myreservation = HostelRoomAllocation::with(['room.hostel'])
            ->where('std_logid', $this->student->std_logid)
            ->get();

        return view('portal.hostelreservation', [
            'myreservation' => $myreservation,
        ]);
    }

    public function bpfee()
    {
        return view('portal.bpfee');
    }

    public function pfee(Request $request)
    {

        if (!$request->has('psess')) {
            return back()->withErrors('Select any least a Session to make payment');
        }
        $psess = $request->psess;

        $feeService = new FeeService($this->student);
        $fees = $feeService->getStudentPreviousFees();
        return view('portal.ppfee', [
            'fees' => $fees,
            'psess' => $psess,
        ]);
    }

    public function previewpfee(Request $request)
    {
        if (!$request->has('psess')) {
            return back()->withErrors('Select any least a Session to make payment');
        }
        $psess = $request->psess;

        $feeService = new FeeService($this->student);
        $fees = $feeService->getStudentPreviousFees()->toArray();
        $pfees = $feeService->getStudentPreviousFeesToPay($fees, $psess);

        if (empty($pfees)) {
            return redirect('/bpfee')->with('error', 'Fees already Paid Selected Session, Proceed to print your receipt.');
        }

        return view('portal.ppfees', [
            'fees' => $pfees,
            'psess' => $psess,
        ]);
    }

    public function getcourses()
    {
        // Check fee eligibility
        $isEligible = STransaction::isEligibleGorCourseReg($this->student->std_logid, $this->student->stdprogramme_id);

        if ($isEligible->isEmpty()) {
            return redirect()->route('fees')->with('error', 'You have to pay the complete fees before course registration.');
        }

        $level = $this->student->stdlevel;
        $baseConditions = [
            'stdcourse' => $this->student->stdcourse,
            'prog' => $this->student->stdprogramme_id,
            'prog_type' => $this->student->stdprogrammetype_id,
        ];

        // Fetch main courses
        $courses = Courses::where(array_merge($baseConditions, ['levels' => $level]))->get();

        // Determine carryover level and fetch courses
        $carryoverCourses = collect(); // default empty collection
        if (in_array($level, [2, 4])) {
            $carryoverLevel = $level - 1;
            $carryoverCourses = Courses::where(array_merge($baseConditions, ['levels' => $carryoverLevel]))->get();
        }

        // Get saved course codes
        $savedCourseCodes = self::getSavedCourses()->pluck('c_code')->toArray();

        // Filter out already saved courses
        $filteredCourses = $courses->reject(fn($course) => in_array($course->thecourse_code, $savedCourseCodes));
        $filteredCarryoverCourses = $carryoverCourses->reject(fn($course) => in_array($course->thecourse_code, $savedCourseCodes));

        // Group by semester
        $firstSemesterCourses = $this->filterCoursesBySemester($filteredCourses, 'First Semester');
        $secondSemesterCourses = $this->filterCoursesBySemester($filteredCourses, 'Second Semester');
        $firstSemesterCarryOverCourses = $this->filterCoursesBySemester($filteredCarryoverCourses, 'First Semester');
        $secondSemesterCarryOverCourses = $this->filterCoursesBySemester($filteredCarryoverCourses, 'Second Semester');

        return view('portal.courses', compact(
            'firstSemesterCourses',
            'secondSemesterCourses',
            'firstSemesterCarryOverCourses',
            'secondSemesterCarryOverCourses'
        ));
    }

    private function getSavedCourses()
    {
        $sessSem = StdSession::getStdCurrentSession();

        return CourseReg::where([
            'log_id' => $this->student->std_logid,
            'cyearsession' => $sessSem['cs_session'],
        ])->get();
    }

    protected function filterCoursesBySemester($courses, $semester, $semColumn = "semester")
    {
        return $courses->filter(function ($course) use ($semester, $semColumn) {
            return $course->$semColumn === $semester;
        });
    }

    public function previewcourse(Request $request)
    {
        $request->validate([
            'courseids' => 'required'
        ], [
            'courseids.required' => 'No courses selected for registration',
        ]);


        $selectedCourses = $request->courseids;

        if (empty($selectedCourses)) {
            return redirect('/courses')->with('error', 'No courses selected for registration');
        }

        $courses = Courses::whereIn('thecourse_id', $selectedCourses)->get();

        $savedCourses = self::getSavedCourses();


        $firstSemesterCourses = $this->filterCoursesBySemester($courses, 'First Semester');
        $secondSemesterCourses = $this->filterCoursesBySemester($courses, 'Second Semester');

        $firstSemesterRegisterUnits = $savedCourses
            ->where('csemester', 'First Semester')
            ->pluck('c_unit')
            ->sum();

        $secondSemesterRegisterUnits = $savedCourses
            ->where('csemester', 'Second Semester')
            ->pluck('c_unit')
            ->sum();


        return view('portal.previewcourses', [
            'firstSemesterCourses' => $firstSemesterCourses,
            'secondSemesterCourses' => $secondSemesterCourses,
            'firstSemesterRegisterUnits' => $firstSemesterRegisterUnits,
            'secondSemesterRegisterUnits' => $secondSemesterRegisterUnits,
            'maxUnitToRegister' => self::MAX_UNIT_TO_REGISTER,
        ]);
    }

    public function savecourses(Request $request)
    {
        $request->validate([
            'courseids' => 'required'
        ], [
            'courseids.required' => 'No courses selected for registration',
        ]);

        $selectedCourses = $request->courseids;

        if (empty($selectedCourses)) {
            return redirect('/courses')->with('error', 'No courses selected for registration');
        }

        $courses = Courses::whereIn('thecourse_id', $selectedCourses)->get();
        $sessSem = STransaction::getCurrentSemesterSession();

        try {
            foreach ($courses as $course) {
                $conditions = [
                    'log_id' => $this->student->std_logid,
                    'cyearsession' => $sessSem['cs_session'],
                    'csemester' => $course->semester,
                    'thecourse_id' => $course->thecourse_id,
                ];

                $attributes = [
                    'c_unit' => $course->thecourse_unit,
                    'clevel_id' => $this->student->stdlevel,
                    'cdate_reg' => date('Y-m-d'),
                    'c_title' => $course->thecourse_title,
                    'c_code' => $course->thecourse_code,
                ];

                CourseReg::updateOrInsert($conditions, $attributes);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while registering courses.');
        }


        return redirect('/viewcourse')->with('success', 'Courses Registered Successfully');
    }

    public function viewcourses()
    {

        $isEligibleGorCourseReg = STransaction::isEligibleGorCourseReg($this->student->std_logid, $this->student->stdprogramme_id);

        if ($isEligibleGorCourseReg->isEmpty()) {

            return redirect()->route('fees')->with('error', 'You have to pay the complete fees before course registration.');
        }

        $savedCourses = self::getSavedCourses();

        $firstSemesterCourses = $this->filterCoursesBySemester($savedCourses, 'First Semester', 'csemester');
        $secondSemesterCourses = $this->filterCoursesBySemester($savedCourses, 'Second Semester', 'csemester');

        return view('portal.viewcourses', [
            'firstSemesterCourses' => $firstSemesterCourses,
            'secondSemesterCourses' => $secondSemesterCourses,
            'sess' => StdSession::getStdCurrentSession()['cs_session'],
        ]);
    }

    public function removecourses(Request $request)
    {
        $request->validate([
            'courseids' => 'required'
        ], [
            'courseids.required' => 'No courses selected for deletion',
        ]);

        $selectedCourses = $request->courseids;

        if (empty($selectedCourses)) {
            return redirect('/viewcourse')->with('error', 'No courses selected for deletion');
        }

        CourseReg::whereIn('stdcourse_id', $selectedCourses)->delete();

        return redirect('/viewcourse')->with('success', 'Selected courses deleted successfully');
    }

    public function coursereghistory()
    {
        $data = CourseReg::getCourseRegistrations($this->student->std_logid);

        return view('portal.creghistory', [
            'data' => $data,
        ]);
    }

    public function printCourseReg(int $session)
    {
        $regCourses = CourseReg::where([
            'log_id' => $this->student->std_logid,
            'cyearsession' => $session,
            'status' => "Approved",
        ])->get();

        if ($regCourses->isEmpty()) {
            return redirect('/creghistory')->with('error', 'Registered Courses are not yet approved by the Course Adviser');
        }

        $firstSemesterCourses = $this->filterCoursesBySemester($regCourses, 'First Semester', 'csemester');
        $secondSemesterCourses = $this->filterCoursesBySemester($regCourses, 'Second Semester', 'csemester');

        $courseAdviser = DB::table('users')
            ->whereRaw("FIND_IN_SET(?, u_cos)", [$this->student->stdcourse])
            ->where("u_prog", $this->student->stdprogramme_id)
            ->where("u_group", 10)
            ->where("u_progtype", $this->student->stdprogrammetype_id)
            ->select('u_surname', 'u_firstname')
            ->first();


        return view('portal.cregform', [
            'firstSemesterCourses' => $firstSemesterCourses,
            'secondSemesterCourses' => $secondSemesterCourses,
            'sess' => $session,
            'student' => $this->student,
            'courseAdviser' => $courseAdviser
        ]);
    }
}
