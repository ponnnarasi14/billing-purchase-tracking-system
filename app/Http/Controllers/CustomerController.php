<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function findByEmail(Request $request)
    {
        return Customer::where('email', $request->email)->first();
    }

    public function purchaseHistory($id)
    {
        $customer = Customer::with('invoices')->findOrFail($id);
        return view('customers.purchase', compact('customer'));
    }
        
}
