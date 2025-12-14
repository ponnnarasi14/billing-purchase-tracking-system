<!DOCTYPE html>
<html>
<head>
    <title>Customer Billing</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        table { border-collapse: collapse; width: 100%; }
        table, th, td { border: 1px solid #ccc; padding: 6px; }
        .invoice-box { margin-top: 20px; border: 1px solid #000; padding: 10px; }
    </style>
</head>

<body>
    <div class="container">
        <h2>Invoice : {{ $invoice->invoice_number }}</h2>
        <p>Customer Name : {{ $invoice->customer->name }} </p>
        <p>Customer Email : {{ $invoice->customer->email }}</p>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Tax</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->invoiceItems as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ number_format($item->product->unit_price,2) }}</td>
                    <td>{{ number_format($item->product->total_tax,2) }}</td>
                    <td>{{ number_format($item->product->total_price,2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="width: 45%; margin-left: auto; text-align: right;">
            <p>Total : ₹{{ number_format($invoice->total_amount,2) }}</p>
            <p>Tax : ₹{{ number_format($invoice->total_tax,2) }}</p>
            <p>Grand Total : ₹{{ number_format($invoice->grand_total,2) }}</p>
            <p>Amount Paid : ₹{{ number_format($invoice->amount_paid,2) }}</p>
            <p>Balance Returned : ₹{{ number_format($invoice->balance_returned,2) }}</p>
     
            @if($invoice->balance_breakdown)
            <h4>Balance Denomination</h4>
            <ul>
                @foreach($invoice->balance_breakdown as  $val=>$count)
                <li>₹ {{ $val }} * {{ $count }}</li>
                @endforeach
            </ul>
            @endif
        </div>

        <a href="{{ route('invoice.pdf', $invoice->id) }}" target="_blank">Download PDF</a>
    </div>
</body>
</html>