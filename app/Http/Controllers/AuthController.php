<?php

namespace App\Http\Controllers;

use App\Models\OtpCode;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use DB;

class AuthController extends Controller
{
    public function __construct()
    {
        //the included method will not get check in the jwt middleware
        $this->middleware('auth:api', ['except' => ['numberSigninHandler', 'verifyCodeHandler', 'login', 'signup']]);
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
                    'username' => 'nullable',
                    'phone_number' => 'nullable|numeric',
                    'email' => 'nullable|email',
                    'account_type' => 'required|in:service_provider,client',
                    'password' => [
                        'required',
                        'string',
                        'min:8',
                        'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                    ],
                ],
            );
            $cleanedName = strtolower(str_replace(' ', '', $credential['name']));
            $randomNumber = rand(100, 999);
            $generatedUsername = $cleanedName . $randomNumber;

            // Check if the generated username already exists
            while (User::where('username', $generatedUsername)->exists()) {
                $randomNumber = rand(100, 999);
                $generatedUsername = $cleanedName . $randomNumber;
            }
            $hashedPassword = Hash::make($credential['password']);

            $res = User::create([
                'name' => $credential['name'],
                'username' => $cleanedName . $randomNumber,
                'phone_number' => $credential['phone_number'],
                'email' => $credential['email'] ?? null,
                'account_type' => $credential['account_type'],
                'password' => $hashedPassword,
            ]);
            DB::commit();
            return response()->json(['message' => 'Account created'], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($e instanceof \Illuminate\Database\QueryException) {
                $errorCode = $e->errorInfo[1];
                $errorMessage = ($e->errorInfo[2] && strpos($e->errorInfo[2], 'username') !== false)
                    ? 'Username must be unique.'
                    : 'Phone number must be unique.';

                if ($errorCode == 1062) { // MySQL error code for duplicate entry
                    return response()->json([
                        'errMessage' => $errorMessage
                    ], 400);

                }
            }
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }

    public function numberSigninHandler(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validate(
                [
                    'phone_number' => 'nullable|numeric',
                ],
            );

            $number = $data['phone_number'];

            $user = User::where('phone_number', $number)->first();

            if (!$user) {
                return response()->json(['message' => 'Number does not exist!'], 404);
            }

            $hasSent = OtpCode::where('user_id', $user['id'])->first();

            if ($hasSent) {
                $hasSent->delete();
                // return response()->json($hasSent, 200);
            }

            $code = $this->_generateCode(false, $number);

            $otpRecord = OtpCode::create([
                'user_id' => $user->id,
                'code' => $code,
                'expires_at' => now()->addMinutes(5), // Assuming the OTP is valid for 5 minutes
            ]);

            DB::commit();
            return response()->json(['message' => 'Otp code sent'], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }

    public function verifyCodeHandler(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validate(
                [
                    'user_id' => 'required',
                    'code' => 'required|numeric'
                ],
            );

            $isValidCode = OtpCode::with('user')
                ->where(['user_id' => $data['user_id'], 'code' => $data['code']])
                ->first();

            if (!$isValidCode) {
                // return response()->json(['Message' => 'Code is invalid'], 403);
                return response()->json($isValidCode, 403);

            }

            $user = $isValidCode->user;

            $customClaims = [
                'iss' => 'Service Hub',
                'data' => $user
            ];

            $token = JWTAuth::claims($customClaims)->fromUser($user);

            $isValidCode->delete();
            DB::commit();
            return $this->respondWithToken($token);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
            return response()->json(['errMessage' => $e->getMessage()], 500);
        }
    }


    //regular username/number password login
    public function login(Request $request)
    {
        try {
            $data = $request->validate([
                'account' => 'required',
                'password' => 'required',
            ]);

            $account = $data['account'];

            $user = User::where('username', $account)
                ->orWhere('phone_number', $account)
                ->first();

            if (!$user || !Hash::check($data['password'], $user['password'])) {
                return response()->json(["errMessage" => "Incorrect credentials"], 403);
            }

            $customClaims = [
                'iss' => 'Service Hub',
                'data' => $user
            ];

            $token = JWTAuth::claims($customClaims)->fromUser($user);

            DB::commit();
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

    private function _generateCode($email, $phone)
    {
        $formattedNumber = '855' . ltrim($phone, '0');
        $code = rand(10000, 99999); {
            if ($email) {
                // Mail::to($email)->send(new VerificationCodeMailable($code));
            } else if ($formattedNumber) {
                $this->smsNotificationHandler($formattedNumber, $code);
            }
        }

        return $code;
    }

    public function smsNotificationHandler($number, $code)
    {
        try {
            $client = new Client([
                'headers' => [
                    'Accept' => 'application/json',
                    'X-Secret' => env('SMS_API_SECRET'),
                    'Content-Type' => 'application/json'
                ],
                'verify' => false,
            ]);
            $response = $client->post("https://cloudapi.plasgate.com/rest/send?private_key=" . env('SMS_API_KEY'), [
                \GuzzleHttp\RequestOptions::JSON
                => [
                        'sender' => 'SMS Info',
                        'to' => $number,
                        'content' => "{$code} is your verification code. Valid for 5 minutes."
                    ]
            ]);
            // \Log::debug($response->message_count);

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Error Message:" . $e->getMessage());
        }
    }
}
