<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public Invoice $invoice;
    public string $pdf;

    // invoice + pdf
    public function __construct(Invoice $invoice, string $pdf)
    {
        $this->invoice = $invoice;
        $this->pdf = $pdf;
    }

    public function build()
    {
        return $this->subject('Invoice ' . $this->invoice->invoice_number)
            ->view('emails.invoice')
            ->with([
                'invoice' => $this->invoice
            ])
            ->attachData(
                $this->pdf,
                $this->invoice->invoice_number . '.pdf',
                [
                    'mime' => 'application/pdf',
                ]
            );
    }
}
