<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admissions\HomeController;
use App\Http\Controllers\Admissions\PaymentsController;
use App\Http\Controllers\Admissions\ApplicantController;
use App\Http\Controllers\Admissions\DashboardController;

Route::get('/admissions', [HomeController::class, 'index'])->name('admissions');
Route::get('/admissions/admreq', [HomeController::class, 'admreq'])->name('admissions.admreq');
Route::get('/admissions/startpart', [HomeController::class, 'startpart'])->name('admissions.startpart');
Route::get('/admissions/starting', [HomeController::class, 'starting'])->name('admissions.starting');
Route::get('/admissions/fpass', [HomeController::class, 'fpass'])->name('admissions.fpass');
Route::post('/admissions/fpass', [HomeController::class, 'resetpass'])->name('admissions.fpass');
Route::get('/admissions/faker', [HomeController::class, 'adminlogin'])->name('admissions.faker');
Route::post('/admissions/register', [HomeController::class, 'store'])->name('admissions.store');
Route::get('/admissions/success', [HomeController::class, 'success'])->name('admissions.success');
Route::post('/admissions/login', [HomeController::class, 'login'])->name('admissions.login')->middleware('throttle:8,1');

Route::middleware('checkApplicantSession')->group(
    function () {
        Route::get('/admissions/dashboard', [DashboardController::class, 'index'])->name('admissions.dashboard');
        Route::get('/admissions/paynow', [PaymentsController::class, 'index'])->name('admissions.paynow');
        Route::post('/admissions/paynow', [PaymentsController::class, 'paynow'])->name('admissions.processpayment');
        Route::get('/admissions/checkpayment', [PaymentsController::class, 'verifypayment'])->name('admissions.checkpayment');
        Route::get('/admissions/paymenthistory', [PaymentsController::class, 'transactionhistory'])->name('admissions.paymenthistory');
        Route::get('/admissions/receipt/{transactionId?}', [PaymentsController::class, 'paymentreceipt'])->name('admissions.receipt');
        Route::get('/admissions/myapplication', [ApplicantController::class, 'applicationhome'])->name('admissions.myapplication');
        Route::get('/admissions/biodata', [ApplicantController::class, 'biodata'])->name('admissions.biodata');
        Route::post('/admissions/biodata', [ApplicantController::class, 'savebiodata'])->name('admissions.biodata');
        Route::get('/admissions/olevel', [ApplicantController::class, 'olevel'])->name('admissions.olevel');
        Route::post('/admissions/olevel', [ApplicantController::class, 'saveolevel'])->name('admissions.olevel');
        Route::get('/admissions/jamb', [ApplicantController::class, 'jamb'])->name('admissions.jamb');
        Route::post('/admissions/jamb', [ApplicantController::class, 'savejamb'])->name('admissions.jamb');
        Route::get('/admissions/school', [ApplicantController::class, 'school'])->name('admissions.school');
        Route::post('/admissions/school', [ApplicantController::class, 'saveschool'])->name('admissions.school');
        Route::get('/admissions/certupload', [ApplicantController::class, 'certupload'])->name('admissions.certupload');
        Route::post('/admissions/certupload', [ApplicantController::class, 'savecertupload'])->name('admissions.certupload');
        Route::get('/admissions/removecert', [ApplicantController::class, 'deletecertupload'])->name('admissions.removecert');
        Route::get('/admissions/declaration', [ApplicantController::class, 'declares'])->name('admissions.declaration');
        Route::post('/admissions/declaration', [ApplicantController::class, 'savedeclares'])->name('admissions.declaration');
        Route::get('/admissions/applicationforms', [ApplicantController::class, 'applicationforms'])->name('admissions.applicationforms');
        Route::get('/admissions/applicationform', [ApplicantController::class, 'applicationform'])->name('admissions.applicationform');
        Route::get('/admissions/applicationcard', [ApplicantController::class, 'applicationcard'])->name('admissions.applicationcard');
        Route::get('/admissions/logout', [ApplicantController::class, 'logout'])->name('admissions.logout');
    }
);
