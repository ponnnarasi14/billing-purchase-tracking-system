<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = 
    [
        'invoice_id', 
        'product_id',
        'quantity',
        'unit_price',
        'tax_percentage',
        'total_price',
        'total_tax'

    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class); //item belongs to one invoice
    }

    public function product()
    {
        return $this->belongsTo(Product::class); //item refer to one product
    }
}
