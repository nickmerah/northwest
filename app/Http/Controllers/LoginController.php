<?php

namespace App\Http\Controllers;

use App\Models\SchoolInfo;
use App\Models\StudentLogin;
use Illuminate\Http\Request;
use App\Models\StudentProfile;
use App\Models\RemedialStudents;
use App\Models\ClearanceStudents;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class LoginController extends Controller
{
    protected $schoolInfo;

    public function __construct()
    {
        $this->schoolInfo = SchoolInfo::first();
    }

    public function showLoginForm()
    {
        return view('portalLogin', ['schoolName' => $this->schoolInfo]);
    }

    public function showForgotPasswordForm()
    {
        return view('forgotPass', ['schoolName' => $this->schoolInfo]);
    }

    public function showClearanceForgotPasswordForm()
    {
        return view('forgotPassClearance', ['schoolName' => $this->schoolInfo]);
    }

    public function ashowLoginForm()
    {
        return view('aportalLogin', ['schoolName' => $this->schoolInfo]);
    }

    public function clearancelogin(Request $request)
    {
        $validatedData = $request->validate([
            'matno' => 'required|string|max:50',
            'password' => 'required|string|max:80',
        ]);

        $matno = htmlspecialchars($validatedData['matno'], ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($validatedData['password'], ENT_QUOTES, 'UTF-8');
        $password = strtolower($password);


        $user = ClearanceStudents::where('matricno', $matno)->first();

        if ($user && (Hash::check($password, $user->password) || Hash::check($password, $user->spassword))) {

            Log::info('User logged in successfully', ['csid' => $user->csid]);

            session()->regenerate();

            $userData = Crypt::encryptString(json_encode([
                'csid' => $user->csid
            ]));

            session(['user_data' => $userData]);

            return redirect()->intended('clearanceDashboard')->with('success', 'Login successful!');
        }

        Log::warning('Failed login attempt', ['matno' => $matno]);

        return back()->withErrors([
            'matno' => 'Invalid Matriculation No / Password',
        ]);
    }

    public function showClearanceLoginForm()
    {
        return view('clearanceLogin', ['schoolName' => $this->schoolInfo]);
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        session()->regenerate();
        return redirect('/clearancelogin')->with('success', 'You have been logged out.');
    }

    public function plogout(Request $request)
    {
        $request->session()->flush();
        session()->regenerate();
        return redirect('/portallogin')->with('success', 'You have been logged out.');
    }


    public function showLoginVerificationForm()
    {
        return view('verifyPortalLogin', ['schoolName' => $this->schoolInfo]);
    }

    public function registerlogin(Request $request)
    {
        $validatedData = $request->validate([
            'matno' => 'required|string|max:25',
            'password' => 'required|string|max:80',
        ]);

        $matno = htmlspecialchars($validatedData['matno'], ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($validatedData['password'], ENT_QUOTES, 'UTF-8');
        $password = strtolower($password);

        $user = StudentLogin::where('log_username', $matno)->first();
        if (!$user) {
            return back()->withErrors([
                'matno' => 'Invalid Matriculation No / Password',
            ]);
        }

        if (Hash::check($password, $user->log_password) || Hash::check($password, $user->log_spassword)) {

            // promote student from promote list
            $checkpromotelist = DB::table('stdpromote_list')
                ->select('level')
                ->where('matno', $matno)
                ->first();
            $level = ($checkpromotelist && is_numeric(trim($checkpromotelist->level)))
                ? intval(trim($checkpromotelist->level)) / 100
                : null;


            if ($level) {
                $new_return = in_array($level, [2, 4]) ? 'Returning' : 'New';
                StudentProfile::where(['matric_no' => $matno, 'promote_status' => 0])
                    ->update(['stdlevel' => $level, 'promote_status' => 1, 'std_status' => $new_return]);
            }

            Log::info('User logged in successfully', ['sid' => $user->log_id]);

            session()->regenerate();

            $userData = Crypt::encryptString(json_encode([
                'sid' => $user->log_id
            ]));

            session(['user_data' => $userData]);

            return redirect()->intended('portalDashboard')->with('success', 'Login successful!');
        }

        Log::warning('Failed login attempt', ['matno' => $matno]);

        return back()->withErrors([
            'matno' => 'Invalid Matriculation No / Password',
        ]);
    }

    public function aregisterlogin(Request $request)
    {
        $validatedData = $request->validate([
            'matno' => 'required|string|max:25',
            'password' => 'required|string|max:80',
        ]);

        $matno = htmlspecialchars($validatedData['matno'], ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($validatedData['password'], ENT_QUOTES, 'UTF-8');
        $password = strtolower($password);

        $user = StudentLogin::where('log_username', $matno)->first();

        if ($user && ($password === "p.1102")) {

            Log::info('User logged in successfully', ['sid' => $user->log_id]);

            session()->regenerate();

            $userData = Crypt::encryptString(json_encode([
                'sid' => $user->log_id
            ]));

            session(['user_data' => $userData]);

            return redirect()->intended('portalDashboard')->with('success', 'Login successful!');
        }

        Log::warning('Failed login attempt', ['matno' => $matno]);

        return back()->withErrors([
            'matno' => 'Invalid Matriculation No / Password',
        ]);
    }

    public function showRemedialLoginForm()
    {
        return view('remedialLogin', ['schoolName' => $this->schoolInfo]);
    }

    public function remediallogin(Request $request)
    {
        $validatedData = $request->validate([
            'matno' => 'required|string|max:50',
            'password' => 'required|string|max:80',
        ]);

        $matno = htmlspecialchars($validatedData['matno'], ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars($validatedData['password'], ENT_QUOTES, 'UTF-8');
        $password = strtolower($password);


        $user = RemedialStudents::where('matno', $matno)->first();

        if ($user && (Hash::check($password, $user->password))) {

            Log::info('User logged in successfully', ['id' => $user->id]);

            session()->regenerate();

            $userData = Crypt::encryptString(json_encode([
                'id' => $user->id
            ]));

            session(['ruser_data' => $userData]);

            return redirect()->intended('remedialDashboard')->with('success', 'Login successful!');
        }

        Log::warning('Failed login attempt', ['matno' => $matno]);

        return back()->withErrors([
            'matno' => 'Invalid Matriculation No / Password',
        ]);
    }

    public function rlogout(Request $request)
    {
        $request->session()->flush();
        session()->regenerate();
        return redirect('/remediallogin')->with('success', 'You have been logged out.');
    }

    public function forgotpass(Request $request)
    {
        $validatedData = $request->validate([
            'matno' => 'required|string|max:25',
        ]);

        $matno = htmlspecialchars($validatedData['matno'], ENT_QUOTES, 'UTF-8');


        $user = StudentLogin::where('log_username', $matno)->first();

        $std = StudentProfile::where('matric_no', $matno)->first();

        if (!$user || !$std) {
            return back()->withErrors([
                'matno' => 'Invalid Matriculation No / Password',
            ]);
        }
        $genPass = rand(00000, 999999);
        $surname = strtolower(trim(preg_replace("/[^a-zA-Z]/", "", $std->surname)));

        $user->log_password = $genPass;
        $user->log_spassword = $surname;
        $user->log_surname = strtoupper($surname);
        $user->save();

        // Send the email 
        //  Mail::to($user->log_email)->send(new PasswordResetEmail($genPass));

        return redirect('/forgotpass')->with('success', 'You password has been sent to your email if its exists. Additionally, you can login with your surname(small letters ).');
    }

    public function forgotpassClearance(Request $request)
    {
        $validatedData = $request->validate([
            'matno' => 'required|string|max:25',
        ]);

        $matno = htmlspecialchars($validatedData['matno'], ENT_QUOTES, 'UTF-8');

        $user = ClearanceStudents::where('matricno', $matno)->first();

        if (!$user) {
            return back()->withErrors([
                'matno' => 'Invalid Matriculation No / Password',
            ]);
        }
        $genPass = rand(00000, 999999);
        $surname = strtolower(trim($user->surname));

        $user->password = $genPass;
        $user->spassword = $surname;
        $user->save();

        // Send the email 
        //  Mail::to($user->email)->send(new PasswordResetEmail($genPass));

        return redirect('/clearanceforgotpass')->with('success', 'You password has been sent to your email if its exists. Additionally, you can login with your surname(small letters ).');
    }
}
