<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Denomination;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DenominationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Denomination::truncate();

        $denoms =[
            ['value'=>2000, 'available_notes' =>50],
            ['value'=>500,'available_notes'=>100],
            ['value'=>200,'available_notes'=>50],
            ['value'=>100,'available_notes'=>100],
            ['value'=>50,'available_notes'=>50],
            ['value'=>20,'available_notes'=>100],
            ['value'=>10,'available_notes'=>200],
        ];
        foreach($denoms as $denom)
        {
             Denomination::create($denom);
        }
    }
}
