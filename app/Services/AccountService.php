<?php

namespace App\Services;

use Exception;
use App\Helpers\AccountHelper;
use App\Http\Resources\AppLoginResource;
use App\Interfaces\AccountRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;



class AccountService
{
    protected $accountRepository;
    protected $accountHelper;

    public function __construct(AccountRepositoryInterface $accountRepository, AccountHelper $accountHelper)
    {
        $this->accountRepository = $accountRepository;
        $this->accountHelper = $accountHelper;
    }

    public function accountRegister($request)
    {

        $request = $request->all();
        //check for captcha if present in the request
        if (isset($request['captcha'])) {
            $captchaResult = AccountHelper::validateCaptcha($request['captcha'], $request['firstNumber'], $request['secondNumber']);
            if (!$captchaResult) {
                abort(Response::HTTP_BAD_REQUEST, 'Captcha validation failed.');
                
            }
        }

        // need to check if portal is closed
        if (!$this->accountRepository->isPortalClosed($request['prog'])) {
            abort(Response::HTTP_BAD_REQUEST, 'Portal is CLOSED to Admission Application.');
        }

        // check if programme is disabled
        if ($this->accountRepository->isProgrammeDisabled($request['progtype'], $request['cos_id'], $request['cos_id_two'])) {
            abort(Response::HTTP_BAD_REQUEST, 'You have chosen a course of study that is disabled, contact the school admin.');
        }


        //generate username
        $request['username'] = AccountHelper::generateUsername($request['prog'], $request['progtype']);

        // check if the user already exists
        if ($this->accountRepository->usernameAlreadyExists($request['username'])) {
            abort(Response::HTTP_BAD_REQUEST, 'Application number already exists, try again!');
        }

        return $this->accountRepository->registerAccount($request);
    }

    public function accountLogin($request)
    {
        $credentials = $request->only('username', 'password');

        $response = $this->accountRepository->loginAccount($credentials);

        if (!isset($response['success'])) {
            abort(Response::HTTP_BAD_REQUEST, $response['message']);
        }

        $userId = $response['user']['log_id'];
        $token = $this->accountHelper->generateOAuthToken($credentials);
        $this->accountHelper->cacheApplicantData($userId);

        return [
            'success' => true,
            'user' => new AppLoginResource($response['user']),
            'token' => $token,
        ];
    }

    public function resetPassword($request)
    {
        $username = $request->only('username');

        return $this->accountRepository->resetPassword($username);
    }
}
