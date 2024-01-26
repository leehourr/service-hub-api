<?php

namespace App\Http\Controllers;

use App\Models\ServiceListing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Tymon\JWTAuth\Contracts\Providers\Auth;

class ServiceListingController extends Controller
{
    public function __construct()
    {
        //the included method will not get check in the jwt middleware
        $this->middleware('auth:api', ['except' => []]);
    }
    public function addServiceHandler(Request $request)
    {
        DB::beginTransaction();

        try {

            $payload = auth()->payload();
            $user = $payload['data'];
            // return response()->json($user, 200);
            if ($user['account_type'] != 'service_provider') {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $data = $request->validate([
                'service_name' => 'required|string',
                'service_description' => 'required|string',
                'service_category' => 'required',
                'pricing' => 'nullable|required|numeric',
                'created_at' => 'nullable',
            ]);

            // Create and save the service
            $service = ServiceListing::create([
                'service_name' => $data['service_name'],
                'service_description' => $data['service_description'],
                'service_category' => $data['service_category'],
                'pricing' => $data['pricing'],
                'service_provider_id' => $user['id'],
                'created_at' => Carbon::now()
            ]);

            DB::commit();
            return response()->json(['message' => 'Service added successfully', 'data' => $service], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }

    public function getServiceHandler()
    {
        try {
            $payload = auth()->payload();
            $user = $payload['data'];
            // return response()->json($user, 200);
            if ($user['account_type'] != 'service_provider') {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Create and save the service
            $services = ServiceListing::where('service_provider_id', $user['id'])->get();

            DB::commit();
            return response()->json(['data' => $services], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }
    public function editService(Request $request, $id)
    {
        DB::beginTransaction();
        try {

            $payload = auth()->payload();
            $user = $payload['data'];
            // return response()->json($user, 200);

            $service = ServiceListing::where(['id' => $id, 'service_provider_id' => $user['id']])->first();

            if ($user['account_type'] != 'service_provider' || $service == null) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            DB::commit();
            $service->update($request->all());
            return response()->json(['message' => 'Service updated', 'data' => $service], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }

    public function removeService($id)
    {
        DB::beginTransaction();
        try {

            $payload = auth()->payload();
            $user = $payload['data'];

            $service = ServiceListing::where(['id' => $id, 'service_provider_id' => $user['id']])->first();

            if ($user['account_type'] != 'service_provider' || $service == null) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            DB::commit();
            $service->update(['status' => 'unavailable']);
            return response()->json(['message' => 'Service updated', 'data' => $service], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }
}
