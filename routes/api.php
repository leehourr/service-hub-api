<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group([
    'prefix' => 'v1',
    'middleware' => 'api',
], function ($router) {
    //auth
    Route::post('signup', [AuthController::class, 'signup']);
    // Route::post('email-signup', [AuthController::class, 'emailSignup']);
    // Route::post('number-signup', [AuthController::class, 'numberSignup']);
    Route::post('number-signin', [AuthController::class, 'numberSigninHandler']);
    Route::post('email-signin', [AuthController::class, 'emailSigninHandler']);
    Route::post('verify-otp', [AuthController::class, 'verifyCodeHandler']);
    Route::post('logout', [AuthController::class, 'logout']);


});
