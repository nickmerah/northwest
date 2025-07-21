<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Levels;
use App\Models\Department;
use App\Models\Programmes;
use App\Models\SchoolInfo;
use Illuminate\Support\Str;
use App\Models\StudentLogin;
use Illuminate\Http\Request;
use App\Models\StudentProfile;
use App\Models\ClearanceStudents;
use App\Models\DepartmentOptions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class RegisterController extends Controller
{
    protected $schoolInfo;
    protected $programmes;
    protected $levels;

    public function __construct()
    {
        $this->schoolInfo = SchoolInfo::first();
        $this->programmes = Programmes::all();
        $this->levels = Levels::all();
    }


    public function showClearanceRegistrationForm()
    {
        $number1 = rand(1, 20);
        $number2 = rand(1, 20);
        $operator = rand(0, 1) ? '+' : '-';
        $question = "{$number1} {$operator} {$number2}";

        session(['captcha_answer' => $this->calculateCaptchaAnswer($number1, $number2, $operator)]);

        return view('clearanceRegister', array_merge(
            compact('question'),
            [
                'schoolName' => $this->schoolInfo,
                'programmes' => $this->programmes,
                'levels' => $this->levels,
            ]
        ));
    }

    private function calculateCaptchaAnswer($number1, $number2, $operator)
    {
        return $operator === '+' ? $number1 + $number2 : $number1 - $number2;
    }

    public function getDepartments($programmeId)
    {
        $departments = DepartmentOptions::where('prog_id', $programmeId)->get(['do_id', 'programme_option']);

        return response()->json($departments);
    }

    public function getLevels($programmeId)
    {
        $levels = Levels::where('programme_id', $programmeId)->get(['level_id', 'level_name']);

        return response()->json($levels);
    }

    public function clearanceRegister(Request $request)
    {
        $validatedData = $request->validate([
            'surname' => 'required|string|max:150',
            'firstname' => 'required|string|max:150',
            'othernames' => 'nullable|string|max:150',
            'matric_number' => 'required|string|max:50|unique:cprofile,matricno',
            'year_of_graduation' => 'required|numeric|between:2003,' . date('Y'),
            'department' => 'required|numeric',
            'programme' => 'required|numeric',
            'email' => 'required|email|max:150',
            'phone' => 'required|numeric',
            'level' => 'required|numeric',
            'password' => 'required|string|min:4',
            'captcha' => 'required|numeric',
        ]);

        // Validate the captcha answer
        if ($request->captcha != session('captcha_answer')) {
            return back()->withErrors(['captcha' => 'Incorrect Maths answer. Please try again.'])->withInput();
        }

        $sanitizedData = [
            'surname' => strip_tags($validatedData['surname']),
            'firstname' => strip_tags($validatedData['firstname']),
            'othernames' => isset($validatedData['othernames']) ? strip_tags($validatedData['othernames']) : null,
            'matricno' => strip_tags($validatedData['matric_number']),
            'graduation_year' => $validatedData['year_of_graduation'],
            'dept_id' => $validatedData['department'],
            'prog_id' => $validatedData['programme'],
            'level_id' => $validatedData['level'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'password' => strtolower($validatedData['password']),
            'spassword' => strtolower($validatedData['surname']),
        ];

        // Capitalize specific fields
        $sanitizedData['surname'] = strtoupper($sanitizedData['surname']);
        $sanitizedData['firstname'] = strtoupper($sanitizedData['firstname']);
        $sanitizedData['othernames'] = isset($sanitizedData['othernames']) ? strtoupper($sanitizedData['othernames']) : null;
        $sanitizedData['matricno'] = strtoupper($sanitizedData['matricno']);
        $sanitizedData['email'] = strtolower($sanitizedData['email']);
        $sanitizedData['phone'] = strtolower($sanitizedData['phone']);


        // Create a new student record with sanitized and capitalized data
        ClearanceStudents::create($sanitizedData);

        return redirect()->route('clearancelogin')->with('success', 'Registration successful. Please log in.');
    }

    // PORTAL

    public function LoginVerification(Request $request)
    {
        $request->validate([
            'matno' => 'required|max:25|regex:/^[A-Z0-9\/]+$/',
        ], [
            'matno.regex' => 'Matric number can only contain letters, numbers, and slashes.',
        ]);

        $matricNo = $request->input('matno');

        $result = $this->checkMatriculationNumber($matricNo);

        if (is_array($result) && isset($result['error'])) {
            return redirect()->back()->withErrors(['matno' => $result['error']]);
        }

        $student = $result;

        $number1 = rand(1, 20);
        $number2 = rand(1, 20);
        $operator = rand(0, 1) ? '+' : '-';
        $question = "{$number1} {$operator} {$number2}";

        session(['captcha_answer' => $this->calculateCaptchaAnswer($number1, $number2, $operator)]);

        return view('portalRegister', array_merge(
            compact('student', 'question'),
            [
                'schoolName' => $this->schoolInfo,
            ]
        ));
    }

    public function portalRegister(Request $request)
    {
        $validatedData = $request->validate([
            'password' => 'required|string|min:4',
            'captcha' => 'required|numeric',
        ]);

        // Validate the captcha answer
        if ($request->captcha != session('captcha_answer')) {
            return redirect()->route('portalverify')->with('error', 'Incorrect Maths answer. Please try again.')->withInput();
        }

        $matricNo = $request->input('matno');

        $result = $this->checkMatriculationNumber($matricNo);

        if (is_array($result) && isset($result['error'])) {
            return redirect()->back()->withErrors(['matno' => $result['error']]);
        }

        $student = $result;

        //  DB::beginTransaction();

        try {

            $loginData = [
                'log_surname' => $student->surname,
                'log_firstname' => $student->firstname,
                'log_othernames' => $student->othername,
                'log_username' => $student->matno,
                'log_email' => $student->email,
                'log_password' => strtolower($validatedData['password']),
                'log_spassword' => strtolower($student->surname),
                'token' => Str::random(60),
                'token_expires_at' => now()->addHour(),
            ];

            $login = StudentLogin::create($loginData);

            $nullable = '';
            $deptId = DepartmentOptions::where('do_id', $student->do_id)->value('dept_id') ?? 0;
            $facId = Department::where('departments_id', $deptId)->value('fac_id') ?? 0;

            $studentData = [

                'std_logid' => $login->log_id,
                'matric_no' => $student->matno,
                'surname'  => $student->surname,
                'firstname' => $student->firstname,
                'othernames' => $student->othername,
                'gender' => $student->gender,
                'marital_status' => $nullable,
                'birthdate' => $this->isValidDate($student->birthdate) ? $student->birthdate : null,
                'matset' => $nullable,
                'local_gov' => $this->isValidInteger($student->lga) ? $student->lga : 0,
                'state_of_origin' => $this->isValidInteger($student->stateor) ? $student->stateor : 0,
                'religion' => $nullable,
                'nationality' => $student->nationality ?? $nullable,
                'contact_address' => $nullable,
                'student_email' => $student->email ?? $nullable,
                'student_homeaddress' => $nullable,
                'student_mobiletel' => $student->gsm ?? $nullable,
                'std_genotype' => $nullable,
                'std_bloodgrp' => $nullable,
                'std_pc' => $nullable,
                'next_of_kin' => $student->nok ?? $nullable,
                'nok_address' => $student->nokadd ?? $nullable,
                'nok_tel' => $student->nokgsm ?? $nullable,
                'stdprogramme_id' => $student->prog ?? 0,
                'stdprogrammetype_id' => $student->progtype ?? 0,
                'stdfaculty_id' => $facId ?? 0,
                'stddepartment_id' => $deptId ?? 0,
                'stdcourse' => $student->do_id ?? 0,
                'stdlevel' => $student->level ?? 0,
                'std_admyear' => $student->admyear ?? 2023,
                'std_photo' => $student->stdno . '.jpg',
                'std_status' => ($student->level == 1 || $student->level == 3) ? 'New' : 'Returning',
                'student_status' => $student->stdstatus ?? $nullable,
                'promote_status' => 0
            ];

            StudentProfile::create($studentData);

            // activate the stdaccess account
            DB::table('stdaccess')->where('matno', $student->matno)->update(['activated' => 1]);
            // DB::commit();
            return redirect()->route('portallogin')->with('success', 'Registration successful. Please log in.');
        } catch (\Exception $e) {

            //   DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Error creating the account: ' . $e->getMessage()]);
        }
        return redirect()->back()->withErrors(['error' => 'Unable to create account']);
    }

    public function checkMatriculationNumber($matricNo)
    {
        $student = DB::table('stdaccess')->where('matno', $matricNo)->first();

        if (!$student) {
            return ['error' => 'Matriculation number not found.'];
        }

        if ($student->activated == 1) {
            return ['error' => 'Matriculation number already verified, Login to continue.'];
        }

        $studentProfile = StudentProfile::where('matric_no', $matricNo)->first();
        $studentLogin = StudentLogin::where('log_username', $matricNo)->first();

        if ($studentProfile || $studentLogin) {
            return ['error' => 'Matriculation number already verified, Login to continue.'];
        }

        return $student;
    }

    private function isValidDate($date, $format = 'Y-m-d')
    {
        // Check if the date is empty or not a valid date
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    private function isValidInteger($value)
    {
        // Check if the value is a valid integer
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
}
