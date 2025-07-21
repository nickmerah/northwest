<?php

namespace App\Traits;

use App\Models\RemedialStudents;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

trait ValidatesRemedialUser
{
    protected $remedialstudent;

    protected function validateRemedialUser()
    {
        $userData = session('ruser_data');

        if (!$userData) {
            return redirect('/remediallogin')->with('error', 'Session expired. Please log in again.');
        }

        try {
            $userData = json_decode(Crypt::decryptString($userData), true);
        } catch (DecryptException $e) {
            session()->forget('ruser_data');
            return redirect('/remediallogin')->with('error', 'Session expired. Please log in again.');
        }

        $this->remedialstudent = RemedialStudents::where('id', $userData['id'] ?? null)->first();

        if (!$this->remedialstudent) {
            session()->forget('ruser_data');
            return redirect('/remediallogin')->with('error', 'Student record not found. Please log in again.');
        }
    }
}
