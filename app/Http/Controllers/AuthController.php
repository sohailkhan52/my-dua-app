<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
class AuthController extends Controller
{
           /**
     * API Login (JWT)
     */
    public function login(Request $request)
    {
       
    $validate = Validator::make($request->all(),[
      'email'=>'required|email',
      'password'=>'required|string|min:8',
    ]);
    if($validate->fails()){
      return response()->json([
         'status'=>'error',
         'errors'=>$validate->errors(),
      ],422);
    }
    $credentials=$request->only("email",'password');
    if(!$token=auth('api')->attempt($credentials)){
      return response()->json([
         'status'=>'error',
         'message'=>'invalid Credentials'
      ],401);
    }

    $ttl=auth('api')->factory()->getTTL();

    $expiresAt=now()->addMinutes($ttl);
    $user = auth("api")->user();
    $user->update([
      'token'=>$token,
      'token_expires'=>$expiresAt,
    ]);
    return response()->json([
            'status' => 'success',
            'user'   => auth('api')->user(),
          
        ]);
    }

    public function register(Request $request){
      $validate= Validator::make($request->all(),[
         'name' =>'required|string|max:255',
         'email'=>'required|email|unique:users',
         'password'=>'required|string|min:3|confirmed',
      ]);
      if($validate->fails()){
         return response()->json([
            'status'=>'error',
            'errors'=>$validate->errors(),
         ],422);
      }
      $user = User::create([
         'name'=>$request->name,
         'email'=>$request->email,
         'password'=>Hash::make($request->password),
      ]);
      $user->assignRole('user');
      $token = auth('api')->login($user);
      $ttl=auth('api')->factory()->getTTL();

      $expiresAt = now()->addMinutes($ttl);
      $user->update([
         'token'=>$token,
         'token_expires'=>$expiresAt,
      ]);
        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type'  => 'bearer',
            ],
        ], 201);
    }
        public function user()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
      auth("api")->logout();

      return response()->json([
         'status'=>'success',
         'message'=>'Successfully logged out'
      ]);
    }
}