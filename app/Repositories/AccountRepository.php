<?php

namespace App\Repositories;

use App\Models\DepartmentOptions;
use Illuminate\Support\Facades\DB;
use App\Models\Admissions\AppLogin;
use Illuminate\Support\Facades\Auth;
use App\Models\Admissions\AppProfile;
use App\Models\Admissions\AppSession;
use App\Interfaces\AccountRepositoryInterface;


class AccountRepository implements AccountRepositoryInterface
{
    public function registerAccount(array $data): AppProfile
    {
        return DB::transaction(function () use ($data) {

            $appLogin = AppLogin::create([
                'log_surname'    => $data['surname'],
                'log_firstname'  => $data['firstname'],
                'log_othernames' => $data['othernames'] ?? null,
                'log_username'   => $data['username'],
                'jambno'         => $data['username'],
                'log_email'      => $data['email'],
                'log_password'   => $data['password'],
                'log_gsm'        => $data['phoneno'],
            ]);

            $appProfile = AppProfile::create([
                'surname'             => $data['surname'],
                'firstname'           => $data['firstname'],
                'othernames'          => $data['othernames'],
                'app_no'              => $appLogin->log_username,
                'jambno'              => $appLogin->log_username,
                'student_email'       => $data['email'],
                'isjamb'              => $data['isjamb'] ?? 0,
                'appyear'             => AppSession::getAppCurrentSession(),
                'student_mobiletel'   => $data['phoneno'],
                'stdprogramme_id'     => $data['prog'],
                'stdcourse'           => $data['cos_id'],
                'std_course'          => $data['cos_id_two'],
                'std_programmetype'   => $data['progtype'],
                'std_logid'           => $appLogin->log_id,
            ]);

            return $appProfile;
        });
    }

    public function isPortalClosed(int $programme): bool
    {
        return DB::table('portal_status')->where('prog_type', $programme)->value('p_status');
    }

    public function isProgrammeDisabled(int $progtype, int $cos_id, int $cos_id_two): bool
    {
        $query = DepartmentOptions::where(function ($q) use ($cos_id, $cos_id_two) {
            $q->where('do_id', $cos_id)
                ->orWhere('do_id', $cos_id_two);
        });

        if ($progtype == 1) {
            $query->where('d_status', 0);
        } else {
            $query->where('d_status_pt', 0);
        }

        return $query->exists();
    }

    public function usernameAlreadyExists(string $username): bool
    {
        return AppLogin::where('log_username', $username)->exists();
    }

    public function getAccountByUsername(string $username): ?AppLogin
    {
        return AppLogin::where('log_username', $username)->first();
    }

    public function getApplicantDetails(int $applicantId): ?AppProfile
    {
        return AppProfile::where('std_logid', $applicantId)->first();
    }

    public function loginAccount(array $data): ?array
    {
        if (!Auth::attempt([
            'log_username' => $data['username'],
            'password' => $data['password'],
        ])) {
            return [
                'success' => false,
                'message' => 'Invalid credentials',
            ];
        }

        return [
            'success' => true,
            'user' => Auth::user(),
        ];
    }

    public function resetPassword(array $data): ?array
    {
        $username = $data['username'];

        if (!self::usernameAlreadyExists($username)) {
            return [
                'success' => false,
                'message' => 'Invalid credentials',
            ];
        }

        $user = AppLogin::where('log_username', $username)->first();
        $genPass = rand(00000, 999999);
        $user->log_password = $genPass;
        $user->save();

        return [
            'success' => true,
            'user' => $username,
            'passkey' => $genPass,
        ];
    }
}
