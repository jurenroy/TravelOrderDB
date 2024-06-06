<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveForm extends Model
{

    use HasFactory;

    protected $primaryKey = 'leaveform_id';
    
    protected $fillable = [
        "name_id",
        "position_id",
        "type",
        "detail",
        "description",
        "days" ,
        "dates" ,
        "commutation",
        "applicant" ,
        "asof",
        "tevl" ,
        "tesl" ,
        "ltavl" ,
        "ltasl" ,
        "bvl" ,
        "vsl" ,
        "certification",
        "reco",
        "recodesc",
        "recommendation",
        "dayswpay" ,
        "dayswopay" ,
        "others" ,
        "disapproved",
        "approval"
        // 'date' is not included as it's set to auto-populate
    ];

    protected $table = 'leaveform'; // Specify the actual table name

    public $timestamps = false; // Disable timestamps

}
