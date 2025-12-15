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
    <h2>Customer Billing</h2>
    <form id="billingForm">

        <h3>Customer Details</h3>
        Customer Email : <input type="email" name="email" placeholder="Customer Email" required>
        Customer Name : <input type="text" name="name" placeholder="Customer Name">

        <h3>Bill Section</h3>
        <table id="itemsTable">
            <tbody>
            <tr>
                <td>
                    <select class="product"></select>
                </td>
                <td>
                    <input type="number" class="quantity" value="1" min="1">
                </td>
            </tr>
            </tbody>
        </table>

        <button type="button" onclick="addRow()">+ Add Item</button>

        <h3>Amount Paid By Customer</h3>
        <input type="number" name="amount_paid" placeholder="Amount Paid" required>

        <br><br>
        <button type="submit">Cancel</button>
        <button type="submit">Generate Invoice</button>
    </form>
    <div id="invoiceResult"></div>

<script>
    const token = document.querySelector('meta[name="csrf-token"]').content;

    /* Load products */
    let productList = [];

    function loadProducts() {
        fetch('/products')
            .then(res => res.json())
            .then(products => {
                productList = products;

                document.querySelectorAll('.product').forEach(select => {
                    fillProductOptions(select);
                });
            });
    }

    function fillProductOptions(select) {
        select.innerHTML = `<option value="">Select Product</option>`;

        productList.forEach(p => {
            select.innerHTML += `
                <option value="${p.id}">
                    ${p.name} - ₹${p.price}
                </option>
            `;
        });
    }

    /* Add product row */
    function addRow() {
        const table = document.querySelector('#itemsTable tbody');

        // RESET first row correctly
        const firstProduct = table.querySelector('tr:first-child .product');
        if (firstProduct) {
            firstProduct.value = ""; // reset safely
        }

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select class="product"></select>
            </td>
            <td>
                <input type="number" class="quantity" value="1" min="1">
            </td>
            <td>
                <button type="button" onclick="removeRow(this)">Remove</button>
            </td>
        `;

        table.appendChild(row);

        fillProductOptions(row.querySelector('.product'));
    }


    function removeRow(btn) {
        btn.closest('tr').remove();
    }

    /* Auto-fill customer name by email */
    document.querySelector('input[name="email"]').addEventListener('blur', function () {
        if (!this.value) return;

        fetch(`/customer/by-email?email=${this.value}`)
            .then(res => res.json())
            .then(customer => {
                if (customer) {
                    document.querySelector('input[name="name"]').value = customer.name;
                }
            });
    });

    /* Submit billing form */
    document.getElementById('billingForm').addEventListener('submit', function(e) {
        e.preventDefault();

        let items = [];
        document.querySelectorAll('.product').forEach((p, i) => {
            items.push({
                product_id: p.value,
                quantity: document.querySelectorAll('.quantity')[i].value
            });
        });

        fetch('/invoice', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                email: this.email.value,
                name: this.name.value,
                amount_paid: this.amount_paid.value,
                items: items
            })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert(data.message);
                return;
            }

            const invoice = data.invoice;

            let rows = '';
            invoice.invoice_items.forEach(item => {
                rows += `
                    <tr>
                        <td>${item.product_id}</td>
                        <td>${item.product.name}</td>
                        <td>₹${item.unit_price}</td>
                        <td>${item.quantity}</td>
                        <td>₹${item.unit_price * item.quantity}</td>
                        <td>${item.tax_percentage}%</td>
                        <td>₹${item.total_tax}</td>
                        <td>₹${(parseFloat(item.total_price) + parseFloat(item.total_tax)).toFixed(2)}</td>
                    </tr>
                `;
            });

            document.getElementById('invoiceResult').innerHTML = `
                <h3>Invoice Details</h3>

                <p><b>Invoice No:</b> ${invoice.invoice_number}</p>
                <p><b>Customer Name:</b> ${invoice.customer.name}</p>
                <p><b>Email:</b> ${invoice.customer.email}</p>

                <table border="1" cellpadding="6" cellspacing="0" width="100%">
                    <tr>
                        <th>Product Id</th>
                        <th>Product</th>
                        <th>unit Price</th>
                        <th>Qty</th>
                        <th>Purchase Price</th>
                        <th>Tax % for item</th>
                        <th>Tax payable for item</th>
                        <th>Total Price for the item</th>
                    </tr>
                    ${rows}
                </table>

                <br>
                <div style="width: 45%; margin-left: auto; text-align: right;">

                    <p>Total Price without Tax: ₹${invoice.total_amount}</p>
                    <p>Total Tax Payable: ₹${invoice.total_tax}</p>

                    <p><b>Net Price for Purchased Item: ₹${invoice.grand_total}</b></p>

                    <p>
                        Round down value for purchased item net price:
                        ₹${Math.floor(invoice.grand_total)}
                    </p>

                    <p>Amount Paid By Customer: ₹${invoice.amount_paid}</p>

                    <p>
                        <b>Balance payable to the customer:
                        ₹${invoice.balance_returned}</b>
                    </p>

                    <h4>Balance Denomination</h4>
                    <ul style="list-style: none; padding: 0;">
                        ${Object.entries(invoice.balance_breakdown)
                            .map(([note, count]) => `<li>₹${note} x ${count}</li>`)
                            .join('')}
                    </ul>

                </div>
            `;
        });

    });

    loadProducts();
</script>

</body>
</html>
