<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $primaryKey = 'id'; // Specify the primary key if it's not 'id'

    protected $fillable = [
        'serviceRequestNo',
        'date',
        'division_id',
        'typeOfService',
        'note',
        'remarks',
        'requestedBy',
        'approvedBy',
        'servicedBy',
        'feedback_filled', // Add this line
    ];
}
