<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dtr extends Model
{
    use HasFactory;

    protected $table = 'dtrs';

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'name_id',
        'start_date',
        'end_date',
    ];

    public $timestamps = false; // Disable timestamps
}
