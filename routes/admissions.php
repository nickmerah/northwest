<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admissions\HomeController;
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
Route::post('/admissions/login', [HomeController::class, 'login'])->name('admissions.login');

Route::middleware('checkApplicantSession')->group(
    function () {
        Route::get('/admissions/dashboard', [DashboardController::class, 'index'])->name('admissions.dashboard');
    }
);
