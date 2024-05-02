<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Name extends Model
{
    use HasFactory;
    protected $table = 'name';

    protected $primaryKey = 'name_id';
    
    protected $fillable = [
        'first_name',
        'middle_init',
        'last_name',
    ];

    public $timestamps = false; // Disable timestamps
}
