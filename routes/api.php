<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admissions\Api\V1\AccountController;
use App\Http\Controllers\Admissions\Api\v1\PaymentController;
use App\Http\Controllers\Admissions\Api\v1\ProfileController;
use App\Http\Controllers\Admissions\Api\v1\DashBoardController;
use App\Http\Controllers\Admissions\Api\v1\SchoolSettingsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::get('/school-info', [SchoolSettingsController::class, 'index']);
    Route::get('/programmes', [SchoolSettingsController::class, 'getProgrammes']);
    Route::get('/programme-types', [SchoolSettingsController::class, 'getProgrammeTypes']);
    Route::get('/getstateoforigin', [SchoolSettingsController::class, 'getStateofOrigin']);
    Route::post('/getlga', [SchoolSettingsController::class, 'getLGA']);
    Route::get('/getolevelsubjects', [SchoolSettingsController::class, 'getOlevelSubjects']);
    Route::get('/getolevelgrades', [SchoolSettingsController::class, 'getOlevelGrades']);
    Route::get('/courses-of-study/{programmeId}/{programmeTypeId}', [SchoolSettingsController::class, 'getCoursesOfStudy']);
    Route::post('/register', [AccountController::class, 'register']);
    Route::post('/login', [AccountController::class, 'login']);
    Route::post('/forgotpassword', [AccountController::class, 'resetpassword']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/dashboard', [DashBoardController::class, 'index']);
        Route::post('/makepayment', [PaymentController::class, 'paynow']);
        Route::post('/verifypayment', [PaymentController::class, 'checkpayment']);
        Route::get('/paymenthistory', [PaymentController::class, 'getpaymentHistory']);
        Route::get('/biodata', [ProfileController::class, 'getProfile']);
        Route::post('/updatebiodata', [ProfileController::class, 'saveProfile']);

        // Route::get('/logout', [AccountController::class, 'logout']);
    });

    Route::get('/paymentresponse', [PaymentController::class, 'paymentresponse'])->name('paymentresponse');
    Route::get('/paystackcancelaction', [PaymentController::class, 'paystackcancelaction'])->name('paystackcancelaction');
});
