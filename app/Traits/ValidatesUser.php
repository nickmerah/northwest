<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\ClearanceStudents;

trait ValidatesUser
{
    protected $student;

    protected function validateUser()
    {
        $userData = session('user_data');

        if (!$userData) {
            return redirect('/clearancelogin')->with('error', 'Session data not found. Please log in again.');
        }

        try {
            $userData = json_decode(Crypt::decryptString($userData), true);
        } catch (DecryptException $e) {
            session()->forget('user_data');
            return redirect('/clearancelogin')->with('error', 'Invalid session data. Please log in again.');
        }

        $this->student = ClearanceStudents::where('csid', $userData['csid'] ?? null)->first();

        if (!$this->student) {
            session()->forget('user_data');
            return redirect('/clearancelogin')->with('error', 'User record not found. Please log in again.');
        }
    }
}
