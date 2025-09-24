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
        'ictnote',
        'files'
    ];

    // Automatically cast 'files' to an array when retrieved from the database
    protected $casts = [
        'files' => 'array',  // Laravel will handle the conversion from JSON to array
    ];

    // You can also enable timestamps if necessary
    // public $timestamps = true;

    // Optional: Ensure date is a Carbon instance (if you want to work with date-specific methods)
    protected $dates = ['date'];
}