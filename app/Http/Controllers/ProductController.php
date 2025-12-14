<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //listout available prodcut
    public function index()
    {
        return response()->json(
            Product::select('id', 'name', 'price', 'stock')->where('stock', '>', 0)->orderBy('name')->get()
        );
    }
}
