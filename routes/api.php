<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Auth routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
Route::post('forgot-password', [NewPasswordController::class, 'forgotPassword']);
Route::post('reset-password', [NewPasswordController::class, 'resetPassword'])->name('password.reset');
// Notice in case of unverified email
Route::get('verification-notice', [NewPasswordController::class, 'notice'])->name('verification.notice');
// set key in redis
Route::get('set-key', [NewPasswordController::class, 'setKeyRedis']);


// Authenticated routes
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::post('email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail']);
});

// Authenticated and verified email routes
Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::post('password-change', [NewPasswordController::class, 'changePassword']);
});
