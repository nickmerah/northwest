<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admissions\HomeController;
use App\Http\Controllers\Api\V1\UserController;

Route::get('/admissions', [HomeController::class, 'index'])->name('admissions');
Route::get('/admissions/admreq', [HomeController::class, 'admreq'])->name('admissions.admreq');
Route::get('/admissions/startpart', [HomeController::class, 'startpart'])->name('admissions.startpart');
Route::get('/admissions/starting', [HomeController::class, 'starting'])->name('admissions.starting');
Route::get('/admissions/fpass', [HomeController::class, 'fpass'])->name('admissions.fpass');
Route::get('/admissions/faker', [HomeController::class, 'adminlogin'])->name('admissions.faker');


Route::get('/login', [UserController::class, 'loginform']);
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::get('/allusers', [UserController::class, 'show'])->name('allusers');
Route::middleware('auth')->group(function () {
    Route::get('/allusers', [UserController::class, 'show'])->name('allusers');
});
