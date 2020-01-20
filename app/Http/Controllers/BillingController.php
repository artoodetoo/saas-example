<?php

namespace App\Http\Controllers;

use App\Plan;

class BillingController extends Controller
{
    public function index()
    {
        $plans = Plan::all();
        $currentPlan = $this->user()->subscription('default') ?? null;
        $paymentMethods = $this->user()->paymentMethods();
        $defaultPaymentMethod = $this->user()->defaultPaymentMethod();
        return view('billing.index', compact('plans', 'currentPlan', 'paymentMethods', 'defaultPaymentMethod'));
    }

    public function cancel()
    {
        $this->user()->subscription('default')->cancel();
        return redirect()->route('billing');
    }

    public function resume()
    {
        $this->user()->subscription('default')->resume();
        return redirect()->route('billing');
    }
}
