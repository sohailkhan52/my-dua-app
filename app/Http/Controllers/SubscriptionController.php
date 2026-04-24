<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Exceptions\IncompletePayment;

class SubscriptionController extends Controller
{
    /**
     * Show the subscription page with available plans
     */
    public function index()
    {
        $plans = config('plans.plans');
        $user  = auth()->user();

        // get current subscription if any
        $currentSubscription = null;
        if ($user && $user->subscribed('default')) {
            $currentSubscription = $user->subscription('default')->strip_plan;
        }

        return view('subscription', compact('plans', 'currentSubscription'));
    }

    /**
     * Create a Stripe Checkout session for subscription
     */

    public function checkout(Request $request)
    {
        $request->validate([
            'price_id' => 'required|string',
        ]);
        $user    = $request->user();
        $priceId = $request->price_id;

        //Create checkout session using Cashier
        return $user->newSubscription('default', $priceId)
            ->allowPromotionCodes()
            ->checkout([
                'customer_update' => [
                    'address' => 'auto',
                ],
                'success_url'     => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'      => route('subscription.cancel'),
            ]);
    }

    /**
     * Handle successful subscription
     */
    public function success()
    {
        return redirect('/home')->with("success","payment successful");
    }

    /**
     * Handle cancelled subscription checkout
     */
    public function cancel()
    {
        return view('subscription.cancel');
    }

    /**
     * Change subscription plan (Basic -> Medium -> Premium)
     */
    public function changePlan(Request $request)
    {
        $request->validate([
            'new_price_id' => 'required|string',
        ]);

        $user       = $request->user();
        $newPriceId = $request->new_price_id;

        if (! $user->subscribed('default')) {
            return redirect()->route('subscription')
                ->with("error", "You don\'t have an active subscription.");
        }
        try {
            // Swap to the new plan
            // Cashier handles proration automatically [citation:4]
            $user->subscription('default')->swap($newPriceId);
            $newPlanName = $this->getPlanNameFromPriceId($newPriceId);

            return redirect()->route('subscription')
                ->with('success', "Your plan has been changed to {$newPlanName}.");
        } catch (IncompletePayment $exception) {
            // Handle incomplete payment (e.g., 3D Secure required)
            return redirect()->route(
                'cashier.payment',
                [$exception->payment->id, 'redirect' => route('subscription')]
            );
        }
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription(Request $request)
    {
        $user = $request->user();
        if (! $user->subscribed('default')) {
            return back()->with('error', "No active subscripion found.");
        }

        // cancel at end of billing period (grace period)
        $user->subscription('default')->cancel();

        $endsAt = $user->subscription('default')->ends_at->format("F j, Y");

        return redirect()->route('subscription')->with('warning', "Your subscription has been cancelled. You'll have access untill {$endsAt}.");
    }

    /**
     * Resume cancelled subscription (during grace period)
     */

    public function resumeSubscription(Request $request)
    {
        $user = $request->user();

        if (! $user->subscription('default')->cancelled()) {
            return back()->with('error', "Your subscription is not cancelled.");
        }

        if ($user->subscription('default')->onGracePeriod()) {
            $user->subscription('default')->resume();
            return redirect()->route('subscription')->with('success', 'Your subscription has been resumed.');
        }

        return redirect()->route('subscription')->with('error', 'Your grace period has expired.
            please create a new subscription.');
    }

    /**
     * Helper: Get plan name from Stripe Price ID
     */
    private function getPlanNameFromPriceId($priceId)
    {
        $plans = config("plans.plans");
        foreach ($plans as $key => $plan) {
            if ($plan['price_id'] === $priceId) {
                return $plan['name'];
            }
        }
        return "unknowm";
    }
}
