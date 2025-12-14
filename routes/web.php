<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/invoice/create');
});

Route::get('/products', [ProductController::class, 'index']);

Route::get('/invoice/create', [InvoiceController::class, 'create'])->name('invoice.create');
Route::post('/invoice', [InvoiceController::class, 'store'])->name('invoice.store');
Route::get('/invoice/{id}', [InvoiceController::class, 'show']) ->name('invoice.show');
Route::get('/invoice/{id}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoice.pdf');


// Route::get('/', function() { return redirect('/billing'); });
// Route::get('/billing', [BillingController::class, 'create'])->name('billing.create');
Route::post('/billing/generate', [BillingController::class, 'generateInvoice'])->name('billing.generate');

Route::get('/customer/by-email', [CustomerController::class,'findByEmail']);
Route::get('/customer/{id}', [CustomerController::class,'purchaseHistory']);

