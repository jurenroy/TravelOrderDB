<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $primaryKey = 'employee_id';
    
    protected $fillable = [
        'employee_id',
        'name_id',
        'position_id',
        'division_id',
        'chief',
        'rd',
    ];

    protected $table = 'employee';

    public $timestamps = false; // Disable timestamps
}
