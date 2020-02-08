<?php

namespace App\Services;

use App\Payment;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;

class InvoicesService
{
    public function generateInvoice(Payment $payment)
    {
        $customer = new Buyer([
            'name'          => $payment->user->name,
            'custom_fields' => [
                'email' => $payment->user->email,
            ],
        ]);

        $item = (new InvoiceItem())
            ->title('Subscription fee')
            ->pricePerUnit(cents($payment->total));

        $invoice = Invoice::make()
            ->buyer($customer)
            ->filename('invoices/' . $payment->id)
            ->addItem($item);

        return $invoice->save();
    }
}
