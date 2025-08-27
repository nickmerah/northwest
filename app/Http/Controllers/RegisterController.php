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
use App\Models\StateOfOrigin;
use App\Models\StudentProfile;
use App\Models\ClearanceStudents;
use App\Models\DepartmentOptions;
use Illuminate\Support\Facades\DB;

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

        $sors = StateOfOrigin::all();


        return view('portalRegister', array_merge(
            compact('student', 'question'),
            [
                'schoolName' => $this->schoolInfo,
                'sors' => $sors,
            ]
        ));
    }

    public function portalRegister(Request $request)
    {
        $validatedData = $request->validate([
            'password' => 'required|string|min:4',
            'captcha' => 'required|numeric',
            'email' => 'required|email',
        ]);

        // Validate the captcha answer
        if ($request->captcha != session('captcha_answer')) {
            return redirect()->route('portalverify')->with('error', 'Incorrect Maths answer. Please try again.')->withInput();
        }

        $matricNo = $request->input('matno');
        $email = strtolower($request->input('email'));
        $gsm = $request->input('gsm');
        $sor = $request->input('sor');

        $result = $this->checkMatriculationNumber($matricNo);

        if (is_array($result) && isset($result['error'])) {
            return redirect()->back()->withErrors(['matno' => $result['error']]);
        }

        $student =  isset($result->fname) ? trim($result->fname) : '';
        $nameParts = explode(" ", $student);
        // Assign values based on the number of parts
        $surname = isset($nameParts[0]) ? trim($nameParts[0]) : '';
        $firstname = isset($nameParts[1]) ? trim($nameParts[1]) : '';
        $othernames = isset($nameParts[2]) ? trim(implode(" ", array_slice($nameParts, 2))) : '';

        //  DB::beginTransaction();

        try {

            $loginData = [
                'log_surname' => $surname,
                'log_firstname' => $firstname,
                'log_othernames' => $othernames,
                'log_username' => $matricNo,
                'log_email' => $email,
                'log_password' => strtolower($validatedData['password']),
                'log_spassword' => strtolower($surname),
                'token' => Str::random(60),
                'token_expires_at' => now()->addHour(),
            ];

            $login = StudentLogin::create($loginData);

            $nullable = '';
            $deptId = DepartmentOptions::where('do_id', $result->do_id)->value('dept_id') ?? 0;
            $facId = Department::where('departments_id', $deptId)->value('fac_id') ?? 0;

            $studentData = [

                'std_logid' => $login->log_id,
                'matric_no' => $matricNo,
                'surname'  => $surname,
                'firstname' => $firstname,
                'othernames' => $othernames,
                'gender' => $nullable,
                'marital_status' => $nullable,
                'birthdate' =>  null,
                'matset' => $nullable,
                'local_gov' => 0,
                'state_of_origin' => $sor,
                'religion' => $nullable,
                'nationality' => $nullable,
                'contact_address' => $nullable,
                'student_email' => $email,
                'student_homeaddress' => $nullable,
                'student_mobiletel' => $gsm ?? $nullable,
                'std_genotype' => $nullable,
                'std_bloodgrp' => $nullable,
                'std_pc' => $nullable,
                'next_of_kin' =>  $nullable,
                'nok_address' =>  $nullable,
                'nok_tel' => $nullable,
                'stdprogramme_id' => $result->prog ?? 0,
                'stdprogrammetype_id' => $result->progtype ?? 0,
                'stdfaculty_id' => $facId ?? 0,
                'stddepartment_id' => $deptId ?? 0,
                'stdcourse' => $result->do_id ?? 0,
                'stdlevel' => $result->level ?? 0,
                'std_admyear' => $result->admyear ?? 2024,
                'std_photo' => 'avatar.jpg',
                'std_status' => ($result->level == 1) ? 'New' : 'Returning',
                'student_status' => $nullable,
                'promote_status' => 0
            ];

            StudentProfile::create($studentData);
            // DB::commit();

            DB::table('stdaccess')
                ->where('matno', $matricNo)
                ->update(['logstatus' => 1]);


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

        $studentProfile = StudentProfile::where('matric_no', $matricNo)->first();
        $studentLogin = StudentLogin::where('log_username', $matricNo)->first();

        if ($studentProfile || $studentLogin || $student->logstatus == 1) {
            return ['error' => 'Matriculation number already verified, Login to continue.'];
        }

        return $student;
    }
}
