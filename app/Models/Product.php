<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'product_code', 'price', 'stock', 'tax_percentage'];

    protected $casts = ['price' => 'decimal:2', 'tax_percentage' => 'decimal:2'];

    public function invoiceItems() 
    {
        return $this->hasMany(InvoiceITem::class); //one product apper in many invoice items
    }
}
