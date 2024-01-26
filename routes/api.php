<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceListingController;
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
    Route::post('login', [AuthController::class, 'login']);
    // Route::post('email-signin', [AuthController::class, 'emailSigninHandler']);
    Route::post('verify-otp', [AuthController::class, 'verifyCodeHandler']);
    Route::post('logout', [AuthController::class, 'logout']);

    //service-listing
    Route::post('add-service', [ServiceListingController::class, 'addServiceHandler']);
    Route::get('service-list', [ServiceListingController::class, 'getServiceHandler']);
    Route::patch('edit-service/{id}', [ServiceListingController::class, 'editService']);
    Route::delete('remove-service/{id}', [ServiceListingController::class, 'removeService']);




});
