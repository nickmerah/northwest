<?php

namespace App\Traits;

use App\Models\StdSession;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

trait ValidatesPortalUser
{
    protected $student;

    protected $currentSession;

    protected function validatePortalUser()
    {
        $userData = session('user_data');

        if (!$userData) {
            return redirect('/portallogin')->with('error', 'Session data not found. Please log in again.');
        }

        try {
            $userData = json_decode(Crypt::decryptString($userData), true);
        } catch (DecryptException $e) {
            session()->forget('user_data');
            return redirect('/portallogin')->with('error', 'Invalid session data. Please log in again.');
        }

        $this->student = StudentProfile::with(
            'programme',
            'department',
            'programmeType',
            'departmentOption',
            'level',
            'school',
            'stateor',
            'lga'
        )->where('std_logid', $userData['sid'] ?? null)->first();

        if (!$this->student) {
            session()->forget('user_data');
            return redirect('/portallogin')->with('error', 'Student record not found. Please log in again.');
        }

        $this->currentSessionSem = StdSession::getStdCurrentSession($this->student->stdprogramme_id);
    }
}
