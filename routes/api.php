<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admissions\Api\V1\AccountController;
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
    Route::get('/courses-of-study/{programmeId}/{programmeTypeId}', [SchoolSettingsController::class, 'getCoursesOfStudy']);
    Route::post('/register', [AccountController::class, 'register']);
    Route::post('/login', [AccountController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/dashboard', [DashBoardController::class, 'index']);
       // Route::get('/logout', [AccountController::class, 'logout']);
    });

    
});
