<?php

namespace App\Services;
use App\Models\Denomination;
use Illuminate\Support\Facades\DB;

class DenominationService
{
    // deduction will be done in transaction.
    public function computeChange(int $changeAmount): array
    {
        $breakdown = [];

        if ($changeAmount <= 0) 
        {
            return 
            [
                'breakdown' => $breakdown, 
                'remaining' => 0
            ];
        }

        // rows must be locked in calling transaction.
        $remaining = $changeAmount;

        // Get denominations in descending order (e.g. 500, 200, 100...)
        $denominations  = Denomination::orderBy('value', 'desc')->get();
        

        foreach ($denominations as $denomination) 
        {
            if ($remaining <= 0) {
                break;
            }

            $requiredNotes = intdiv($remaining, $denomination->value);

            if ($requiredNotes <= 0) {
                continue;
            }

            $usableNotes = min($requiredNotes, $denomination->available_notes);

            if ($usableNotes > 0) {
                $breakdown[$denomination->value] = $usableNotes;
                $remaining -= ($usableNotes * $denomination->value);
            }
        }

        return 
        [
            'breakdown' => $breakdown ?? [], 
            'remaining' => $remaining ?? 0,
        ];
    }

    
    //Must be called ONLY after successful invoice creation,inside the same DB transaction.
    public function deductNotes(array $breakdown): void
    {
        //Deduct denomination notes from database.
        foreach ($breakdown as $value => $count) 
        {
            Denomination::where('value', $value)->decrement('available_notes', $count);
        }
    }
}