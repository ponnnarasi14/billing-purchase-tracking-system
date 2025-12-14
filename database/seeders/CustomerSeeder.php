<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::truncate();
        
        $customers =[
            ['name' => 'Thomas D. Call', 'email' => 'thomas.d.call@example.com'],
            ['name' => 'Antony', 'email' => 'antony@example.com'],
            ['name' => 'divya', 'email' => 'divya@example.com'],
        ];

        foreach($customers as $customer) 
        {    
            Customer::create($customer);
        }
    }
}
