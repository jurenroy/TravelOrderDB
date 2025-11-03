<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrderRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_id',
        'job_order_no',
        'date',
        'type',
        'brand',
        'serial',
        'property_no',
        'date_of_aquisition',
        'aquisition_cost',
        'date_of_last_repair',
        'nature_of_last_repair',
        'nature_and_scope',
        'parts',
        'requested_by',
        'performed_by',
        'noted_by',
        'date_finished',
        'remarks'
    ];
}
