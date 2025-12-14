<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denomination extends Model
{
    use HasFactory;

    protected $fillable = ['value', 'available_notes'];

    protected $casts = ['value' => 'integer', 'available_notes' => 'integer'];
    
}
