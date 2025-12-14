<!DOCTYPE html>
<html>
    <head>
        <title>Customer Billing</title>
    </head>
    <body>
     
            <div class="container">
                <h2>Generate Bill</h2>
                @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
                <form method="POST" action="{{ route('billing.generate') }}"></form>
                @csrf
                <div>
                    <label>Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                    <button type="button" id="fetchCustomer">Fetch</button>
                </div>
                <div>
                    <label>Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}">
                </div>
                <div id="items-wrapper">
                    <template id="row-template">
                        <div class="row-item">
                            <select name="items[][product_id]" class="product-select">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-tax="{{ $product->tax_percentage }}">{{ $product->name }} - {{ $product->product_code }}</option>
                                @endforeach
                            </select>
                            <input type="number" name="items[][quantity]" value="1" min="1" class="qty">
                            <span class="line-total">0.00</span>
                            <button type="button" class="remove-row">Remove</button>
                        </div>
                    </template>
                </div>

                <button type="button" id="add-row">Add Product</button>

                <div>
                    <label>Amount Given</label>
                    <input type="number" name="amount_paid" step="0.01" id="amount_paid" value="{{ old('amount_paid',0) }}" required>
                </div>

                <div>
                    <button type="submit">Generate Bill</button>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                const wrapper = document.getElementById('items-wrapper');
                const template = document.getElementById('row-template').content;
                function addRow(){
                    const node = document.importNode(template, true);
                    wrapper.appendChild(node);
                    recalc();
                }
                document.getElementById('add-row').addEventListener('click', addRow);
                addRow();

                wrapper.addEventListener('click', function(e){
                    if (e.target.classList.contains('remove-row')){
                        e.target.closest('.row-item').remove();
                        recalc();
                    }
                });
                wrapper.addEventListener('change', function(e){
                    recalc();
                });
                function recalc(){
                    // client-side simple totals; server will validate
                    document.querySelectorAll('.row-item').forEach(function(row){
                        const sel = row.querySelector('.product-select');
                        const qty = row.querySelector('.qty').value || 0;
                        const price = parseFloat(sel.selectedOptions[0].dataset.price || 0);
                        const total = (price * qty).toFixed(2);
                        row.querySelector('.line-total').textContent = total;
                    });
                }

                document.getElementById('fetchCustomer').addEventListener('click', function(){
                    const email = document.getElementById('email').value;
                    if (!email) return alert('Enter email');
                    fetch(`/customer/${encodeURIComponent(email)}`).then(r => r.json()).then(data => {
                        if (data && data.name) document.getElementById('name').value = data.name;
                        else alert('No customer found');
                    });
                });
            });
            </script>
       
    </body>
</html>