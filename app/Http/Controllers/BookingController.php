<?php

namespace App\Http\Controllers;

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

            $bookingList = Booking::where('user_id', $user['id'])->orWhere('service_provider_id', $user['id'])->get();
            return response()->json(['data' => $bookingList], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }

    public function addBooking(Request $request, $service_provider_id)
    {
        DB::beginTransaction();
        try {

            $payload = auth()->payload();
            $user = $payload['data'];

            if ($user['account_type'] != 'client') {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $hasBooked = Booking::where(['user_id' => $user['id'], 'service_provider_id' => $service_provider_id])->exists();

            if ($hasBooked) {
                return response()->json(['message' => 'You already booked this service.'], 422);
            }

            $booking = Booking::create([
                'date_time' => Carbon::now(),
                'user_id' => $user['id'],
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

    public function cancelBooking(Request $request, $booking_id)
    {
        DB::beginTransaction();
        try {

            $payload = auth()->payload();
            $user = $payload['data'];

            if ($user['account_type'] != 'client') {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            Booking::destroy($booking_id);

            DB::commit();
            return response()->json(['message' => 'Booking cancel'], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }
}
