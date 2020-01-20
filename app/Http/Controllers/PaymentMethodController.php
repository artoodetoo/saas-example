<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $intent = $this->user()->createSetupIntent();
        return view('payment-methods.create', compact('intent'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->user()->addPaymentMethod($request->payment_method);
            if ($request->default == 1) {
                $this->user()->updateDefaultPaymentMethod($request->payment_method);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['stripe' => $e->getMessage()]);
        }
        return redirect()->route('billing')->withMessage('Payment method added successfully!');
    }

    /**
     * Mark payment method as default.
     *
     * @param  string  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function markDefault($paymentMethod)
    {
        try {
            $this->user()->updateDefaultPaymentMethod($paymentMethod);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['stripe' => $e->getMessage()]);
        }
        return redirect()->route('billing')->withMessage('Payment method updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
