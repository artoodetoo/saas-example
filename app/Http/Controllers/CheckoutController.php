<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Plan;

class CheckoutController extends Controller
{
    public function checkout($plan_id)
    {
        $plan = Plan::findOrFail($plan_id);
        $intent = auth()->user()->createSetupIntent();

        return view('billing.checkout', compact('plan', 'intent'));
    }

    public function processCheckout(Request $request)
    {
        $plan = Plan::findOrFail($request->billing_plan_id);
        try {
            auth()->user()
                ->newSubscription($plan->name, $plan->stripe_plan_id)
                ->create($request->payment_method);
            return redirect()->route('billing')->withMessage('Subscribed successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['stripe' => $e->getMessage()]);
        }
    }
}
