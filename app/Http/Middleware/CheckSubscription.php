<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $requiredPlan = null)
    {
        $user = $request->user();

        if(!$user || !$user->subscribed('default')){
            return redirect()->route('pricing')
            ->with("error","You need an active subscription to access this content.");
        }

        if($requiredPlan){
            $planPriceIds = config("plans.plans.{$requiredPlan}.price_id",null);
            if ($planPriceIds && !$user->subscribedToPlan($planPriceIds, 'default')) {
                return redirect()->route('pricing')
                    ->with('error', "This content requires a {$requiredPlan} plan or higher.");
            }
        }
        return $next($request);
    }
}
