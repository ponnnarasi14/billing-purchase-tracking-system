<!DOCTYPE html>
<html>
    <body>
        <h2></h2>
        <p>
            <strong>Invoice No: </strong>{{ $invoice->invoice_number }}<br>
            <strong>Date : </strong>{{ $invoice->created_at->format('d-m-Y') }}<br>
            <strong>Customer Name: </strong>{{ $invoice->customer->name }}<br>
            <strong>Customer email: </strong>{{ $invoice->customer->email }}<br>

        </p>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Tax</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->invoiceItems as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="right">{{ $item->product->quantity }}</td>
                    <td class="right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="right">{{ $item->tax_percentage  }}%</td>
                    <td class="right">{{ number_format($item->total_price + $item->total_tax, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <table>
            <tr>
                <td class="right"><strong>Sub Total</strong></td>
                <td class="right">{{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="right"><strong>Tax</strong></td>
                <td class="right">{{ number_format($invoice->total_tax, 2) }}</td>
            </tr>
            <tr>
                <td class="right"><strong>Grand Total</strong></td>
                <td class="right">{{ number_format($invoice->grand_total, 2) }}</td>
            </tr>
              <tr>
                <td class="right"><strong>Amount Paid</strong></td>
                <td class="right">{{ number_format($invoice->amount_paid, 2) }}</td>
            </tr>
            <tr>
                <td class="right"><strong>Balance Returned</strong></td>
                <td class="right">{{ number_format($invoice->balance_returned, 2) }}</td>
            </tr>
        </table>
        @if(!empty($invoice->balance_breakdown))
        <h4>Balance Denominations</h4>
        <ul>
            @foreach($invoice->balance_breakdown as $value => $count)
                <li>₹{{ $value }} x {{ $count }}</li>
            @endforeach
        </ul>
        @endif
    </body>
</html>