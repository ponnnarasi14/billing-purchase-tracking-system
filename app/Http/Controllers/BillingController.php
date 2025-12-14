<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use PHPUnit\Framework\MockObject\Invocation;

class BillingController extends Controller
{
    protected InvoiceService $invoiceService ;

    public function __construct(InvoiceService $invoiceService )
    {
        $this->invoiceService  = $invoiceService ;
    }

    public function create()
    {
       
        $products = Product::with('invoiceItems')->orderBy('name')->get();
       
        return view('billing.create', compact('products'));
    }

    public function generateInvoice(Request $request)
    {
        $data =$request->validate([
            'name' => 'nullable|string',
            'email' => 'required|email',
            'amount_paid' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:produts,id',
            'items.*.quantity' => 'required|integer|min:1',

        ]);

        try{
            $invoice = $this->invoiceService->createInvoice($data);
            return redirect()->route('invoice.show', $invoice->id)->with('sucess', 'Invoice Generated');
        }catch(\Exception $e){
            return back()->withErrors($e->getMessage())->withInput();
        }
    }
}
