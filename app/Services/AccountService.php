<?php

namespace App\Services;

use Exception;
use App\Helpers\AccountHelper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\AppLoginResource;
use App\Interfaces\AccountRepositoryInterface;



class AccountService
{
    protected $accountRepository;

    public function __construct(AccountRepositoryInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function accountRegister($request)
    {

        $request = $request->all();
        //check for captcha if present in the request
        if (isset($request['captcha'])) {
            $captchaResult = AccountHelper::validateCaptcha($request['captcha'], $request['firstNumber'], $request['secondNumber']);
            if (!$captchaResult) {
                throw new Exception('Captcha validation failed.');
            }
        }

        // need to check if portal is closed
        if (!$this->accountRepository->isPortalClosed($request['prog'])) {
            throw new Exception('Portal is CLOSED to Admission Application.');
        }

        // check if programme is disabled
        if ($this->accountRepository->isProgrammeDisabled($request['progtype'], $request['cos_id'], $request['cos_id_two'])) {
            throw new Exception('You have chosen a course of study that is disabled, contact the school admin.');
        }


        //generate username
        $request['username'] = AccountHelper::generateUsername($request['prog'], $request['progtype']);

        // check if the user already exists
        if ($this->accountRepository->usernameAlreadyExists($request['username'])) {
            throw new Exception('Application number already exists, try again!');
        }

        return $this->accountRepository->registerAccount($request);
    }

    public function accountLogin($request)
    {
        $request = $request->only('username', 'password');

        $response = $this->accountRepository->loginAccount($request);

        if (!isset($response['success'])) {
            throw new Exception($response['message']);
        }

        try {
            $tokenResponse = Http::asForm()->post(env('APP_URL') . '/oauth/token', [
                'grant_type' => 'password',
                'client_id' => env('PERSONAL_ACCESS_CLIENT_ID'),
                'client_secret' => env('PERSONAL_ACCESS_CLIENT_SECRET'),
                'username' => $request['username'],
                'password' => $request['password'],
                'scope' => '*',
            ]);
        } catch (\Exception $e) {
            throw new Exception('Token request failed');
        }

        if ($tokenResponse->failed()) {
            throw new Exception('Token request failed');
        }

        $tokenData = $tokenResponse->json();

        // Calculate expires_at from current time + expires_in
        $tokenData['expires_at'] = Carbon::now()->addSeconds($tokenData['expires_in']);

        return [
            'success' => true,
            'user' => new AppLoginResource($response['user']),
            'token' => $tokenData,
        ];
    }

    public function resetPassword($request)
    {
        $username = $request->only('username');

        return $this->accountRepository->resetPassword($username);
    }
}
