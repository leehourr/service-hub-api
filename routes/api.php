<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ReviewController;
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
    Route::get('service', [ServiceListingController::class, 'getServiceList']);
    Route::post('add-service', [ServiceListingController::class, 'addServiceHandler']);
    Route::get('service-list', [ServiceListingController::class, 'getServiceHandler']);
    Route::patch('edit-service/{id}', [ServiceListingController::class, 'editService']);
    Route::delete('remove-service/{id}', [ServiceListingController::class, 'removeService']);

    //booking
    Route::get('booking-list', [BookingController::class, 'getBooking']);
    Route::post('booking/{service_provider_id}', [BookingController::class, 'addBooking']);
    Route::delete('booking/{booking_id}', [BookingController::class, 'cancelBooking']);

    //Appointment
    Route::get('appointment-list', [AppointmentController::class, 'getAppointment']);
    Route::post('appointment/{service_provider_id}/{booking_id}', [AppointmentController::class, 'addAppointment']);
    Route::delete('appointment/{appointment_id}', [AppointmentController::class, 'cancelAppointment']);

    //review
    Route::post('submit-review/{service_provider_id}', [ReviewController::class, 'submitReview']);

    //chat
    Route::post('send-chat/{user_id}', [ChatController::class, 'sendChat']);
    Route::get('view-chat/{sender_id}/{user_id}', [ChatController::class, 'viewChat']);
    Route::get('chat-list/{user_id}', [ChatController::class, 'getChatList']);
    Route::delete('removechat/{chat_id}', [ChatController::class, 'deleteChat']);


});
