<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayslipRegular extends Model
{
    use HasFactory;
    // Disable automatic timestamps (created_at and updated_at)
    public $timestamps = false;

    // Specify the table if the model name does not follow Laravel's naming convention
    protected $table = 'payroll_regular';

    protected $primaryKey = 'id';
    // Define the fillable fields to prevent mass-assignment vulnerabilities
    protected $fillable = [
        "period",
        "name_id",
        "rate",
        "aca_pera",
        "subtotal",
        "gsis",
        "pagibig",
        "MP2",
        "PhilHealth",
        "wtax",
        "gsis_conso_loan",
        "gsis_policy_loan",
        "gsis_emergency_loan",
        "gsis_uoli1",
        "gsis_uoli2",
        "gsis_uoli_loan1",
        "gsis_uoli_loan_2",
        "gsis_housing_loan",
        "gsis_educational_loan",
        "gsis_computer_loan",
        "gsis_mpl",
        "gsis_gfal",
        "pagibig_housing_loan",
        "pagibig_mpl",
        "lbp_salary_loan",
        "cola_disallowance",
        "praise_disallowance",
        "enrp_mowel",
        "ucpb_salary_loan",
        "mgbea10",
        "total_deductions",
        "total",
        "first_cutoff",
        "second_cutoff",
        "certify",
        "certiby",
        "certipos",
        "dtrs_id"
      ];
}
