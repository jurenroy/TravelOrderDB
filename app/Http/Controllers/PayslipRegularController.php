<?php

namespace App\Http\Controllers;

use App\Models\PayslipRegular;
use App\Models\Dtr;
use App\Models\Employee;
use App\Models\Salary;
use Carbon\Carbon;

use Illuminate\Http\Request;

class PayslipRegularController extends Controller
{
    public function index()
    {
        return response()->json(PayslipRegular::all());
    }
    public function store(Request $request)
    {
        $data = $request->all();

        // Check if the request is multiple records
        if (isset($data[0])) {

            PayslipRegular::insert($data);

            return response()->json([
                'message' => 'Bulk payslip Regular inserted successfully',
                'count' => count($data)
            ]);
        }

        // Single insert fallback
        $payslip = PayslipRegular::create($data);

        return response()->json([
            'message' => 'Payslip Regular inserted successfully',
            'data' => $payslip
        ]);
    }

    public function show($id)
    {
        $payslip = PayslipRegular::findOrFail($id);
        return response()->json($payslip);
    }

    // public function update(Request $request, $id)
    // {
    //     $payslip = PayslipRegular::findOrFail($id);
    //     $payslip->update($request->all());

    //     return response()->json($payslip);
    // }

    public function save(Request $request)
    {
        $data = $request->all();

        // Bulk update
        if (isset($data[0]) && is_array($data[0])) {
            $updatedCount = 0;
            $failedCount = 0;

            foreach ($data as $record) {
                $payslip = PayslipRegular::find($record['id']);
                if ($payslip) {
                    $payslip->update($record);
                    $updatedCount++;
                } else {
                    $failedCount++;
                }
            }

            return response()->json([
                'message' => 'Bulk save processed successfully',
                'updated' => $updatedCount,
                'failed' => $failedCount
            ]);
        }

        // Single record save
        $payslip = PayslipRegular::findOrFail($request->id);
        $payslip->update($request->all());

        return response()->json([
            'message' => 'Payslip saved successfully',
            'data' => $payslip
        ]);
    }

    public function destroy($id)
    {
        $payslip = PayslipRegular::findOrFail($id);
        $payslip->delete();

        return response()->json(['message' => 'Payslip Regular deleted successfully']);
    }

