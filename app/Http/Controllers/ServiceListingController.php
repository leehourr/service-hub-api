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
        $this->middleware('auth:api', ['except' => ['getServiceList', 'searchService']]);
    }

    public function searchService($search)
    {
        try {
            $results = ServiceListing::where('service_name', 'like', '%' . $search . '%')
                ->orWhere('service_category', 'like', '%' . $search . '%')
                ->orWhere('service_description', 'like', '%' . $search . '%')
                ->orWhereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%');
                })
                ->get();

            $services = $results->map(function ($service) use ($search) {
                return [
                    'id' => $service->id,
                    'service_description' => $service->service_description,
                    'service_category' => $service->service_category,
                    'pricing' => $service->pricing,
                    'created_at' => $service->created_at,
                    'updated_at' => $service->updated_at,
                    'service_provider_id' => $service->service_provider_id,
                    'service_name' => $service->service_name,
                    'status' => $service->status,
                    'name' => $service->user->name,
                    'image' => $service->image,
                ];
            });

            return response()->json(['data' => $services], 200);
        } catch (\Throwable $e) {

            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
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

    public function getServiceList()
    {
        try {
            // $payload = auth()->payload();
            // $user = $payload['data'];
            // return response()->json($user, 200);

            $services = ServiceListing::with('user')->get();

            $services = $services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'service_description' => $service->service_description,
                    'service_category' => $service->service_category,
                    'pricing' => $service->pricing,
                    'created_at' => $service->created_at,
                    'updated_at' => $service->updated_at,
                    'service_provider_id' => $service->service_provider_id,
                    'service_name' => $service->service_name,
                    'status' => $service->status,
                    'name' => $service->user->name,
                    'image' => $service->image,
                ];
            });

            DB::commit();
            return response()->json(['data' => $services], 200);
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
