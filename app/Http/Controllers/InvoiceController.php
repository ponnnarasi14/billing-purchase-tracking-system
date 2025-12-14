<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    protected InvoiceService  $invoiceService ;

    public function __construct(InvoiceService  $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function create()
    {
        return view('invoices.create');
    }

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'name'                  => 'nullable|string',
            'email'                 => 'required|email',
            'amount_paid'           => 'required|numeric|min:0',
            'items'                 => 'required|array|min:1',
            'items.*.product_id'    => 'required|exists:products,id',
            'items.*.quantity'      => 'required|integer|min:1',
        ]);

        try{
            $invoice = $this->invoiceService->createInvoice($validated);

         

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'invoice' => $invoice->load('invoiceItems.product', 'customer')
            ]);
        }
        catch(\Exception $e)
        {
             return response()->json([
                'success' => false,
                'message' => $e->getMessage()
             ], 422);
        }
    }

    public function show($id)
    {
        $invoice = Invoice::with(['invoiceItems.product', 'customer'])->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    }

    public function downloadPdf($id)
    {
        $invoice = Invoice::with(['invoiceItems.product', 'customer'])->findOrFail($id);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download(
            'invoice-' . $invoice->invoice_number . '.pdf'
        );
    }
}