    public function getByPeriod($period)
{
    return response()->json(
        PayslipRegular::where('period', $period)->get()
    );
}

public function getByDtrs($dtrs_id)
{
    return response()->json(
        PayslipRegular::where('dtrs_id', $dtrs_id)->get()
    );
}

public function getByName($name_id)
{
    return response()->json(
        PayslipRegular::where('name_id', $name_id)->get()
    );
}

public function generateBatchPayslips()
{
    $employees = Employee::where('status', 1)->get();

    // Step 2: Loop through each employee
    foreach ($employees as $employee) {
        $dtrs = Dtr::where('name_id', $employee->name_id)->get(); // get all DTRs
    
        foreach ($dtrs as $dtr) {
            $existingPayslip = PayslipRegular::where('name_id', $employee->name_id)
                                         ->where('dtrs_id', $dtr->id)
                                         ->first();
            if (!$existingPayslip) {
                echo "Generating payslip for DTR ID: {$dtr->id}\n";
                $this->generatePayslip($dtr);
            } else {
                echo "Payslip already exists for DTR ID: {$dtr->id}\n";
            }
        }
    }

    return response()->json([
        'message' => 'Regular payslips generated successfully'
    ]);
}


public function generatePayslip(Dtr $dtrs)
{

    $startDate = Carbon::parse($dtrs->start_date);
    $endDate = Carbon::parse($dtrs->end_date);

    $period = $startDate->format('F j') . '-' . $endDate->format('j, Y');

    $employee = Employee::where('name_id', $dtrs->name_id)->first();

    if (!$employee) {
        return;
    }

    $salary = Salary::where('salary_id', $employee->salary_id)->first();
    
    $rate = $salary ? $salary->amount : 500;    

    $aca_pera = 2000;
    $mgbea10 = 250;

    $subtotal = $rate + $aca_pera;

    // Default deductions
    $gsis = 0;
    $pagibig = 0;
    $mp2 = 0;
    $philhealth = 0;
    $wtax = 0;

    $gsis_conso_loan = 0;
    $gsis_policy_loan = 0;
    $gsis_emergency_loan = 0;
    $gsis_uoli1 = 0;
    $gsis_uoli2 = 0;
    $gsis_uoli_loan1 = 0;
    $gsis_uoli_loan_2 = 0;
    $gsis_housing_loan = 0;
    $gsis_educational_loan = 0;
    $gsis_computer_loan = 0;
    $gsis_mpl = 0;
    $gsis_gfal = 0;

    $pagibig_housing_loan = 0;
    $pagibig_mpl = 0;

    $lbp_salary_loan = 0;
    $cola_disallowance = 0;
    $praise_disallowance = 0;
    $enrp_mowel = 0;
    $ucpb_salary_loan = 0;

    $total_deductions =
        $gsis +
        $pagibig +
        $mp2 +
        $philhealth +
        $wtax +
        $gsis_conso_loan +
        $gsis_policy_loan +
        $gsis_emergency_loan +
        $gsis_uoli1 +
        $gsis_uoli2 +
        $gsis_uoli_loan1 +
        $gsis_uoli_loan_2 +
        $gsis_housing_loan +
        $gsis_educational_loan +
        $gsis_computer_loan +
        $gsis_mpl +
        $gsis_gfal +
        $pagibig_housing_loan +
        $pagibig_mpl +
        $lbp_salary_loan +
        $cola_disallowance +
        $praise_disallowance +
        $enrp_mowel +
        $ucpb_salary_loan +
        $mgbea10;

    $total = $subtotal - $total_deductions;

    $first_cutoff = $total / 2;
    $second_cutoff = $total / 2;

    $certify = 23;
    $certiby = "Administrative Officer IV";
    $certipos = "OIC, Finance Section";


    PayslipRegular::create([

        'period' => $period,
        'name_id' => $employee->name_id,
        'rate' => $rate,

        'aca_pera' => $aca_pera,
        'subtotal' => $subtotal,

        'gsis' => $gsis,
        'pagibig' => $pagibig,
        'MP2' => $mp2,
        'PhilHealth' => $philhealth,
        'wtax' => $wtax,

        'gsis_conso_loan' => $gsis_conso_loan,
        'gsis_policy_loan' => $gsis_policy_loan,
        'gsis_emergency_loan' => $gsis_emergency_loan,
        'gsis_uoli1' => $gsis_uoli1,
        'gsis_uoli2' => $gsis_uoli2,
        'gsis_uoli_loan1' => $gsis_uoli_loan1,
        'gsis_uoli_loan_2' => $gsis_uoli_loan_2,
        'gsis_housing_loan' => $gsis_housing_loan,
        'gsis_educational_loan' => $gsis_educational_loan,
        'gsis_computer_loan' => $gsis_computer_loan,
        'gsis_mpl' => $gsis_mpl,
        'gsis_gfal' => $gsis_gfal,

        'pagibig_housing_loan' => $pagibig_housing_loan,
        'pagibig_mpl' => $pagibig_mpl,

        'lbp_salary_loan' => $lbp_salary_loan,
        'cola_disallowance' => $cola_disallowance,
        'praise_disallowance' => $praise_disallowance,
        'enrp_mowel' => $enrp_mowel,
        'ucpb_salary_loan' => $ucpb_salary_loan,

        'mgbea10' => $mgbea10,

        'total_deductions' => $total_deductions,
        'total' => $total,

        'first_cutoff' => $first_cutoff,
        'second_cutoff' => $second_cutoff,

        'certify' => $certify,
        'certiby' => $certiby,
        'certipos' => $certipos,

        'dtrs_id' => $dtrs->id
    ]);
}
}