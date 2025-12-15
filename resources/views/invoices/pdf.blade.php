<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            padding: 15px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        .invoice-info {
            margin-bottom: 15px;
        }

        .invoice-info strong {
            display: inline-block;
            width: 140px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            font-size: 12px;
        }

        table th {
            background-color: #f2f2f2;
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .summary-table {
            width: 40%;
            float: right;
            margin-top: 10px;
        }

        .summary-table td {
            border: 1px solid #ccc;
            padding: 6px;
        }

        .summary-table strong {
            font-weight: bold;
        }

        .clear {
            clear: both;
        }

        .denomination {
            margin-top: 20px;
        }

        .denomination p {
            margin: 3px 0;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 11px;
            color: #777;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>INVOICE</h2>

    <div class="invoice-info">
        <p>
            <strong>Invoice No:</strong> {{ $invoice->invoice_number }}<br>
            <strong>Date:</strong> {{ $invoice->created_at->format('d-m-Y') }}<br>
            <strong>Customer Name:</strong> {{ $invoice->customer?->name }}<br>
            <strong>Customer Email:</strong> {{ $invoice->customer?->email }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th class="right">Qty</th>
                <th class="right">Price</th>
                <th class="right">Tax</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->invoiceItems as $item)
                <tr>
                    <td>{{ $item->product?->name ?? 'N/A' }}</td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td class="right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="right">{{ $item->tax_percentage }}%</td>
                    <td class="right">
                        {{ number_format($item->total_price + $item->total_tax, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td><strong>Sub Total</strong></td>
            <td class="right">{{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Tax</strong></td>
            <td class="right">{{ number_format($invoice->total_tax, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Grand Total</strong></td>
            <td class="right">{{ number_format($invoice->grand_total, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Amount Paid</strong></td>
            <td class="right">{{ number_format($invoice->amount_paid, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Balance Returned</strong></td>
            <td class="right">{{ number_format($invoice->balance_returned, 2) }}</td>
        </tr>
    </table>

    <div class="clear"></div>

    @if(!empty($invoice->balance_breakdown))
        <div class="denomination">
            <h4>Balance Denominations</h4>
            @foreach ($invoice->balance_breakdown as $note => $count)
                <p>₹{{ $note }} × {{ $count }}</p>
            @endforeach
        </div>
    @endif

    <div class="footer">
        Thank you for your purchase!
    </div>

</div>

</body>
</html>
