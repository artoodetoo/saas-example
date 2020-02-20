<?php

namespace App\Http\Controllers;

use App\Plan;
use App\Services\InvoicesService;
use App\Payment;

class BillingController extends Controller
{
    public function index()
    {
        $plans = Plan::all();
        $currentPlan = $this->user()->subscription('default') ?? null;

        $paymentMethods = NULL;
        $defaultPaymentMethod = NULL;
        if (!is_null($currentPlan)) {
            $paymentMethods = $this->user()->paymentMethods();
            $defaultPaymentMethod = $this->user()->defaultPaymentMethod();
        }

        $payments = Payment::where('user_id', $this->user()->id)->latest()->get();

        return view('billing.index', compact('plans', 'currentPlan', 'paymentMethods', 'defaultPaymentMethod', 'payments'));
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

    public function downloadInvoice($paymentId)
    {
        $payment = Payment::where('user_id', $this->user()->id)->where('id', $paymentId)->firstOrFail();
        $filename = storage_path('app/invoices/' . $payment->id .'.pdf');
        if (!file_exists($filename)) {
            abort(404);
        }
        return response()->download($filename);
    }
}
