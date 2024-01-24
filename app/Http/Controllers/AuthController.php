<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use DB;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'signup']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function signup(Request $request)
    {
        DB::beginTransaction();
        try {
            $credential = $request->validate(
                [
                    'name' => 'required',
                    'username' => 'required',
                    'phone_number' => 'nullable|numeric',
                    'account_type' => 'required|in:service_provider,client',
                    'password' => [
                        'required',
                        'string',
                        'min:8',
                        'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                    ],
                ],
            );

            $hashedPassword = Hash::make($credential['password']);

            $res = User::create([
                'name' => $credential['name'],
                'username' => $credential['username'],
                'phone_number' => $credential['phone_number'],
                'account_type' => $credential['account_type'],
                'password' => $hashedPassword,
            ]);
            DB::commit();
            return response()->json(['message' => 'Account created'], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($e instanceof \Illuminate\Database\QueryException) {
                $errorCode = $e->errorInfo[1];

                if ($errorCode == 1062) { // MySQL error code for duplicate entry
                    return response()->json([
                        'errMessage' => 'Username must be unique.'
                    ], 400);
                }
            }
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $data = $request->validate([
                'username' => 'required',
                'password' => 'required',
            ]);

            $user = User::where('username', $data['username'])->first();
            if (!$user || !Hash::check($data['password'], $user['password'])) {
                return response()->json(["message" => "Incorrect credentials"], 403);
            }
            $accounType = AccountType::where('id', $user['account_type_id'])->first();

            $customClaims = [
                'iss' => 'wbs',
                'user_id' => $user->id,
                'username' => $user->username,
                'account_type_id' => $accounType->id,
                'account_type' => $accounType->account_type
            ];

            $token = JWTAuth::claims($customClaims)->fromUser($user);
            // return response()->json($token, 200);

            return $this->respondWithToken($token);
        } catch (\Throwable $e) {
            // DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }

    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
