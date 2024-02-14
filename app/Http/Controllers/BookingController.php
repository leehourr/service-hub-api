<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Booking;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }

    public function getBooking(Request $request)
    {
        try {
            $payload = auth()->payload();
            $user = $payload['data'];

            $userBookings = Booking::with(['user', 'serviceProvider'])
                ->where('user_id', $user['id'])
                ->get();

            // Fetch bookings for the user as a service provider
            $providerBookings = Booking::with(['user', 'serviceProvider'])
                ->where('service_provider_id', $user['id'])
                ->get();

            // Merge the results
            $bookingList = $userBookings->merge($providerBookings);
            // Map the results and structure the response
            $formattedBookings = $bookingList->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'service_name' => $booking->service->service_name,
                    'provider_name' => $booking->serviceProvider->name,
                    'client_name' => $booking->user->name,
                    'client_id' => $booking->user->id,
                    'book_date' => $booking->created_at,
                    'status' => $booking->status,
                    // Add other fields as needed
                ];
            });
            return response()->json(['data' => $formattedBookings], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }

    public function addBooking(Request $request, $service_provider_id, $service_id)
    {
        DB::beginTransaction();
        try {

            $payload = auth()->payload();
            $user = $payload['data'];

            if ($user['account_type'] != 'client') {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $hasBooked = Booking::where(['user_id' => $user['id'], 'service_provider_id' => $service_provider_id, 'service_id' => $service_id])->first();
            // return response()->json($hasBooked, 422);
            // return response()->json(['message' => 'You already booked this service.', 'data' => $hasBooked], 200);

            if ($hasBooked && $hasBooked['status'] == "cancelled") {
                $success = $hasBooked->update(['status' => "pending"]);
                DB::commit();
                if ($success) {
                    return response()->json(['message' => 'Rebook successfully', 'data' => $hasBooked], 200);
                } else {
                    return response()->json(['message' => 'Update failed.'], 500);
                }
            }

            if ($hasBooked && $hasBooked['status'] == "pending") {
                DB::commit();
                return response()->json(['message' => 'You already booked this service.'], 200);
            }


            $booking = Booking::create([
                'date_time' => Carbon::now(),
                'user_id' => $user['id'],
                'service_id' => $service_id,
                'service_provider_id' => $service_provider_id,
                'created_at' => Carbon::now()
            ]);

            DB::commit();
            return response()->json(['message' => 'Service booked successfully', 'data' => $booking], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }


    public function acceptBooking(Request $request, $client_id, $booking_id)
    {
        DB::beginTransaction();
        try {

            $payload = auth()->payload();
            $user = $payload['data'];

            if ($user['account_type'] != 'service_provider') {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $booking = Booking::where(['id' => $booking_id, 'user_id' => $client_id, 'service_provider_id' => $user['id']])->first();

            if ($booking && $booking['status'] == "pending") {
                $success = $booking->update(['status' => "accepted"]);
                DB::commit();
                if ($success) {
                    Appointment::create(
                        [
                            'date_time' => Carbon::now(),
                            'user_id' => $client_id,
                            'service_provider_id' => $user['id'],
                            'booking_id' => $booking_id
                        ]
                    );
                    return response()->json(['message' => 'Booking acccepted', 'data' => $booking], 200);
                } else {
                    return response()->json(['message' => 'Update failed.'], 500);
                }
            }

            return response()->json(['message' => 'Booking updated', 'data' => $booking], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }

    public function cancelBooking(Request $request, $booking_id)
    {
        DB::beginTransaction();
        try {

            $payload = auth()->payload();
            $user = $payload['data'];

            if ($user['account_type'] != 'client') {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $booking = Booking::where('id', $booking_id)->update(['status' => "cancelled"]);
            // Booking::destroy($booking_id);

            DB::commit();
            return response()->json(['message' => 'Booking cancelled', 'data' => $booking], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }
}
