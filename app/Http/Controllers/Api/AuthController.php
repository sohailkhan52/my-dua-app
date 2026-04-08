<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; // ✅ IMPORTANT
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * API Login (JWT)
     */
    public function login(Request $request)
    {
        // validates  email and password from the Api request
        $validate = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:8',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validate->errors(),
            ], 422);
        }
        //Gather user credential that will be compaired with the credentials present in the token in database
        $credentials = $request->only("email", 'password');

        if (! $token = auth('api')->attempt($credentials)) {

            return response()->json([
                "status" => false,
                'error'  => "Invalid Credentials",
            ]);
        }

        $ttl = auth('api')->factory()->getTTL();

        $expiresAt = now()->addMinutes($ttl);
        $user      = auth("api")->user();
        $user->update([
            'token'         => $token,
            'token_expires' => $expiresAt,
        ]);
        return response()->json([
            'status'        => true,
            'message'        => "User loged in successfully",
            'user'          => auth('api')->user(),
            'authorization' => [
                'token' => $token,
                'type'  => 'bearer',
            ],

        ]);
    }
    /**
     * API Register (JWT)
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            "email"    => "required|email|unique:users",
            "password" => "required|string|min:8|confirmed",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status " => false,
                "error "  => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            "name"     => strtolower($request->name),
            "email"    => $request->email,
            "password" => Hash::make($request->password),
        ]);

        $user->assignRole('user');

        $token     = auth("api")->login($user);
        $ttl       = auth("api")->factory()->getTTL();
        $expiresAt = now()->addMinutes($ttl);

        $user->update([
            "token"         => $token,
            "token_expires" => $expiresAt,
        ]);

        return response()->json([
            "Status"        => true,
            "Message"       => "User registered successfully",
            "user"          => $user,
            "authorization" => [
                'token' => $token,
                "type"  => "bearer",
            ],
        ], 201);
    }

    public function user()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {

        $user = User::where("id", auth('api')->id())->first();

        $user->update(["token" => ""]);
        return response()->json([
            'status'  => true,
            'message' => 'Successfully logged out',
        ]);
    }
}
