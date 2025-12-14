<?php

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function build()
    {
        $this->invoice->load('customer','invoiceItems.product');

        return $this->subject("Invoice {$this->invoice->invoice_number}")
                    ->view('emails.invoice')
                    ->with(['invoice' => $this->invoice])
                    ->attach(storage_path("app/invoices/{$this->invoice->invoice_number}.pdf"));
    }
}
