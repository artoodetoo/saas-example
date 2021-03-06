<?php

namespace App\Http\Controllers;

use App\Country;
use Illuminate\Http\Request;
use App\Plan;

class CheckoutController extends Controller
{
    public function checkout($plan_id)
    {
        $plan = Plan::findOrFail($plan_id);
        $countries = Country::all();

        $currentPlan = $this->user()->subscription('default')->stripe_plan ?? null;
        if (!is_null($currentPlan) && $currentPlan != $plan->stripe_plan_id) {
            $this->user()->subscription('default')->swap($plan->stripe_plan_id);
            redirect()->route('billing');
        }

        $intent = $this->user()->createSetupIntent();

        return view('billing.checkout', compact('plan', 'intent', 'countries'));
    }

    public function processCheckout(Request $request)
    {
        $plan = Plan::findOrFail($request->billing_plan_id);
        try {
            
            $this->user()
                ->newSubscription('default', $plan->stripe_plan_id)
                ->create($request->payment_method);

            $this->user()->update([
                'trial_ends_at' => null,
                'company_name' => $request->company_name,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'country_id' => $request->country_id,
                'city' => $request->city,
                'postcode' => $request->postcode,
            ]);

            return redirect()->route('billing')->withMessage('Subscribed successfully!');

        } catch (\Exception $e) {

            return redirect()->back()->withErrors(['stripe' => $e->getMessage()]);

        }
    }
}
