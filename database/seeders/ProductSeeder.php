<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::truncate();

        $products = [
            ['name' =>'Colgate Toothpaste', 'product_code' =>'CGTHPST', 'price'=>50, 'stock'=>250, 'tax_percentage'=>8],
            ['name' =>'Milk 500g', 'product_code' =>'MK500', 'price'=>25, 'stock'=>50, 'tax_percentage'=>12],
            ['name' =>'Bread', 'product_code' =>'BREAD', 'price'=>50, 'stock'=>50, 'tax_percentage'=>5],
            ['name' =>'Apple 1kg', 'product_code' =>'APPLE1', 'price'=>120, 'stock'=>100, 'tax_percentage'=>0],
        ];

        foreach ($products as $product) 
        {
            Product::create($product);
        }
    }
}
