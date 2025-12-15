<?php
namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\DenominationService;

use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    protected DenominationService $denominationService;

    public function __construct(DenominationService $denominationService)
    {
        $this->denominationService = $denominationService;
    }

   
    public function createInvoice(array $payload): Invoice
    {
        return DB::transaction(function() use ($payload) 
        {
            // find or create customer by email
            $customer = Customer::firstOrCreate(
                ['email' => $payload['email']],
                ['name' => $payload['name'] ?? null]
            );

            //calculate items
            $subTotal = 0;
            $totalTax = 0;
            $preparedItems = [];

            //product calculation
            foreach ($payload['items'] as $item) {

                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception(
                        "Insufficient stock for product: {$product->name}"
                    );
                }

                $lineAmount = $product->price * $item['quantity'];
                $lineTax = ($lineAmount * $product->tax_percentage) / 100;

                $itemsData[] = [
                    'product'        => $product,
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $product->price,
                    'tax_percentage' => $product->tax_percentage,
                    'line_amount'    => $lineAmount,
                    'line_tax'       => $lineTax,
                ];

                $subTotal += $lineAmount;
                $totalTax += $lineTax;
            }

            //payment validation
            $grandTotal = round($subTotal + $totalTax, 2);
            $amountPaid = round($payload['amount_paid'], 2);

            if ($amountPaid < $grandTotal) {
                throw new \Exception('Paid amount is less than invoice total');
            }

            $changeAmount = (int) floor($amountPaid - $grandTotal);

            //create invoice
            $invoice = Invoice::create([
                'invoice_number'   => $this->generateInvoiceNumber(),
                'customer_id'      => $customer->id,
                'total_amount'     => $subTotal,
                'total_tax'        => $totalTax,
                'grand_total'      => $grandTotal,
                'amount_paid'      => $amountPaid,
                'balance_returned' => 0,
                'balance_breakdown'=> [],
            ]);

            //save item reduce stock
            foreach ($itemsData as $row) {

                InvoiceItem::create([
                    'invoice_id'     => $invoice->id,
                    'product_id'     => $row['product']->id,
                    'quantity'       => $row['quantity'],
                    'unit_price'     => $row['unit_price'],
                    'tax_percentage' => $row['tax_percentage'],
                    'total_price'    => $row['line_amount'],
                    'total_tax'      => $row['line_tax'],
                ]);

                $row['product']->decrement('stock', $row['quantity']);
            }

            //denomination handling lock cash
            DB::table('denominations')->lockForUpdate()->get();

            $changeResult = $this->denominationService->computeChange($changeAmount);

            $breakdown = $changeResult['breakdown'] ?? [];

            if (!is_array($breakdown)) {
                throw new \Exception('Unable to return change with available denominations');
            }

            $returnedAmount = 0;
            foreach ($breakdown as $note => $count) {
                $returnedAmount += ($note * $count);
            }

            $invoice->update([
                'balance_returned'  => $returnedAmount,
                'balance_breakdown' => $breakdown,
            ]);

            if (!empty($breakdown)) {
                $this->denominationService->deductNotes($breakdown);
            }

            //load relation
            $invoice->load('customer', 'invoiceItems.product');

            //send mail after commit
            DB::afterCommit(function () use ($invoice) {

                // reload invoice safely with relations
                $invoice = Invoice::with(['customer', 'invoiceItems.product'])
                    ->find($invoice->id);

                if (!$invoice || !$invoice->customer?->email) {
                    return;
                }

                // generate pdf
                $pdf = Pdf::loadView('invoices.pdf', [
                    'invoice' => $invoice
                ]);

                // send mail with pdf attached
                Mail::to($invoice->customer->email)
                    ->send(new InvoiceMail($invoice, $pdf->output()));
            });

            return $invoice;
        });
    }
   
    //Generate invoice number
    private function generateInvoiceNumber(): string
    {
        return 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
