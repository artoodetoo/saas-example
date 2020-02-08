<?php

namespace App\Http\Controllers;

use App\Plan;
use App\Services\InvoicesService;
use App\Payment;

class BillingController extends Controller
{
    public function index()
    {
        $payment = Payment::with('user')->find(5);
        return (new InvoicesService())->generateInvoice($payment);

        $plans = Plan::all();
        $currentPlan = $this->user()->subscription('default') ?? null;

        $paymentMethods = NULL;
        $defaultPaymentMethod = NULL;
        if (!is_null($currentPlan)) {
            $paymentMethods = $this->user()->paymentMethods();
            $defaultPaymentMethod = $this->user()->defaultPaymentMethod();
        }

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
