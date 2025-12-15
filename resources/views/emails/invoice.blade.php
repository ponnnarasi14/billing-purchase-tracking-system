<p>{{ $invoice->customer?->name }}</p>
<p>Invoice Details : {{ $invoice->invoice_number }}, Total : ₹{{ number_format($invoice->grand_total,2)}}</p>