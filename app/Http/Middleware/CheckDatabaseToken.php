<?php
/**
 * THE BELOW CODE IS USED    TO COMPAIR THE REQUESTED TOKEN OF THE API WITH THE TOKEN STORED IN THE DATABASE 
 * 
 */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckDatabaseToken
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $token = auth()->getToken();
        
        // Compare request token with database token
        if (!$user || $user->token !== (string)$token) {
            return response()->json([
                'message' => 'Token did not matched or token expiry'
            ], 401);
        }
        
        return $next($request);
    }
}