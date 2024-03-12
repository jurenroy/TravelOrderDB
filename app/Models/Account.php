<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{

    use HasFactory;

    protected $primaryKey = 'account_id';
    
    protected $fillable = [
        
        'type_id',
        'name_id',
        'email',
        'password',
        'signature'
    ];

    protected $table = 'accounts'; // Specify the actual table name

    public $timestamps = false; // Disable timestamps

}
