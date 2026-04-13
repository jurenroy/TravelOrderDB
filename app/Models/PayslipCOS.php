<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayslipCOS extends Model
{
    use HasFactory;
    // Disable automatic timestamps (created_at and updated_at)
    public $timestamps = false;

    // Specify the table if the model name does not follow Laravel's naming convention
    protected $table = 'payroll_cos';

    protected $primaryKey = 'id';
    // Define the fillable fields to prevent mass-assignment vulnerabilities
    protected $fillable = [
        "period",
        "name_id",
        "days",
        "rate",
        "premium_rate",
        "premium",
        "rate_total",
        "premium_total",
        "additional",
        "subtotal",
        "overpayment",
        "penalty",
        "pass_slip",
        "tax",
        "PhilHealth",
        "PhilHealthDiff",
        "Pagibig",
        "MP2",
        "MPL",
        "MGB_coop_loan",
        "total_deductions",
        "total",
        "certify",
        "certiby",
        "certipos",
        "dtrs_id"
    ];
}
