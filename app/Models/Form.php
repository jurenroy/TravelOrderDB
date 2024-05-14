<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{

    use HasFactory;

    protected $primaryKey = 'travel_order_id';
    
    protected $fillable = [
        'name_id',
        'position_id',
        'division_id',
        'station',
        'destination',
        'purpose',
        'departure',
        'arrival',
        'signature1',
        'signature2',
        'pdea',
        'ala',
        'appropriations',
        'remarks',
        'note',
        'sname',
        'sdiv',
        'to_num',
        'initial'

        // 'date' is not included as it's set to auto-populate
    ];

    protected $table = 'form'; // Specify the actual table name

    public $timestamps = false; // Disable timestamps

    

}
