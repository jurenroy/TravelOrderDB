<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DtrDays extends Model
{
    use HasFactory;
    // Disable automatic timestamps (created_at and updated_at)
    public $timestamps = false;

    // Specify the table if the model name does not follow Laravel's naming convention
    protected $table = 'dtr_days';

    protected $primaryKey = 'id';
    // Define the fillable fields to prevent mass-assignment vulnerabilities
    protected $fillable = [
        'name_id',
        'date',
        'timestamp',
        'status',
        'dtrs_id'
    ];
}
