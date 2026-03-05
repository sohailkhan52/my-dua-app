<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // THIS CODE CHECKS THAT THE ACCOUNT CAN ONLY BE LOGED IN THROUGH GOOGLE ACCOUNT

    protected function attemptLogin(Request $request)
    {
        // Find user by email
        $user = User::where('email', $request->email)->first();

        // If user has a google_id, prevent normal login
        if ($user && !empty($user->google_id)) {
            return false; // Will fail login
        }

        // Otherwise, proceed with normal login attempt
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->filled('google_token')
        );
    }
}
