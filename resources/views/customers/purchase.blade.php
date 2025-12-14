<!DOCTYPE html>
<html>
<head>
    <title>Customer purchase</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        table { border-collapse: collapse; width: 100%; }
        table, th, td { border: 1px solid #ccc; padding: 6px; }
        .invoice-box { margin-top: 20px; border: 1px solid #000; padding: 10px; }
    </style>
</head>

<body>
    <h2>Purchase History for {{ $customer->name }}</h2>

    <table border="1">
        <tr>
            <th>Invoice ID</th>
            <th>Invoice Date</th>
            <th>Invoice Amount</th>
            <th>Action</th>
        </tr>
        @foreach($customer->invoices as $invoice)
        <tr>
            <td>{{ $invoice->id }}</td>
            <td>{{ $invoice->created_at->format('d-m-Y') }}</td>
            <td>{{ $invoice->grand_total }}</td>
            <td><a href="{{ route('invoice.show', $invoice->id) }}">View Details</a></td>
        </tr>
        @endforeach
    </table>
</body>
</html>