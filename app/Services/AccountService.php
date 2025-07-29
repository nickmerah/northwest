<?php

namespace App\Services;

use Exception;
use App\Helpers\AccountHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\AppLoginResource;
use Laravel\Passport\RefreshTokenRepository;
use App\Interfaces\AccountRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;



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

        if (!$response['success']) {
            //  abort(Response::HTTP_BAD_REQUEST, $response['message']);
            throw new HttpException(Response::HTTP_BAD_REQUEST, $response['message'] ?? 'Bad request');
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

    public function accountLogout()
    {
        $user = auth()->user();
        if ($user) {
            $accessToken = $user->token();

            // Revoke and delete the access token
            $accessToken->revoke();
            $accessToken->delete();

            // Revoke and delete the refresh token(s)
            app(RefreshTokenRepository::class)->revokeRefreshTokensByAccessTokenId($accessToken->id);
            DB::table('oauth_refresh_tokens')->where('access_token_id', $accessToken->id)->delete();

            $this->accountHelper->clearCachedApplicantData($user->log_id);
        }

        return [
            'success' => true,
            'message' => 'Successfully logged out.',
        ];
    }
}
