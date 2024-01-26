<?php

namespace App\Http\Controllers;

use App\Models\RatingsReview;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Carbon;

class ReviewController extends Controller
{
    public function __construct()
    {
        //the included method will not get check in the jwt middleware
        $this->middleware('auth:api', ['except' => []]);
    }

    public function submitReview(Request $request, $service_provider_id)
    {
        try {
            $payload = auth()->payload();
            $user = $payload['data'];
            // return response()->json($user, 200);
            if ($user['account_type'] != 'client') {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $data = $request->validate([
                'rating' => 'required|numeric',
                'review' => 'nullable',
            ]);
            // Create and save the service
            $services = RatingsReview::create([
                'rating' => $data['rating'],
                'review' => $data['review'] ?? null,
                'user_id' => $user['id'],
                'service_provider_id' => $service_provider_id,
                'created_at' => Carbon::now()
            ]);

            DB::commit();
            return response()->json(['data' => $services], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }
}
