<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelClearance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_id',
        'travel_order_id',
        'destination',
        'purpose',
        'departure',
        'arrival',
        'pap_code',
        'basis_of_approval',
        'remarks',
        'reviewed_by',
        'clearance_number',
        'signature',
    ];

    protected $table = 'travel_clearances';
}
