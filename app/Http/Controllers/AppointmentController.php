<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Booking;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Carbon;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function getAppointment(Request $request)
    {
        try {
            $payload = auth()->payload();
            $user = $payload['data'];

            $appointments = Appointment::with(['user', 'serviceProvider', 'bookings.service'])
                ->where('user_id', $user['id'])
                ->orWhere('service_provider_id', $user['id'])
                ->get();

            $appointmentList = $appointments->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'date_time' => $appointment->date_time,
                    'status' => $appointment->status,
                    'created_at' => $appointment->created_at,
                    'updated_at' => $appointment->updated_at,
                    'user_id' => $appointment->user_id,
                    'service_provider_id' => $appointment->service_provider_id,
                    'deleted_at' => $appointment->deleted_at,
                    'booking_id' => $appointment->booking_id,
                    'name' => $appointment->user->name,
                    'provider_name' => $appointment->serviceProvider->name,
                    'service_name' => $appointment->bookings->service->service_name,  // Assuming you have a 'serviceListing' relationship in your Booking model
                ];
            });

            return response()->json(['data' => $appointmentList], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }



    public function addAppointment(Request $request, $service_provider_id, $booking_id)
    {
        DB::beginTransaction();
        try {

            $payload = auth()->payload();
            $user = $payload['data'];

            if ($user['account_type'] != 'client') {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $hasBooked = Appointment::where(['user_id' => $user['id'], 'service_provider_id' => $service_provider_id, 'status' => 'pending'])->exists();

            if ($hasBooked) {
                return response()->json(['message' => 'Appointment already made'], 422);
            }

            $booking = Appointment::create([
                'date_time' => Carbon::now(),
                'user_id' => $user['id'],
                'service_provider_id' => $service_provider_id,
                'booking_id' => $booking_id,
                'created_at' => Carbon::now()
            ]);

            DB::commit();
            return response()->json(['message' => 'Appointment booked successfully', 'data' => $booking], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }

    public function cancelAppointment(Request $request, $appointment_id)
    {
        DB::beginTransaction();
        try {

            $payload = auth()->payload();
            $user = $payload['data'];

            if ($user['account_type'] != 'client') {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            Appointment::destroy($appointment_id);

            DB::commit();
            return response()->json(['message' => 'Appoinement canceled'], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }
}
