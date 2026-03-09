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
        $user = User::where("email", $request->email)->first();
            if (!$user||!$user->hasRole('admin')) {

            auth()->logout();

            return response()->json([
               'error'=>"Only admin can login"
            ]);
        }
    $token=auth('api')->attempt($credentials);

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
            'authorization' => [
                'token' => $token,
                'type'  => 'bearer',
            ],
          
        ]);
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