<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rso extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'rso_number';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'rso_number',
        'rso_name',             // ✅ Added
        'rso_date',
        'rso_subject',
        'rso_scheduled_dates_from',
        'rso_scheduled_dates_to',
        'rso_signatory',
        'rso_remarks',
        'rso_scan_copy',
    ];

    public function signatory()
    {
        return $this->belongsTo(User::class, 'rso_signatory');
    }
}
