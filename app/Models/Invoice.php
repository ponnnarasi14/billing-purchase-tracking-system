<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number', 
        'customer_id', 
        'total_amount', 
        'total_tax', 
        'grand_total', 
        'amount_paid', 
        'balance_returned', 
        'balance_breakdown'
    ];

    protected $casts = [

        'balance_breakdown' => 'array',
        'total_amount' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_returned' => 'decimal:2',
        'balance_breakdown' => 'array'
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class); //invoice belongs to one customer
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);//invoice belongs to many items
    }
}
