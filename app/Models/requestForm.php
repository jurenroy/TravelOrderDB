<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestForm extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'name_id',
        'division_id',
        'date',
        'documents',
        'rating',
        'remarks',
        'note',
    ];

    protected $table = 'request_form';

}
